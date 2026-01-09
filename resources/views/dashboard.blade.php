@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard">
    <h1 class="page-title">Dashboard</h1>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-overdue">
            <span class="stat-number">{{ $stats['overdue_count'] }}</span>
            <span class="stat-label">Overdue</span>
        </div>
        <div class="stat-card stat-due-soon">
            <span class="stat-number">{{ $stats['due_soon_count'] }}</span>
            <span class="stat-label">Due Next 7 Days</span>
        </div>
        <div class="stat-card stat-in-progress">
            <span class="stat-number">{{ $stats['in_progress_count'] }}</span>
            <span class="stat-label">In Progress</span>
        </div>
        <div class="stat-card stat-completed">
            <span class="stat-number">{{ $stats['completed_count'] }}</span>
            <span class="stat-label">Completed</span>
        </div>
    </div>

    <!-- Overdue Activities -->
    @if($overdue->isNotEmpty())
    <section class="activity-section">
        <h2 class="section-title section-title-overdue">
            <span class="section-icon">🚨</span> Overdue Activities
        </h2>
        <div class="activity-table-container">
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>Activity</th>
                        <th>Lead</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overdue as $activity)
                    <tr class="row-overdue">
                        <td>
                            <a href="{{ route('activities.edit', $activity) }}" class="activity-name">
                                {{ $activity->name }}
                            </a>
                        </td>
                        <td>{{ $activity->lead?->full_name ?? 'No lead' }}</td>
                        <td>
                            {!! $activity->urgency_badge !!}
                        </td>
                        <td>
                            <span class="status-badge status-{{ $activity->status }}">
                                {{ $activity->status_label }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-view" onclick="openActivityModal({{ $activity->id }})">
                                View
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    <!-- Due Soon Activities -->
    @if($dueSoon->isNotEmpty())
    <section class="activity-section">
        <h2 class="section-title section-title-due-soon">
            <span class="section-icon">⏰</span> Due Soon (Next 7 Days)
        </h2>
        <div class="activity-table-container">
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>Activity</th>
                        <th>Lead</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dueSoon as $activity)
                    <tr class="row-{{ $activity->urgency_level }}">
                        <td>
                            <a href="{{ route('activities.edit', $activity) }}" class="activity-name">
                                {{ $activity->name }}
                            </a>
                        </td>
                        <td>{{ $activity->lead?->full_name ?? 'No lead' }}</td>
                        <td>
                            {!! $activity->urgency_badge !!}
                        </td>
                        <td>
                            <span class="status-badge status-{{ $activity->status }}">
                                {{ $activity->status_label }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-view" onclick="openActivityModal({{ $activity->id }})">
                                View
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    <!-- In Progress Activities -->
    @if($inProgress->isNotEmpty())
    <section class="activity-section">
        <h2 class="section-title section-title-in-progress">
            <span class="section-icon">🔄</span> In Progress
        </h2>
        <div class="activity-table-container">
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>Activity</th>
                        <th>Lead</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inProgress as $activity)
                    <tr class="row-{{ $activity->urgency_level ?? 'normal' }}">
                        <td>
                            <a href="{{ route('activities.edit', $activity) }}" class="activity-name">
                                {{ $activity->name }}
                            </a>
                        </td>
                        <td>{{ $activity->lead?->full_name ?? 'No lead' }}</td>
                        <td>
                            {!! $activity->urgency_badge !!}
                        </td>
                        <td>
                            <button class="btn btn-sm btn-view" onclick="openActivityModal({{ $activity->id }})">
                                View
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    <!-- Empty State -->
    @if($overdue->isEmpty() && $dueSoon->isEmpty() && $inProgress->isEmpty())
    <div class="empty-state">
        <span class="empty-icon">📋</span>
        <h3>No activities yet</h3>
        <p>Get started by creating your first activity.</p>
        <a href="{{ route('activities.create') }}" class="btn btn-primary">Create Activity</a>
    </div>
    @endif
</div>

<!-- Activity Modal -->
@include('activities._modal')
@endsection
