<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with activity overview.
     */
    public function index(): JsonResponse
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

        return response()->json([
            'stats' => $stats,
            'overdue' => ActivityResource::collection($overdue),
            'due_soon' => ActivityResource::collection($dueSoon),
            'in_progress' => ActivityResource::collection($inProgress),
        ]);
    }
}
