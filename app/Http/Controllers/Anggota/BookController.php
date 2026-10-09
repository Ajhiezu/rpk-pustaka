<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['category', 'location']);

        // Grouped search query to prevent category leakage
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Format filter: all, physical, digital
        if ($request->filled('format')) {
            if ($request->format === 'digital') {
                $query->whereNotNull('pdf_path')->where('pdf_path', '!=', '');
            } elseif ($request->format === 'physical') {
                $query->where('stock', '>', 0);
            }
        }

        // Book Type filter: fiksi, nonfiksi
        if ($request->filled('book_type') && in_array($request->book_type, ['fiksi', 'nonfiksi'])) {
            $query->where('book_type', $request->book_type);
        }

        $books = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('member.books.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        $book->load(['category', 'location']);

        $activeDigitalLoan = null;
        $activePhysicalLoan = null;

        if (Auth::check()) {
            $activeDigitalLoan = Loan::where('user_id', Auth::id())
                ->where('loan_type', 'digital')
                ->where('status', 'borrowed')
                ->whereDate('due_date', '>=', now()->toDateString())
                ->whereHas('loanDetails', fn($q) => $q->where('book_id', $book->id))
                ->first();

            $activePhysicalLoan = Loan::where('user_id', Auth::id())
                ->where('loan_type', 'physical')
                ->where('status', 'borrowed')
                ->whereHas('loanDetails', fn($q) => $q->where('book_id', $book->id))
                ->first();
        }

        return view('member.books.show', compact('book', 'activeDigitalLoan', 'activePhysicalLoan'));
    }
}
