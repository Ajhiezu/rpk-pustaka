<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\Loan;
use App\Models\Category;
use App\Models\Article;
use App\Models\Essay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = [];
        $activities = [];

        if ($user->isAdmin()) {
            // ── Single aggregated DB hit for loan counts ──────────────────────
            $loanStats = Loan::selectRaw("
                COUNT(*) as total_loans,
                SUM(CASE WHEN status = 'borrowed' THEN 1 ELSE 0 END) as active_loans,
                SUM(CASE WHEN loan_type = 'physical' AND status = 'borrowed' THEN 1 ELSE 0 END) as active_physical_loans,
                SUM(CASE WHEN loan_type = 'digital'  AND status = 'borrowed' AND due_date >= CURDATE() THEN 1 ELSE 0 END) as active_digital_loans,
                SUM(CASE WHEN status = 'borrowed' AND due_date < CURDATE() THEN 1 ELSE 0 END) as overdue_loans
            ")->first();

            // ── Single aggregated DB hit for book counts ──────────────────────
            $bookStats = Book::selectRaw("
                COUNT(*) as total_books,
                SUM(stock) as total_physical_stock,
                SUM(CASE WHEN (collection_type = 'digital' OR collection_type = 'fisik_digital') AND pdf_path IS NOT NULL AND pdf_path != '' THEN 1 ELSE 0 END) as total_digital_books
            ")->first();

            // ── Remaining small counts (cached for 60s) ───────────────────────
            [$totalMembers, $totalCategories, $pendingEssays, $totalArticles] = Cache::remember('dashboard_admin_misc_counts', 60, function () {
                return [
                    User::where('role', 'anggota')->count(),
                    Category::count(),
                    Essay::where('status', 'submitted')->count(),
                    Article::count(),
                ];
            });

            $stats = [
                'total_books'           => (int) ($bookStats->total_books ?? 0),
                'total_physical_stock'  => (int) ($bookStats->total_physical_stock ?? 0),
                'total_digital_books'   => (int) ($bookStats->total_digital_books ?? 0),
                'total_members'         => $totalMembers,
                'active_loans'          => (int) ($loanStats->active_loans ?? 0),
                'active_physical_loans' => (int) ($loanStats->active_physical_loans ?? 0),
                'active_digital_loans'  => (int) ($loanStats->active_digital_loans ?? 0),
                'overdue_loans'         => (int) ($loanStats->overdue_loans ?? 0),
                'total_categories'      => $totalCategories,
                'pending_essays'        => $pendingEssays,
                'total_articles'        => $totalArticles,
            ];

            // Recent loans: only fetch columns we actually display
            $activities = Loan::with([
                'user:id,name,email',
                'loanDetails.book:id,title,image',
            ])
                ->latest()
                ->limit(5)
                ->get();

        } else {
            // ── Member: single aggregated query ──────────────────────────────
            $myStats = Loan::where('user_id', $user->id)
                ->selectRaw("
                    COUNT(*) as my_loans,
                    SUM(CASE WHEN status = 'borrowed' THEN 1 ELSE 0 END) as my_borrowed,
                    SUM(CASE WHEN loan_type = 'digital' AND status = 'borrowed' AND due_date >= CURDATE() THEN 1 ELSE 0 END) as my_digital_active,
                    SUM(CASE WHEN loan_type = 'physical' AND status = 'borrowed' AND due_date < CURDATE() THEN 1 ELSE 0 END) as my_overdue
                ")->first();

            $stats = [
                'my_loans'          => (int) ($myStats->my_loans ?? 0),
                'my_borrowed'       => (int) ($myStats->my_borrowed ?? 0),
                'my_digital_active' => (int) ($myStats->my_digital_active ?? 0),
                'my_overdue'        => (int) ($myStats->my_overdue ?? 0),
                'my_essays'         => Essay::where('user_id', $user->id)->count(),
            ];

            // Recent books for member to explore (only needed columns)
            $activities = Book::select('id', 'title', 'author', 'image', 'category_id', 'created_at')
                ->with('category:id,name')
                ->latest()
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact('stats', 'activities'));
    }
}
