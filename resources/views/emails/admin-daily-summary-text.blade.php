ManyCents Resources - Master Activity Summary
=============================================
{{ now()->format('l, d F Y') }}

This is the daily master summary of all activities across all users.

SUMMARY
-------
Overdue: {{ $overdue->count() }}
Due Soon: {{ $dueSoon->count() }}
In Progress: {{ $inProgress->count() }}
Users: {{ $users->count() }}

@if($overdue->isNotEmpty())
OVERDUE ACTIVITIES ({{ $overdue->count() }})
--------------------------------------------
@foreach($overdue as $activity)
* {{ $activity->name }}
  - {{ abs($activity->days_until_due) }} day(s) overdue
@if($activity->lead)  - Lead: {{ $activity->lead->full_name }}
@endif
@if($activity->user)  - User: {{ $activity->user->name }}
@endif

@endforeach
@endif
@if($dueSoon->isNotEmpty())
DUE SOON ({{ $dueSoon->count() }})
----------------------------------
@foreach($dueSoon as $activity)
* {{ $activity->name }}
@if($activity->days_until_due === 0)  - Due today
@elseif($activity->days_until_due <= 2)  - {{ $activity->days_until_due }} day(s) left
@else  - {{ $activity->days_until_due }} days left
@endif
@if($activity->lead)  - Lead: {{ $activity->lead->full_name }}
@endif
@if($activity->user)  - User: {{ $activity->user->name }}
@endif

@endforeach
@endif
@if($inProgress->isNotEmpty() && $overdue->isEmpty() && $dueSoon->isEmpty())
IN PROGRESS ({{ $inProgress->count() }})
----------------------------------------
@foreach($inProgress->take(15) as $activity)
* {{ $activity->name }}
@if($activity->due_date)  - Due: {{ $activity->due_date->format('d M Y') }}
@else  - No due date
@endif
@if($activity->lead)  - Lead: {{ $activity->lead->full_name }}
@endif
@if($activity->user)  - User: {{ $activity->user->name }}
@endif

@endforeach
@if($inProgress->count() > 15)
And {{ $inProgress->count() - 15 }} more activities...
@endif
@endif
@if($totalCount === 0)
No active activities across all users.
@endif

--
This is an automated master summary from ActionTrack.
(c) {{ date('Y') }} ManyCents
