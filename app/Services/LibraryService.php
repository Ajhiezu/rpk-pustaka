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
use Illuminate\Support\Facades\Cache;
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
                ? (int) $this->getSetting('digital_loan_duration_days', 7)
                : (int) $this->getSetting('physical_loan_duration_days', 14);
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
            if ($loanType === 'physical') {
                // Lock all rows in one query to prevent race conditions
                $books = Book::whereIn('id', $bookIds)->lockForUpdate()->get()->keyBy('id');
                foreach ($bookIds as $bookId) {
                    $book = $books->get($bookId);
                    if (!$book) {
                        throw new \DomainException("Buku dengan ID {$bookId} tidak ditemukan.");
                    }
                    if ($book->available_stock <= 0) {
                        throw new \DomainException("Buku fisik '{$book->title}' tidak tersedia untuk dipesan saat ini.");
                    }
                    $booksToLoan[] = $book;
                }
            } else {
                // Digital loan: ensure PDF exists — load in batch
                $books = Book::whereIn('id', $bookIds)->get()->keyBy('id');
                foreach ($bookIds as $bookId) {
                    $book = $books->get($bookId);
                    if (!$book) {
                        throw new \DomainException("Buku dengan ID {$bookId} tidak ditemukan.");
                    }
                    if (!$book->hasDigital()) {
                        throw new \DomainException("Versi digital untuk buku '{$book->title}' belum tersedia.");
                    }
                    $booksToLoan[] = $book;
                }
            }

            // 3. Determine Expiry Deadline and Unique Stable Loan Code
            $expiryHours = (int) ($this->getSetting('physical_reservation_expiry_hours', 24));
            $initialStatus = $data['status'] ?? ($loanType === 'physical' ? 'pending' : 'borrowed');
            $pickupDeadline = ($loanType === 'physical' && $initialStatus === 'pending') ? now()->addHours($expiryHours) : null;
            $approvedAt = in_array($initialStatus, ['approved', 'borrowed']) ? now() : null;
            $borrowedAt = $initialStatus === 'borrowed' ? now() : null;

            $datePrefix = date('Ymd');
            // Single query to get today's count instead of looping
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
            $detailRows = [];
            $now = now();
            foreach ($booksToLoan as $book) {
                $detailRows[] = [
                    'loan_id'    => $loan->id,
                    'book_id'    => $book->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            // Bulk insert all loan details in one query
            LoanDetail::insert($detailRows);

            // Bulk decrement stock for physical loans in one query
            if ($loanType === 'physical') {
                $bookIdsToDecrement = array_map(fn($b) => $b->id, $booksToLoan);
                DB::table('books')->whereIn('id', $bookIdsToDecrement)->decrement('available_stock');
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
                // Bulk restore stock in one query
                $bookIds = $loanRecord->loanDetails->pluck('book_id')->toArray();
                if (!empty($bookIds)) {
                    DB::table('books')->whereIn('id', $bookIds)->increment('available_stock');
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
                // Bulk restore stock in one query
                $bookIds = $loanRecord->loanDetails->pluck('book_id')->toArray();
                if (!empty($bookIds)) {
                    DB::table('books')->whereIn('id', $bookIds)->increment('available_stock');
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
                // Bulk restore stock in one query
                $bookIds = $loanRecord->loanDetails->pluck('book_id')->toArray();
                if (!empty($bookIds)) {
                    DB::table('books')->whereIn('id', $bookIds)->increment('available_stock');
                }
            }

            return true;
        });
    }

    /**
     * Automatically expire all unclaimed physical reservations past deadline.
     * Uses bulk update + single stock restoration instead of N loops.
     */
    public function expireAllOverdueReservations(): int
    {
        $overdueLoans = Loan::where('status', 'pending')
            ->where('loan_type', 'physical')
            ->whereNotNull('pickup_deadline')
            ->where('pickup_deadline', '<', now())
            ->with('loanDetails') // eager load to avoid N+1 inside loop
            ->get();

        if ($overdueLoans->isEmpty()) {
            return 0;
        }

        $expiredLoanIds = $overdueLoans->pluck('id')->toArray();
        $bookIdsToRestore = $overdueLoans->flatMap(fn($l) => $l->loanDetails->pluck('book_id'))->unique()->toArray();

        // Bulk expire all overdue loans in one query
        Loan::whereIn('id', $expiredLoanIds)->update(['status' => 'expired']);

        // Bulk restore available_stock in one query
        if (!empty($bookIdsToRestore)) {
            DB::table('books')->whereIn('id', $bookIdsToRestore)->increment('available_stock');
        }

        return count($expiredLoanIds);
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
                throw new \DomainException('Peminjaman ini sudah dikembalikan sepenuhnya sebelumnya.');
            }

            if (!in_array($loanRecord->status, ['borrowed', 'overdue', 'approved', 'partially_returned'])) {
                throw new \DomainException('Transaksi tidak dalam status yang valid untuk diproses pengembaliannya.', 422);
            }

            $detailIds = $data['detail_ids'] ?? [];
            if (!is_array($detailIds)) {
                $detailIds = [$detailIds];
            }

            // Get target loan details to return
            $targetQuery = $loanRecord->loanDetails()->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'returned');
            });

            if (!empty($detailIds)) {
                $targetQuery->whereIn('id', $detailIds);
            }

            $detailsToReturn = $targetQuery->get();

            if ($detailsToReturn->isEmpty()) {
                throw new \DomainException('Tidak ada item buku yang dipilih untuk dikembalikan.');
            }

            // Create ReturnBook record
            $returnBook = ReturnBook::create([
                'loan_id' => $loanRecord->id,
                'return_date' => now(),
                'condition' => $data['condition'] ?? 'good',
                'notes' => $data['notes'] ?? null,
            ]);

            // Mark selected details as returned and restore physical stock
            $detailIdsToReturn = $detailsToReturn->pluck('id')->toArray();
            $bookIdsToRestore = [];

            // Bulk update detail status in one query
            LoanDetail::whereIn('id', $detailIdsToReturn)->update([
                'status'      => 'returned',
                'returned_at' => now(),
            ]);

            // Restore physical stock in bulk (only for 'good' condition)
            if ($loanRecord->isPhysical() && ($data['condition'] ?? 'good') === 'good') {
                $bookIdsToRestore = $detailsToReturn->pluck('book_id')->unique()->toArray();
                if (!empty($bookIdsToRestore)) {
                    DB::table('books')->whereIn('id', $bookIdsToRestore)->increment('available_stock');
                }
            }

            // Check if all details are now returned
            $remainingUnreturned = $loanRecord->loanDetails()
                ->where(function ($q) {
                    $q->whereNull('status')->orWhere('status', '!=', 'returned');
                })->count();

            if ($remainingUnreturned === 0) {
                $loanRecord->update(['status' => 'returned']);
            } else {
                $loanRecord->update(['status' => 'partially_returned']);
            }

            // Calculate fines for physical returns
            if ($loanRecord->isPhysical()) {
                $this->calculateFine($loanRecord, $returnBook, $detailsToReturn);
            }

            return $returnBook;
        });
    }

    /**
     * Calculate late or damaged/lost fines for physical loans.
     */
    protected function calculateFine(Loan $loan, ReturnBook $returnBook, $detailsToReturn = null)
    {
        $dueDate = Carbon::parse($loan->due_date);
        $returnDate = Carbon::parse($returnBook->return_date);

        // 1. Late Fine
        if ($returnDate->greaterThan($dueDate)) {
            $lateFinePerDay = $this->getSetting('late_fine_per_day', 1000);
            $days = $returnDate->diffInDays($dueDate);
            $lateAmount = $days * $lateFinePerDay;
            $lateAmount = min($lateAmount, 10000000); // Cap fine at 10M

            if ($lateAmount > 0) {
                $existingFine = Fine::where('loan_id', $loan->id)->where('status', 'unpaid')->first();
                if ($existingFine) {
                    $existingFine->update([
                        'amount' => min($existingFine->amount + $lateAmount, 50000000),
                    ]);
                } else {
                    Fine::create([
                        'loan_id' => $loan->id,
                        'amount'  => $lateAmount,
                        'type'    => 'late',
                        'status'  => 'unpaid',
                    ]);
                }
            }
        }

        // 2. Damage/Lost Fine (Summed across all target returned books)
        if (in_array($returnBook->condition, ['damaged', 'lost'])) {
            $targetDetails = ($detailsToReturn && count($detailsToReturn) > 0) ? $detailsToReturn : $loan->loanDetails;
            $totalDamageFine = 0;

            foreach ($targetDetails as $detail) {
                $book = $detail->book;
                if (!$book) continue;

                $totalDamageFine += $book->getCalculatedFineAmount();
            }

            $totalDamageFine = min($totalDamageFine, 50000000); // Cap fine at 50M

            if ($totalDamageFine > 0) {
                $existingFine = Fine::where('loan_id', $loan->id)->where('status', 'unpaid')->first();
                if ($existingFine) {
                    $existingFine->update([
                        'amount' => min($existingFine->amount + $totalDamageFine, 50000000),
                        'type'   => $returnBook->condition,
                    ]);
                } else {
                    Fine::create([
                        'loan_id' => $loan->id,
                        'amount'  => $totalDamageFine,
                        'type'    => $returnBook->condition,
                        'status'  => 'unpaid',
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
            'status'       => 'paid',
            'payment_date' => $data['payment_date'] ?? now(),
        ]);

        return $fine;
    }

    /**
     * Get a setting value with short-lived cache to reduce DB hits.
     */
    protected function getSetting(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 120, function () use ($key, $default) {
            return Setting::where('key', $key)->value('value') ?? $default;
        });
    }
}
