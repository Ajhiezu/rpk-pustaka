<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LoansExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with(['user', 'loanDetails.book']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->latest()->paginate(15);
        
        return view('admin.reports.index', compact('loans'));
    }

    public function exportPdf(Request $request)
    {
        $loans = Loan::with(['user', 'loanDetails.book'])
            ->when($request->start_date, fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->end_date, fn($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf', compact('loans'));
        return $pdf->download('laporan-perpustakaan-'.now()->format('Y-m-d').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new LoansExport($request), 'laporan-perpustakaan-'.now()->format('Y-m-d').'.xlsx');
    }
}
