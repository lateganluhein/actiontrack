ActionTrack - Your Activity Summary
====================================

Hi {{ $person->first_name }},

Here's a summary of your activities:

@if($asLead->isNotEmpty())
ACTIVITIES YOU LEAD ({{ $asLead->count() }})
---------------------------------------------
@foreach($asLead as $activity)
* {{ $activity->name }}
@if($activity->is_overdue)  - {{ abs($activity->days_until_due) }} day(s) overdue
@elseif($activity->days_until_due !== null && $activity->days_until_due <= 2)  - {{ $activity->days_until_due === 0 ? 'Due today' : $activity->days_until_due . ' day(s) left' }}
@elseif($activity->days_until_due !== null && $activity->days_until_due <= 7)  - {{ $activity->days_until_due }} days left
@elseif($activity->due_date)  - Due: {{ $activity->due_date->format('d M Y') }}
@else  - No due date
@endif
@if($activity->next_step)  - Next step: {{ Str::limit($activity->next_step, 100) }}
@endif

@endforeach
@endif
@if($asParty->isNotEmpty())
ACTIVITIES YOU PARTICIPATE IN ({{ $asParty->count() }})
-------------------------------------------------------
@foreach($asParty as $activity)
* {{ $activity->name }}
@if($activity->is_overdue)  - {{ abs($activity->days_until_due) }} day(s) overdue
@elseif($activity->days_until_due !== null && $activity->days_until_due <= 7)  - {{ $activity->days_until_due }} days left
@elseif($activity->due_date)  - Due: {{ $activity->due_date->format('d M Y') }}
@endif
@if($activity->lead)  - Lead: {{ $activity->lead->full_name }}
@endif

@endforeach
@endif
@if($totalCount === 0)
You have no active activities at this time.
@endif

--
This is an automated email from ActionTrack.
(c) {{ date('Y') }} ManyCents
