<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Mail\ActivityUpdate;
use App\Models\Activity;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Display a listing of activities.
     */
    public function index(Request $request): View
    {
        $query = Activity::with(['lead', 'parties']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $activities = $query->orderByUrgency()->paginate(20);

        return view('activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new activity.
     */
    public function create(): View
    {
        $people = Person::orderBy('last_name')->orderBy('first_name')->get();

        return view('activities.create', compact('people'));
    }

    /**
     * Store a newly created activity.
     */
    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $activity = Activity::create($request->validated());

        // Sync participants
        if ($request->has('parties')) {
            $activity->parties()->sync($request->parties);
        }

        return redirect()
            ->route('activities.index')
            ->with('success', 'Activity created successfully.');
    }

    /**
     * Display the specified activity (JSON for modal).
     */
    public function show(Activity $activity): JsonResponse
    {
        $activity->load(['lead', 'parties']);

        return response()->json([
            'id' => $activity->id,
            'name' => $activity->name,
            'logic' => $activity->logic,
            'next_step' => $activity->next_step,
            'start_date' => $activity->start_date?->format('d M Y'),
            'due_date' => $activity->due_date?->format('d M Y'),
            'status' => $activity->status,
            'status_label' => $activity->status_label,
            'days_until_due' => $activity->days_until_due,
            'is_overdue' => $activity->is_overdue,
            'urgency_level' => $activity->urgency_level,
            'urgency_badge' => $activity->urgency_badge,
            'lead' => $activity->lead ? [
                'id' => $activity->lead->id,
                'full_name' => $activity->lead->full_name,
                'email' => $activity->lead->email_primary,
            ] : null,
            'parties' => $activity->parties->map(fn($p) => [
                'id' => $p->id,
                'full_name' => $p->full_name,
                'email' => $p->email_primary,
            ]),
            'created_at' => $activity->created_at?->format('d M Y H:i'),
            'updated_at' => $activity->updated_at?->format('d M Y H:i'),
        ]);
    }

    /**
     * Show the form for editing the specified activity.
     */
    public function edit(Activity $activity): View
    {
        $people = Person::orderBy('last_name')->orderBy('first_name')->get();
        $activity->load('parties');

        return view('activities.edit', compact('activity', 'people'));
    }

    /**
     * Update the specified activity.
     */
    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $activity->update($request->validated());

        // Sync participants
        $activity->parties()->sync($request->parties ?? []);

        return redirect()
            ->route('activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Remove the specified activity.
     */
    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()
            ->route('activities.index')
            ->with('success', 'Activity deleted successfully.');
    }

    /**
     * Send email to activity participants.
     */
    public function emailParticipants(Request $request, Activity $activity): JsonResponse
    {
        $request->validate([
            'message' => 'nullable|string|max:5000',
        ]);

        $activity->load(['lead', 'parties']);

        // Collect all recipients with email addresses
        $recipients = collect();

        if ($activity->lead && $activity->lead->email_primary) {
            $recipients->push([
                'person' => $activity->lead,
                'role' => 'Lead',
            ]);
        }

        foreach ($activity->parties as $party) {
            if ($party->email_primary) {
                $recipients->push([
                    'person' => $party,
                    'role' => 'Participant',
                ]);
            }
        }

        if ($recipients->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No participants have email addresses.',
            ], 400);
        }

        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient['person']->email_primary)
                    ->send(new ActivityUpdate(
                        $activity,
                        $recipient['person'],
                        $recipient['role'],
                        $request->message
                    ));
                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = $recipient['person']->full_name . ': ' . $e->getMessage();
            }
        }

        return response()->json([
            'success' => $failedCount === 0,
            'message' => "Sent to {$successCount} recipient(s)." . ($failedCount > 0 ? " {$failedCount} failed." : ''),
            'sent' => $successCount,
            'failed' => $failedCount,
            'errors' => $errors,
        ]);
    }
}
