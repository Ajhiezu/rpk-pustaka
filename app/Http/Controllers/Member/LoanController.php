<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Services\LibraryService;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    protected $libraryService;

    public function __construct(LibraryService $libraryService)
    {
        $this->libraryService = $libraryService;
    }

    public function index()
    {
        $loans = \App\Models\Loan::where('user_id', Auth::id())
            ->with(['loanDetails.book'])
            ->latest()
            ->paginate(10);
            
        return view('member.loans.index', compact('loans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'due_date' => 'required|date|after:today|before_or_equal:' . now()->addDays(14)->toDateString(),
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->available_stock <= 0) {
            return redirect()->back()->with('error', 'Maaf, stok buku ini sedang habis.');
        }

        $this->libraryService->createLoan([
            'user_id' => Auth::id(),
            'book_ids' => [$request->book_id],
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('member.loans.index')->with('success', 'Buku berhasil dipesan! Silakan ambil di perpustakaan sebelum batas waktu.');

    }
}
