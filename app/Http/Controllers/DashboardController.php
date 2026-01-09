<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with activity overview.
     */
    public function index(): View
    {
        // Get overdue activities
        $overdue = Activity::overdue()
            ->with('lead')
            ->orderBy('due_date')
            ->get();

        // Get activities due in next 7 days (not overdue)
        $dueSoon = Activity::dueSoon(7)
            ->with('lead')
            ->orderBy('due_date')
            ->get();

        // Get all in-progress activities
        $inProgress = Activity::inProgress()
            ->with('lead')
            ->orderByUrgency()
            ->get();

        // Calculate stats
        $stats = [
            'overdue_count' => $overdue->count(),
            'due_soon_count' => $dueSoon->count(),
            'in_progress_count' => $inProgress->count(),
            'completed_count' => Activity::completed()->count(),
        ];

        return view('dashboard', compact('overdue', 'dueSoon', 'inProgress', 'stats'));
    }
}
