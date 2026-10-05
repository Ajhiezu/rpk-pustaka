<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\Loan;
use App\Models\Category;
use App\Models\Article;
use App\Models\Essay;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = [];
        $activities = [];

        if ($user->isAdmin()) {
            $stats = [
                'total_books' => Book::count(),
                'total_physical_stock' => Book::sum('stock'),
                'total_digital_books' => Book::whereNotNull('pdf_path')->where('pdf_path', '!=', '')->count(),
                'total_members' => User::where('role', 'anggota')->count(),
                'active_loans' => Loan::where('status', 'borrowed')->count(),
                'active_physical_loans' => Loan::where('loan_type', 'physical')->where('status', 'borrowed')->count(),
                'active_digital_loans' => Loan::where('loan_type', 'digital')->where('status', 'borrowed')->whereDate('due_date', '>=', now()->toDateString())->count(),
                'overdue_loans' => Loan::where('status', 'borrowed')->whereDate('due_date', '<', now()->toDateString())->count(),
                'total_categories' => Category::count(),
                'pending_essays' => Essay::where('status', 'submitted')->count(),
                'total_articles' => Article::count(),
            ];

            // Recent loans for admin
            $activities = Loan::with(['user', 'loanDetails.book'])
                ->latest()
                ->limit(5)
                ->get();
        } else {
            $stats = [
                'my_loans' => Loan::where('user_id', $user->id)->count(),
                'my_borrowed' => Loan::where('user_id', $user->id)->where('status', 'borrowed')->count(),
                'my_digital_active' => Loan::where('user_id', $user->id)
                    ->where('loan_type', 'digital')
                    ->where('status', 'borrowed')
                    ->whereDate('due_date', '>=', now()->toDateString())
                    ->count(),
                'my_overdue' => Loan::where('user_id', $user->id)
                    ->where('loan_type', 'physical')
                    ->where('status', 'borrowed')
                    ->whereDate('due_date', '<', now()->toDateString())
                    ->count(),
                'my_essays' => Essay::where('user_id', $user->id)->count(),
            ];

            // Recent books for anggota to explore
            $activities = Book::with(['category'])->latest()->limit(5)->get();
        }

        return view('dashboard', compact('stats', 'activities'));
    }
}
