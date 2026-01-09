@extends('layouts.app')

@section('title', 'Activities')

@section('content')
<div class="page-header">
    <h1 class="page-title">Activities</h1>
    <a href="{{ route('activities.create') }}" class="btn btn-primary">+ New Activity</a>
</div>

<!-- Filters -->
<div class="filters">
    <form method="GET" action="{{ route('activities.index') }}" class="filter-form">
        <div class="filter-group">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search activities..."
                   class="filter-input">
        </div>
        <div class="filter-group">
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('activities.index') }}" class="btn btn-link">Clear</a>
        @endif
    </form>
</div>

<!-- Activities Table -->
@if($activities->isNotEmpty())
<div class="activity-table-container">
    <table class="activity-table">
        <thead>
            <tr>
                <th>Activity</th>
                <th>Lead</th>
                <th>Participants</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $activity)
            <tr class="row-{{ $activity->urgency_level ?? 'normal' }}">
                <td>
                    <a href="{{ route('activities.edit', $activity) }}" class="activity-name">
                        {{ $activity->name }}
                    </a>
                </td>
                <td>
                    @if($activity->lead)
                        <a href="{{ route('people.show', $activity->lead) }}">
                            {{ $activity->lead->full_name }}
                        </a>
                    @else
                        <span class="text-muted">No lead</span>
                    @endif
                </td>
                <td>
                    @if($activity->parties->isNotEmpty())
                        <span class="participant-count" title="{{ $activity->parties->pluck('full_name')->join(', ') }}">
                            {{ $activity->parties->count() }} participant{{ $activity->parties->count() !== 1 ? 's' : '' }}
                        </span>
                    @else
                        <span class="text-muted">None</span>
                    @endif
                </td>
                <td>
                    {!! $activity->urgency_badge !!}
                </td>
                <td>
                    <span class="status-badge status-{{ $activity->status }}">
                        {{ $activity->status_label }}
                    </span>
                </td>
                <td class="actions-cell">
                    <button class="btn btn-sm btn-view" onclick="openActivityModal({{ $activity->id }})">
                        View
                    </button>
                    <a href="{{ route('activities.edit', $activity) }}" class="btn btn-sm btn-edit">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('activities.destroy', $activity) }}"
                          class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this activity?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-container">
    {{ $activities->withQueryString()->links() }}
</div>
@else
<div class="empty-state">
    <span class="empty-icon">📋</span>
    <h3>No activities found</h3>
    @if(request()->hasAny(['search', 'status']))
        <p>Try adjusting your filters or search term.</p>
        <a href="{{ route('activities.index') }}" class="btn btn-secondary">Clear Filters</a>
    @else
        <p>Get started by creating your first activity.</p>
        <a href="{{ route('activities.create') }}" class="btn btn-primary">Create Activity</a>
    @endif
</div>
@endif

<!-- Activity Modal -->
@include('activities._modal')
@endsection
