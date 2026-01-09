@extends('layouts.app')

@section('title', 'Edit Activity')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Activity</h1>
    <div class="page-actions">
        <a href="{{ route('activities.index') }}" class="btn btn-secondary">Back to List</a>
        <form method="POST" action="{{ route('activities.destroy', $activity) }}"
              class="inline"
              onsubmit="return confirm('Are you sure you want to delete this activity?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete Activity</button>
        </form>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="{{ route('activities.update', $activity) }}" class="activity-form">
        @csrf
        @method('PUT')

        <!-- Activity Name -->
        <div class="form-group">
            <label for="name" class="form-label required">Activity Name</label>
            <input type="text"
                   id="name"
                   name="name"
                   value="{{ old('name', $activity->name) }}"
                   class="form-input @error('name') is-invalid @enderror"
                   placeholder="Enter activity name"
                   required
                   autofocus>
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Dates Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="start_date" class="form-label required">Start Date</label>
                <input type="date"
                       id="start_date"
                       name="start_date"
                       value="{{ old('start_date', $activity->start_date?->format('Y-m-d')) }}"
                       class="form-input @error('start_date') is-invalid @enderror"
                       required>
                @error('start_date')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date"
                       id="due_date"
                       name="due_date"
                       value="{{ old('due_date', $activity->due_date?->format('Y-m-d')) }}"
                       class="form-input @error('due_date') is-invalid @enderror">
                @error('due_date')
                    <span class="form-error">{{ $message }}</span>
                @enderror
                @if($activity->is_overdue)
                    <small class="form-hint form-hint-danger">This activity is overdue!</small>
                @endif
            </div>
        </div>

        <!-- Lead and Status Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="lead_id" class="form-label">Lead Person</label>
                <select id="lead_id"
                        name="lead_id"
                        class="form-select @error('lead_id') is-invalid @enderror">
                    <option value="">No lead assigned</option>
                    @foreach($people as $person)
                        <option value="{{ $person->id }}" {{ old('lead_id', $activity->lead_id) == $person->id ? 'selected' : '' }}>
                            {{ $person->full_name }}
                            @if($person->company) ({{ $person->company }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('lead_id')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status" class="form-label required">Status</label>
                <select id="status"
                        name="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required>
                    <option value="in_progress" {{ old('status', $activity->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status', $activity->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status', $activity->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Participants -->
        <div class="form-group">
            <label class="form-label">Participants (Optional, max 5)</label>
            <div class="participant-search">
                <input type="text"
                       id="participantSearch"
                       class="form-input"
                       placeholder="Search participants..."
                       onkeyup="filterParticipants()">
            </div>
            @php
                $currentParties = old('parties', $activity->parties->pluck('id')->toArray());
            @endphp
            <div class="participant-list" id="participantList">
                @foreach($people as $person)
                    <label class="participant-item" data-name="{{ strtolower($person->full_name) }}">
                        <input type="checkbox"
                               name="parties[]"
                               value="{{ $person->id }}"
                               {{ in_array($person->id, $currentParties) ? 'checked' : '' }}>
                        <span>{{ $person->full_name }}</span>
                        @if($person->company)
                            <small class="participant-company">{{ $person->company }}</small>
                        @endif
                    </label>
                @endforeach
            </div>
            @error('parties')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Logic / Background -->
        <div class="form-group">
            <label for="logic" class="form-label">Background / Logic</label>
            <textarea id="logic"
                      name="logic"
                      class="form-textarea @error('logic') is-invalid @enderror"
                      rows="4"
                      placeholder="Enter background information or context...">{{ old('logic', $activity->logic) }}</textarea>
            @error('logic')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Next Step -->
        <div class="form-group">
            <label for="next_step" class="form-label">Next Step</label>
            <textarea id="next_step"
                      name="next_step"
                      class="form-textarea @error('next_step') is-invalid @enderror"
                      rows="3"
                      placeholder="What needs to happen next?">{{ old('next_step', $activity->next_step) }}</textarea>
            @error('next_step')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Meta Information -->
        <div class="form-meta">
            <small>Created: {{ $activity->created_at?->format('d M Y H:i') }}</small>
            <small>Last updated: {{ $activity->updated_at?->format('d M Y H:i') }}</small>
        </div>

        <!-- Submit -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
            <a href="{{ route('activities.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function filterParticipants() {
    const search = document.getElementById('participantSearch').value.toLowerCase();
    const items = document.querySelectorAll('.participant-item');

    items.forEach(item => {
        const name = item.dataset.name;
        item.style.display = name.includes(search) ? '' : 'none';
    });
}

// Limit to 5 participants
document.querySelectorAll('input[name="parties[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const checked = document.querySelectorAll('input[name="parties[]"]:checked');
        if (checked.length > 5) {
            this.checked = false;
            alert('You can select a maximum of 5 participants.');
        }
    });
});
</script>
@endpush
@endsection
