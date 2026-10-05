<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin sees all activities
            $activities = Loan::with(['user', 'loanDetails.book'])
                ->latest()
                ->paginate(15);
        } else {
            // Members see only their own activities
            $activities = Loan::where('user_id', $user->id)
                ->with(['loanDetails.book'])
                ->latest()
                ->paginate(15);
        }
        
        return view('activities.index', compact('activities'));
    }
}
