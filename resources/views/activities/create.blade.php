@extends('layouts.app')

@section('title', 'New Activity')

@section('content')
<div class="page-header">
    <h1 class="page-title">New Activity</h1>
    <a href="{{ route('activities.index') }}" class="btn btn-secondary">Cancel</a>
</div>

<div class="form-container">
    <form method="POST" action="{{ route('activities.store') }}" class="activity-form">
        @csrf

        <!-- Activity Name -->
        <div class="form-group">
            <label for="name" class="form-label required">Activity Name</label>
            <input type="text"
                   id="name"
                   name="name"
                   value="{{ old('name') }}"
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
                       value="{{ old('start_date', date('Y-m-d')) }}"
                       class="form-input @error('start_date') is-invalid @enderror"
                       required
                       onchange="updateDueDate()">
                @error('start_date')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date"
                       id="due_date"
                       name="due_date"
                       value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}"
                       class="form-input @error('due_date') is-invalid @enderror">
                @error('due_date')
                    <span class="form-error">{{ $message }}</span>
                @enderror
                <small class="form-hint">Auto-set to 30 days after start date</small>
            </div>
        </div>

        <!-- Lead and Status Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="lead_id" class="form-label required">Lead Person</label>
                <select id="lead_id"
                        name="lead_id"
                        class="form-select @error('lead_id') is-invalid @enderror"
                        required>
                    <option value="">Select lead person...</option>
                    @foreach($people as $person)
                        <option value="{{ $person->id }}" {{ old('lead_id') == $person->id ? 'selected' : '' }}>
                            {{ $person->full_name }}
                            @if($person->company) ({{ $person->company }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('lead_id')
                    <span class="form-error">{{ $message }}</span>
                @enderror
                @if($people->isEmpty())
                    <small class="form-hint">
                        <a href="{{ route('people.create') }}">Add a person first</a>
                    </small>
                @endif
            </div>

            <div class="form-group">
                <label for="status" class="form-label required">Status</label>
                <select id="status"
                        name="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required>
                    <option value="in_progress" {{ old('status', 'in_progress') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
            <div class="participant-list" id="participantList">
                @foreach($people as $person)
                    <label class="participant-item" data-name="{{ strtolower($person->full_name) }}">
                        <input type="checkbox"
                               name="parties[]"
                               value="{{ $person->id }}"
                               {{ in_array($person->id, old('parties', [])) ? 'checked' : '' }}>
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
                      placeholder="Enter background information or context...">{{ old('logic') }}</textarea>
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
                      placeholder="What needs to happen next?">{{ old('next_step') }}</textarea>
            @error('next_step')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">Create Activity</button>
            <a href="{{ route('activities.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function updateDueDate() {
    const startDate = document.getElementById('start_date').value;
    if (startDate) {
        const date = new Date(startDate);
        date.setDate(date.getDate() + 30);
        document.getElementById('due_date').value = date.toISOString().split('T')[0];
    }
}

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
