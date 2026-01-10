<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreActivityRequest;
use App\Http\Requests\Api\V1\UpdateActivityRequest;
use App\Http\Resources\Api\V1\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the activities.
     */
    public function index(Request $request): JsonResponse
    {
        $query = auth()->user()->activities()->with(['lead', 'parties']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $activities = $query->orderByUrgency()->paginate(20);

        return ActivityResource::collection($activities)->response();
    }

    /**
     * Store a newly created activity in storage.
     */
    public function store(StoreActivityRequest $request): JsonResponse
    {
        $activity = auth()->user()->activities()->create($request->validated());

        // Sync participants
        if ($request->has('parties')) {
            $activity->parties()->sync($request->parties);
        }

        return (new ActivityResource($activity->load(['lead', 'parties'])))->response()->setStatusCode(201);
    }

    /**
     * Display the specified activity.
     */
    public function show(Activity $activity): JsonResponse
    {
        // Policy check will be handled by Laravel's authorization (implicit with global scope)
        // Ensure the activity belongs to the authenticated user.
        // The global scope on the Activity model already handles this.
        return (new ActivityResource($activity->load(['lead', 'parties'])))->response();
    }

    /**
     * Update the specified activity in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity): JsonResponse
    {
        // Policy check will be handled by Laravel's authorization (implicit with global scope)
        $activity->update($request->validated());

        // Sync participants
        $activity->parties()->sync($request->parties ?? []);

        return (new ActivityResource($activity->load(['lead', 'parties'])))->response();
    }

    /**
     * Remove the specified activity from storage.
     */
    public function destroy(Activity $activity): JsonResponse
    {
        // Policy check will be handled by Laravel's authorization (implicit with global scope)
        $activity->delete();

        return response()->json(null, 204);
    }
}
