<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePersonRequest;
use App\Http\Requests\Api\V1\UpdatePersonRequest;
use App\Http\Resources\Api\V1\PersonResource;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Display a listing of the people.
     */
    public function index(Request $request): JsonResponse
    {
        $query = auth()->user()->people();

        // Search by name, email, or company
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $people = $query->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        return PersonResource::collection($people)->response();
    }

    /**
     * Store a newly created person in storage.
     */
    public function store(StorePersonRequest $request): JsonResponse
    {
        $person = auth()->user()->people()->create($request->validated());

        return (new PersonResource($person))->response()->setStatusCode(201);
    }

    /**
     * Display the specified person.
     */
    public function show(Person $person): JsonResponse
    {
        // Global scope on Person model ensures this person belongs to the authenticated user.
        return (new PersonResource($person))->response();
    }

    /**
     * Update the specified person in storage.
     */
    public function update(UpdatePersonRequest $request, Person $person): JsonResponse
    {
        // Global scope on Person model ensures this person belongs to the authenticated user.
        $person->update($request->validated());

        return (new PersonResource($person))->response();
    }

    /**
     * Remove the specified person from storage.
     */
    public function destroy(Person $person): JsonResponse
    {
        // Global scope on Person model ensures this person belongs to the authenticated user.
        $person->delete();

        return response()->json(null, 204);
    }
}
