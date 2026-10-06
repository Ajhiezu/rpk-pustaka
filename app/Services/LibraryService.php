<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\ReturnBook;
use App\Models\Fine;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LibraryService
{
    /**
     * Create a loan reservation (physical) or active digital loan.
     *
     * @param array $data
     * @return Loan
     * @throws \DomainException
     */
    public function createLoan(array $data)
    {
        return DB::transaction(function () use ($data) {
            $loanType = $data['loan_type'] ?? 'physical';
            $defaultDuration = $loanType === 'digital'
                ? (int) Setting::get('digital_loan_duration_days', 7)
                : (int) Setting::get('physical_loan_duration_days', 14);
            $dueDate = isset($data['due_date']) ? Carbon::parse($data['due_date']) : now()->addDays($defaultDuration);
            $userId = $data['user_id'];
            $bookIds = (array) $data['book_ids'];

            // 1. Prevent duplicate active loan or reservation for the same book and type
            $existingActiveLoan = Loan::where('user_id', $userId)
                ->where('loan_type', $loanType)
                ->whereIn('status', ['pending', 'approved', 'borrowed'])
                ->whereHas('loanDetails', function ($q) use ($bookIds) {
                    $q->whereIn('book_id', $bookIds);
                })
                ->exists();

            if ($existingActiveLoan) {
                $formatName = $loanType === 'digital' ? 'versi digital' : 'buku fisik';
                throw new \DomainException("Anda masih memiliki reservasi atau peminjaman aktif untuk {$formatName} buku ini.");
            }

            // 2. Validate availability with row locking for concurrency protection
            $booksToLoan = [];
            foreach ($bookIds as $bookId) {
                if ($loanType === 'physical') {
                    // Lock the book row to prevent race conditions on remaining stock
                    $book = Book::where('id', $bookId)->lockForUpdate()->firstOrFail();
                    if ($book->available_stock <= 0) {
                        throw new \DomainException("Buku fisik '{$book->title}' tidak tersedia untuk dipesan saat ini.");
                    }
                    $booksToLoan[] = $book;
                } else {
                    // Digital loan: ensure PDF exists
                    $book = Book::findOrFail($bookId);
                    if (!$book->hasDigital()) {
                        throw new \DomainException("Versi digital untuk buku '{$book->title}' belum tersedia.");
                    }
                    $booksToLoan[] = $book;
                }
            }

            // 3. Determine Expiry Deadline and Unique Stable Loan Code
            $expiryHours = (int) (Setting::where('key', 'physical_reservation_expiry_hours')->value('value') ?? 24);
            $initialStatus = $data['status'] ?? ($loanType === 'physical' ? 'pending' : 'borrowed');
            $pickupDeadline = ($loanType === 'physical' && $initialStatus === 'pending') ? now()->addHours($expiryHours) : null;
            $approvedAt = in_array($initialStatus, ['approved', 'borrowed']) ? now() : null;
            $borrowedAt = $initialStatus === 'borrowed' ? now() : null;

            $datePrefix = date('Ymd');
            $todayCount = Loan::whereDate('created_at', now()->toDateString())->count() + 1;
            $loanCode = 'RPK-LOAN-' . $datePrefix . '-' . sprintf('%03d', $todayCount);

            // Ensure loan_code uniqueness
            while (Loan::where('loan_code', $loanCode)->exists()) {
                $todayCount++;
                $loanCode = 'RPK-LOAN-' . $datePrefix . '-' . sprintf('%03d', $todayCount);
            }

            // 4. Create Loan Record
            $loan = Loan::create([
                'user_id' => $userId,
                'loan_code' => $loanCode,
                'loan_type' => $loanType,
                'loan_date' => now(),
                'due_date' => $dueDate,
                'pickup_deadline' => $pickupDeadline,
                'approved_at' => $approvedAt,
                'borrowed_at' => $borrowedAt,
                'status' => $initialStatus,
                'total_books' => count($bookIds),
            ]);

            // 5. Attach Loan Details & decrement stock IMMEDIATELY upon physical reservation
            foreach ($booksToLoan as $book) {
                LoanDetail::create([
                    'loan_id' => $loan->id,
                    'book_id' => $book->id,
                ]);

                if ($loanType === 'physical') {
                    $book->decrement('available_stock');
                }
            }

            return $loan;
        });
    }

    /**
     * Admin approves a pending physical reservation.
     * Available stock DOES NOT CHANGE (already reserved).
     */
    public function approveReservation(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan) {
            $loanRecord = Loan::where('id', $loan->id)->lockForUpdate()->firstOrFail();

            if ($loanRecord->status !== 'pending') {
                throw new \DomainException("Hanya peminjaman berstatus 'Menunggu Persetujuan' yang dapat disetujui.", 422);
            }

            $loanRecord->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            return $loanRecord;
        });
    }

    /**
     * Admin marks approved reservation as handed over (borrowed).
     * Available stock DOES NOT CHANGE.
     */
    public function handoverLoan(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan) {
            $loanRecord = Loan::where('id', $loan->id)->lockForUpdate()->firstOrFail();

            if (!in_array($loanRecord->status, ['approved', 'pending'])) {
                throw new \DomainException("Transaksi tidak dalam status yang valid untuk penyerahan naskah fisik.", 422);
            }

            $loanRecord->update([
                'status' => 'borrowed',
                'approved_at' => $loanRecord->approved_at ?? now(),
                'borrowed_at' => now(),
            ]);

            return $loanRecord;
        });
    }

    /**
     * Admin rejects a pending reservation.
     * Restores available stock (+1 per book).
     */
    public function rejectReservation(Loan $loan, ?string $reason = null): Loan
    {
        return DB::transaction(function () use ($loan, $reason) {
            $loanRecord = Loan::where('id', $loan->id)->lockForUpdate()->firstOrFail();

            if ($loanRecord->status !== 'pending') {
                throw new \DomainException("Hanya reservasi berstatus 'Menunggu Persetujuan' yang dapat ditolak.", 422);
            }

            $loanRecord->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            if ($loanRecord->isPhysical()) {
                foreach ($loanRecord->loanDetails as $detail) {
                    $book = Book::where('id', $detail->book_id)->lockForUpdate()->first();
                    if ($book) {
                        $book->increment('available_stock');
                    }
                }
            }

            return $loanRecord;
        });
    }

    /**
     * Member cancels their own pending reservation.
     * Restores available stock (+1 per book).
     */
    public function cancelReservation(Loan $loan, ?int $userId = null): Loan
    {
        return DB::transaction(function () use ($loan, $userId) {
            $loanRecord = Loan::where('id', $loan->id)->lockForUpdate()->firstOrFail();

            if ($userId !== null && $loanRecord->user_id !== $userId) {
                throw new \DomainException("Anda tidak berhak membatalkan transaksi peminjaman ini.", 403);
            }

            if ($loanRecord->status !== 'pending') {
                throw new \DomainException("Hanya peminjaman berstatus 'Menunggu Persetujuan' yang dapat dibatalkan oleh anggota.", 422);
            }

            $loanRecord->update([
                'status' => 'cancelled',
            ]);

            if ($loanRecord->isPhysical()) {
                foreach ($loanRecord->loanDetails as $detail) {
                    $book = Book::where('id', $detail->book_id)->lockForUpdate()->first();
                    if ($book) {
                        $book->increment('available_stock');
                    }
                }
            }

            return $loanRecord;
        });
    }

    /**
     * Expire a single pending reservation past its pickup deadline.
     * Restores available stock (+1 per book). Idempotent.
     */
    public function expireReservation(Loan $loan): bool
    {
        return DB::transaction(function () use ($loan) {
            $loanRecord = Loan::where('id', $loan->id)->lockForUpdate()->firstOrFail();

            if ($loanRecord->status !== 'pending') {
                return false;
            }

            if ($loanRecord->pickup_deadline && now()->lt($loanRecord->pickup_deadline)) {
                return false;
            }

            $loanRecord->update([
                'status' => 'expired',
            ]);

            if ($loanRecord->isPhysical()) {
                foreach ($loanRecord->loanDetails as $detail) {
                    $book = Book::where('id', $detail->book_id)->lockForUpdate()->first();
                    if ($book) {
                        $book->increment('available_stock');
                    }
                }
            }

            return true;
        });
    }

    /**
     * Automatically expire all unclaimed physical reservations past deadline.
     */
    public function expireAllOverdueReservations(): int
    {
        $expiredCount = 0;
        $overdueLoans = Loan::where('status', 'pending')
            ->where('loan_type', 'physical')
            ->whereNotNull('pickup_deadline')
            ->where('pickup_deadline', '<', now())
            ->get();

        foreach ($overdueLoans as $loan) {
            if ($this->expireReservation($loan)) {
                $expiredCount++;
            }
        }

        return $expiredCount;
    }

    /**
     * Process return of a loan.
     * Prevents double return exploit and handles physical vs digital returns.
     *
     * @param Loan $loan
     * @param array $data
     * @return ReturnBook
     * @throws \DomainException
     */
    public function processReturn(Loan $loan, array $data)
    {
        return DB::transaction(function () use ($loan, $data) {
            $loanRecord = Loan::where('id', $loan->id)->lockForUpdate()->firstOrFail();

            if ($loanRecord->status === 'returned') {
                throw new \DomainException('Peminjaman ini sudah dikembalikan sebelumnya.');
            }

            if (!in_array($loanRecord->status, ['borrowed', 'overdue', 'approved'])) {
                throw new \DomainException('Transaksi tidak dalam status yang valid untuk diproses pengembaliannya.', 422);
            }

            $returnBook = ReturnBook::create([
                'loan_id' => $loanRecord->id,
                'return_date' => now(),
                'condition' => $data['condition'] ?? 'good',
                'notes' => $data['notes'] ?? null,
            ]);

            $loanRecord->update(['status' => 'returned']);

            // Increase physical stock ONLY if loan was physical and condition is good
            if ($loanRecord->isPhysical() && ($data['condition'] ?? 'good') === 'good') {
                foreach ($loanRecord->loanDetails as $detail) {
                    $book = Book::where('id', $detail->book_id)->lockForUpdate()->first();
                    if ($book) {
                        $book->increment('available_stock');
                    }
                }
            }

            // Calculate fines only for physical loans
            if ($loanRecord->isPhysical()) {
                $this->calculateFine($loanRecord, $returnBook);
            }

            return $returnBook;
        });
    }

    /**
     * Calculate late or damaged/lost fines for physical loans.
     */
    protected function calculateFine(Loan $loan, ReturnBook $returnBook)
    {
        $dueDate = Carbon::parse($loan->due_date);
        $returnDate = Carbon::parse($returnBook->return_date);

        // 1. Late Fine
        if ($returnDate->greaterThan($dueDate)) {
            $lateFinePerDay = Setting::where('key', 'late_fine_per_day')->value('value') ?? 1000;
            $days = $returnDate->diffInDays($dueDate);
            $amount = $days * $lateFinePerDay;
            $amount = min($amount, 10000000); // Cap fine at 10M

            Fine::create([
                'loan_id' => $loan->id,
                'amount' => $amount,
                'type' => 'late',
                'status' => 'unpaid',
            ]);
        }

        // 2. Damage/Lost Fine
        if (in_array($returnBook->condition, ['damaged', 'lost'])) {
            foreach ($loan->loanDetails as $detail) {
                $book = $detail->book;
                if (!$book) continue;

                $fineAmount = 0;
                if ($book->fine_type === 'fixed' && !empty($book->fine_value)) {
                    $fineAmount = (float) filter_var($book->fine_value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                } elseif ($book->fine_type === 'multiplier' && !empty($book->fine_value)) {
                    $multiplierStr = preg_replace('/[^0-9.]/', '', $book->fine_value);
                    $multiplier = (float) ($multiplierStr ?: 1);
                    $priceStr = filter_var($book->price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $price = (float) $priceStr;
                    $fineAmount = $price * $multiplier;
                } else {
                    $val = !empty($book->fine_value) ? $book->fine_value : $book->price;
                    $fineAmount = (float) filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                }

                if ($fineAmount <= 0 && !empty($book->price)) {
                    $fineAmount = (float) filter_var($book->price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                }

                $fineAmount = min($fineAmount, 50000000); // Cap fine at 50M

                if ($fineAmount > 0) {
                    Fine::create([
                        'loan_id' => $loan->id,
                        'amount' => $fineAmount,
                        'type' => $returnBook->condition,
                        'status' => 'unpaid',
                    ]);
                }
            }
        }
    }

    /**
     * Mark a fine as paid with payment date.
     */
    public function payFine(Fine $fine, array $data = [])
    {
        if ($fine->status === 'paid') {
            throw new \DomainException('Tagihan denda ini sudah tercatat lunas sebelumnya.');
        }

        $fine->update([
            'status' => 'paid',
            'payment_date' => $data['payment_date'] ?? now(),
        ]);

        return $fine;
    }
}
