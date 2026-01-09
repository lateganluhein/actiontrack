@extends('layouts.app')

@section('title', $person->full_name)

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $person->full_name }}</h1>
    <div class="page-actions">
        <a href="{{ route('people.index') }}" class="btn btn-secondary">Back to List</a>
        <a href="{{ route('people.edit', $person) }}" class="btn btn-primary">Edit Person</a>
    </div>
</div>

<div class="person-detail">
    <!-- Contact Information -->
    <div class="detail-card">
        <h3 class="detail-card-title">Contact Information</h3>
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Name</span>
                <span class="detail-value">{{ $person->full_name }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Company</span>
                <span class="detail-value">{{ $person->company ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Primary Email</span>
                <span class="detail-value">
                    @if($person->email_primary)
                        <a href="mailto:{{ $person->email_primary }}">{{ $person->email_primary }}</a>
                    @else
                        -
                    @endif
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Secondary Email</span>
                <span class="detail-value">
                    @if($person->email_secondary)
                        <a href="mailto:{{ $person->email_secondary }}">{{ $person->email_secondary }}</a>
                    @else
                        -
                    @endif
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Primary Phone</span>
                <span class="detail-value">
                    @if($person->phone_primary)
                        <a href="tel:{{ $person->phone_primary }}">{{ $person->phone_primary }}</a>
                    @else
                        -
                    @endif
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Secondary Phone</span>
                <span class="detail-value">{{ $person->phone_secondary ?? '-' }}</span>
            </div>
        </div>

        @if($person->email_primary)
        <div class="detail-actions">
            <button class="btn btn-secondary" onclick="sendSummary()">
                Send Activity Summary
            </button>
        </div>
        @endif
    </div>

    <!-- Activities as Lead -->
    <div class="detail-card">
        <h3 class="detail-card-title">
            Activities as Lead
            <span class="badge badge-neutral">{{ $activitiesAsLead->count() }}</span>
        </h3>
        @if($activitiesAsLead->isNotEmpty())
            <div class="activity-table-container">
                <table class="activity-table compact">
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activitiesAsLead as $activity)
                        <tr class="row-{{ $activity->urgency_level ?? 'normal' }}">
                            <td>
                                <a href="{{ route('activities.edit', $activity) }}">{{ $activity->name }}</a>
                            </td>
                            <td>{!! $activity->urgency_badge !!}</td>
                            <td>
                                <span class="status-badge status-{{ $activity->status }}">
                                    {{ $activity->status_label }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="empty-message">No activities as lead.</p>
        @endif
    </div>

    <!-- Activities as Participant -->
    <div class="detail-card">
        <h3 class="detail-card-title">
            Activities as Participant
            <span class="badge badge-neutral">{{ $activitiesAsParty->count() }}</span>
        </h3>
        @if($activitiesAsParty->isNotEmpty())
            <div class="activity-table-container">
                <table class="activity-table compact">
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Lead</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activitiesAsParty as $activity)
                        <tr class="row-{{ $activity->urgency_level ?? 'normal' }}">
                            <td>
                                <a href="{{ route('activities.edit', $activity) }}">{{ $activity->name }}</a>
                            </td>
                            <td>{{ $activity->lead?->full_name ?? '-' }}</td>
                            <td>{!! $activity->urgency_badge !!}</td>
                            <td>
                                <span class="status-badge status-{{ $activity->status }}">
                                    {{ $activity->status_label }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="empty-message">No activities as participant.</p>
        @endif
    </div>
</div>

@push('scripts')
<script>
function sendSummary() {
    if (!confirm('Send activity summary to {{ $person->email_primary }}?')) {
        return;
    }

    fetch('{{ route("people.send-summary", $person) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to send summary');
    });
}
</script>
@endpush
@endsection
