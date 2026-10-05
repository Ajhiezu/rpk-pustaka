<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Book;
use App\Models\Category;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['category', 'location']);

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $books = $query->latest()->paginate(12);
        $categories = Category::all();

        return view('member.books.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        $book->load(['category', 'location']);
        return view('member.books.show', compact('book'));
    }
}
