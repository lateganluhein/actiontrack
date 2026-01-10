ActionTrack - Daily Activity Summary
=====================================
{{ now()->format('l, d F Y') }}

Hi {{ $user->name }},

Here's your activity summary for today:

SUMMARY
-------
Overdue: {{ $overdue->count() }}
Due Soon: {{ $dueSoon->count() }}
In Progress: {{ $inProgress->count() }}

@if($overdue->isNotEmpty())
OVERDUE ACTIVITIES
------------------
@foreach($overdue as $activity)
* {{ $activity->name }}
  - {{ abs($activity->days_until_due) }} day(s) overdue
@if($activity->lead)  - Lead: {{ $activity->lead->full_name }}
@endif

@endforeach
@endif
@if($dueSoon->isNotEmpty())
DUE SOON
--------
@foreach($dueSoon as $activity)
* {{ $activity->name }}
@if($activity->days_until_due === 0)  - Due today
@elseif($activity->days_until_due <= 2)  - {{ $activity->days_until_due }} day(s) left
@else  - {{ $activity->days_until_due }} days left
@endif
@if($activity->lead)  - Lead: {{ $activity->lead->full_name }}
@endif

@endforeach
@endif
@if($inProgress->isNotEmpty() && $overdue->isEmpty() && $dueSoon->isEmpty())
IN PROGRESS
-----------
@foreach($inProgress->take(10) as $activity)
* {{ $activity->name }}
@if($activity->due_date)  - Due: {{ $activity->due_date->format('d M Y') }}
@else  - No due date
@endif
@if($activity->lead)  - Lead: {{ $activity->lead->full_name }}
@endif

@endforeach
@if($inProgress->count() > 10)
And {{ $inProgress->count() - 10 }} more activities...
@endif
@endif
@if($totalCount === 0)
No active activities. Enjoy your day!
@endif

--
This is an automated email from ActionTrack.
(c) {{ date('Y') }} ManyCents
