<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use App\Services\LibraryService;

class LoanController extends Controller
{
    protected $libraryService;

    public function __construct(LibraryService $libraryService)
    {
        $this->libraryService = $libraryService;
    }

    public function index(Request $request)
    {
        // Trigger auto-expiry check on index view so stale pending reservations are updated
        $this->libraryService->expireAllOverdueReservations();

        $query = Loan::with(['user', 'loanDetails.book', 'fine']);

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

        // Summary counts for dashboard badges
        $summary = [
            'pending'   => Loan::where('status', Loan::STATUS_PENDING)->count(),
            'approved'  => Loan::where('status', Loan::STATUS_APPROVED)->count(),
            'borrowed'  => Loan::where('status', Loan::STATUS_BORROWED)->count(),
            'overdue'   => Loan::where('status', Loan::STATUS_OVERDUE)->count(),
        ];

        return view('officer.loans.index', compact('loans', 'summary'));
    }

    public function create()
    {
        $borrowers = User::where('role', 'anggota')->get();
        $books = Book::where('available_stock', '>', 0)->get();
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
            'due_date' => 'required|date|after:today|before_or_equal:' . now()->addDays(14)->toDateString(),
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

        $estimatedFine = (float) $loan->loanDetails->sum(function ($detail) {
            $book = $detail->book;
            if (!$book) return 0;
            if ($book->fine_type === 'fixed' && !empty($book->fine_value)) {
                return (float) filter_var($book->fine_value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            } elseif ($book->fine_type === 'multiplier' && !empty($book->fine_value)) {
                $multiplierStr = preg_replace('/[^0-9.]/', '', $book->fine_value);
                $multiplier = (float) ($multiplierStr ?: 1);
                $price = (float) filter_var($book->price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                return $price * $multiplier;
            } else {
                $val = !empty($book->fine_value) ? $book->fine_value : $book->price;
                $fine = (float) filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                return $fine > 0 ? $fine : (float) filter_var($book->price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            }
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
                'condition' => 'good',
                'notes'     => $request->input('notes'),
            ];
        } else {
            $request->validate([
                'condition' => 'required|in:good,damaged,lost',
                'notes'     => 'nullable|string',
            ]);
            $data = [
                'condition' => $request->input('condition'),
                'notes'     => $request->input('notes'),
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
