<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use App\Services\LibraryService;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class LoanController extends Controller
{
    protected $libraryService;

    public function __construct(LibraryService $libraryService)
    {
        $this->libraryService = $libraryService;
    }

    public function index(Request $request)
    {
        // Only run expiry check when there are pending loans (avoid full scan every page load)
        $hasPending = Cache::remember('has_pending_reservations', 30, function () {
            return Loan::where('status', Loan::STATUS_PENDING)
                ->where('loan_type', 'physical')
                ->whereNotNull('pickup_deadline')
                ->where('pickup_deadline', '<', now())
                ->exists();
        });
        if ($hasPending) {
            $this->libraryService->expireAllOverdueReservations();
            Cache::forget('has_pending_reservations');
        }

        $query = Loan::with([
            'user:id,name,email',
            'loanDetails.book:id,title,image',
            'fine:id,loan_id,amount,status',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('loanDetails.book', function ($bq) use ($search) {
                      $bq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('loan_type', $request->type);
        }

        if ($request->filled('fine_status')) {
            $fineStatus = $request->fine_status;
            if ($fineStatus === 'unpaid') {
                $query->whereHas('fine', fn($q) => $q->where('status', 'unpaid'));
            } elseif ($fineStatus === 'paid') {
                $query->whereHas('fine', fn($q) => $q->where('status', 'paid'));
            } elseif ($fineStatus === 'no_fine') {
                $query->whereDoesntHave('fine');
            }
        }

        $loans = $query->latest()->paginate(10)->withQueryString();

        // Single aggregated query for summary counts
        $summaryRaw = Loan::selectRaw("
            SUM(CASE WHEN status = 'pending'  THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'borrowed' THEN 1 ELSE 0 END) as borrowed,
            SUM(CASE WHEN status = 'overdue'  THEN 1 ELSE 0 END) as overdue
        ")->first();

        $summary = [
            'pending'  => (int) ($summaryRaw->pending ?? 0),
            'approved' => (int) ($summaryRaw->approved ?? 0),
            'borrowed' => (int) ($summaryRaw->borrowed ?? 0),
            'overdue'  => (int) ($summaryRaw->overdue ?? 0),
        ];

        return view('officer.loans.index', compact('loans', 'summary'));
    }

    public function create()
    {
        $borrowers = User::select('id', 'name', 'email')->where('role', 'anggota')->orderBy('name')->get();
        $books = Book::select('id', 'title', 'author', 'book_code', 'available_stock', 'location_id')
            ->where('available_stock', '>', 0)
            ->with('location:id,name,description')
            ->orderBy('title')
            ->get();
        return view('officer.loans.create', compact('borrowers', 'books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => [
                'required', 
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if ($user && $user->isAdmin()) {
                        $fail('Administrator tidak diperbolehkan meminjam buku.');
                    }
                },
            ],
            'book_ids' => 'required|array|min:1',
            'book_ids.*' => 'exists:books,id',
            'due_date' => 'required|date|after:today|before_or_equal:' . now()->addDays((int) Setting::get('physical_loan_duration_days', 14))->toDateString(),
        ]);

        try {
            $data = $request->all();
            $data['loan_type'] = $request->input('loan_type', 'physical');
            $data['status'] = Loan::STATUS_BORROWED;

            $this->libraryService->createLoan($data);

            return redirect()->route('admin.loans.index')->with('success', 'Peminjaman berhasil dicatat dengan status Sedang Dipinjam!');
        } catch (\DomainException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Loan $loan)
    {
        $loan->load(['user', 'loanDetails.book.location', 'returnBook', 'fine']);

        $estimatedFine = (float) $loan->loanDetails
            ->filter(fn($detail) => !$detail->isReturned())
            ->sum(function ($detail) {
                $book = $detail->book;
                return $book ? $book->getCalculatedFineAmount() : 0;
            });

        return view('officer.loans.show', compact('loan', 'estimatedFine'));
    }

    public function approve(Loan $loan)
    {
        try {
            $this->libraryService->approveReservation($loan);
            return redirect()->route('admin.loans.show', $loan)
                ->with('success', 'Reservasi peminjaman berhasil disetujui! Menunggu anggota mengambil buku.');
        } catch (\DomainException $e) {
            return redirect()->route('admin.loans.show', $loan)->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('admin.loans.show', $loan)->with('error', 'Gagal menyetujui reservasi: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Loan $loan)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:255',
        ]);

        try {
            $reason = $request->input('rejection_reason', 'Penolakan oleh admin/petugas.');
            $this->libraryService->rejectReservation($loan, $reason);
            return redirect()->route('admin.loans.show', $loan)
                ->with('success', 'Reservasi peminjaman ditolak. Stok fisik buku telah dilepas kembali.');
        } catch (\DomainException $e) {
            return redirect()->route('admin.loans.show', $loan)->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('admin.loans.show', $loan)->with('error', 'Gagal menolak reservasi: ' . $e->getMessage());
        }
    }

    public function handover(Loan $loan)
    {
        try {
            $this->libraryService->handoverLoan($loan);
            return redirect()->route('admin.loans.show', $loan)
                ->with('success', 'Buku fisik telah berhasil diserahkan kepada Anggota! Status peminjaman aktif (borrowed).');
        } catch (\DomainException $e) {
            return redirect()->route('admin.loans.show', $loan)->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('admin.loans.show', $loan)->with('error', 'Gagal memproses penyerahan buku: ' . $e->getMessage());
        }
    }

    public function returnBook(Request $request, Loan $loan)
    {
        if ($loan->isDigital()) {
            $data = [
                'condition'  => 'good',
                'notes'      => $request->input('notes'),
                'detail_ids' => $request->input('detail_ids', []),
            ];
        } else {
            $request->validate([
                'condition'    => 'required|in:good,damaged,lost',
                'notes'        => 'nullable|string',
                'detail_ids'   => 'nullable|array',
                'detail_ids.*' => 'integer',
            ]);
            $data = [
                'condition'  => $request->input('condition'),
                'notes'      => $request->input('notes'),
                'detail_ids' => $request->input('detail_ids', []),
            ];
        }

        try {
            $this->libraryService->processReturn($loan, $data);
            return redirect()->route('admin.loans.show', $loan)->with('success', 'Buku berhasil diproses pengembaliannya!');
        } catch (\DomainException $e) {
            return redirect()->route('admin.loans.show', $loan)->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('admin.loans.show', $loan)->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }

    public function payFine(Request $request, Loan $loan)
    {
        $fine = $loan->fine;
        if (!$fine) {
            return redirect()->back()->with('error', 'Tidak ada tagihan denda untuk transaksi peminjaman ini.');
        }

        $request->validate([
            'payment_date' => 'nullable|date',
        ]);

        try {
            $this->libraryService->payFine($fine, $request->all());
            return redirect()->route('admin.loans.show', $loan)->with('success', 'Pembayaran denda sebesar Rp ' . number_format($fine->amount, 0, ',', '.') . ' berhasil dicatat dan dinyatakan LUNAS!');
        } catch (\Exception $e) {
            return redirect()->route('admin.loans.show', $loan)->with('error', $e->getMessage());
        }
    }
}
