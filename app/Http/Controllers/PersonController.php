<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Mail\BroadcastMessage;
use App\Mail\PersonalSummary;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PersonController extends Controller
{
    /**
     * Display a listing of people.
     */
    public function index(Request $request): View
    {
        $query = Person::query();

        // Search by name, email, or company
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $people = $query->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        return view('people.index', compact('people'));
    }

    /**
     * Show the form for creating a new person.
     */
    public function create(): View
    {
        return view('people.create');
    }

    /**
     * Store a newly created person.
     */
    public function store(StorePersonRequest $request): RedirectResponse
    {
        Person::create($request->validated());

        return redirect()
            ->route('people.index')
            ->with('success', 'Person added successfully.');
    }

    /**
     * Display the specified person.
     */
    public function show(Person $person): View
    {
        // Get activities where this person is lead
        $activitiesAsLead = $person->activitiesAsLead()
            ->with('parties')
            ->orderByUrgency()
            ->get();

        // Get activities where this person is a participant
        $activitiesAsParty = $person->activitiesAsParty()
            ->with('lead')
            ->orderByUrgency()
            ->get();

        return view('people.show', compact('person', 'activitiesAsLead', 'activitiesAsParty'));
    }

    /**
     * Show the form for editing the specified person.
     */
    public function edit(Person $person): View
    {
        return view('people.edit', compact('person'));
    }

    /**
     * Update the specified person.
     */
    public function update(UpdatePersonRequest $request, Person $person): RedirectResponse
    {
        $person->update($request->validated());

        return redirect()
            ->route('people.show', $person)
            ->with('success', 'Person updated successfully.');
    }

    /**
     * Remove the specified person.
     */
    public function destroy(Person $person): RedirectResponse
    {
        $name = $person->full_name;
        $person->delete();

        return redirect()
            ->route('people.index')
            ->with('success', "'{$name}' has been deleted.");
    }

    /**
     * Send activity summary to a specific person.
     */
    public function sendSummary(Person $person): JsonResponse
    {
        if (!$person->email_primary) {
            return response()->json([
                'success' => false,
                'message' => 'This person does not have an email address.',
            ], 400);
        }

        // Get all their activities
        $activities = $person->getAllActivities();

        if ($activities->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'This person has no activities to summarize.',
            ], 400);
        }

        try {
            Mail::to($person->email_primary)
                ->send(new PersonalSummary($person, $activities));

            return response()->json([
                'success' => true,
                'message' => "Summary sent to {$person->email_primary}",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send broadcast email to multiple people.
     */
    public function broadcast(Request $request): JsonResponse
    {
        $request->validate([
            'people' => 'required|array|min:1',
            'people.*' => 'exists:people,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:10000',
        ]);

        $people = Person::whereIn('id', $request->people)
            ->withEmail()
            ->get();

        if ($people->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'None of the selected people have email addresses.',
            ], 400);
        }

        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($people as $person) {
            try {
                Mail::to($person->email_primary)
                    ->send(new BroadcastMessage(
                        $person,
                        $request->subject,
                        $request->message
                    ));
                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = $person->full_name . ': ' . $e->getMessage();
            }
        }

        return response()->json([
            'success' => $failedCount === 0,
            'message' => "Broadcast sent to {$successCount} recipient(s)." . ($failedCount > 0 ? " {$failedCount} failed." : ''),
            'sent' => $successCount,
            'failed' => $failedCount,
            'errors' => $errors,
        ]);
    }
}
