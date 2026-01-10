ActionTrack - Activity Update
=============================

Hi {{ $recipient->first_name }},

Here's an update on an activity you're involved in:

@if($customMessage)
MESSAGE
-------
{{ $customMessage }}

@endif
ACTIVITY DETAILS
----------------
Activity: {{ $activity->name }}
Your Role: {{ $role }}
Status: {{ $activity->status_label }}
@if($activity->due_date)
Due Date: {{ $activity->due_date->format('d M Y') }}@if($activity->is_overdue) ({{ abs($activity->days_until_due) }} day(s) overdue)@elseif($activity->days_until_due <= 2) ({{ $activity->days_until_due === 0 ? 'Due today' : $activity->days_until_due . ' day(s) left' }})@elseif($activity->days_until_due <= 7) ({{ $activity->days_until_due }} days left)@endif

@else
Due Date: No due date set
@endif
@if($activity->lead)
Lead: {{ $activity->lead->full_name }}
@endif
@if($activity->next_step)
Next Step: {{ $activity->next_step }}
@endif

--
This is an automated email from ActionTrack.
(c) {{ date('Y') }} ManyCents
