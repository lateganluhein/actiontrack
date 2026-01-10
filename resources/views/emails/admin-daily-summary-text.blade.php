ActionTrack - Daily Statistics Report
=====================================
{{ $stats['date'] }}

USER STATISTICS
---------------
Total Users: {{ $stats['users']['total'] }}
Active Users: {{ $stats['users']['active'] }}
New This Week: {{ $stats['users']['new_this_week'] }}

ACTIVITY STATISTICS
-------------------
Overdue: {{ $stats['activities']['overdue'] }}
Due Soon: {{ $stats['activities']['due_soon'] }}
In Progress: {{ $stats['activities']['in_progress'] }}
Completed: {{ $stats['activities']['completed'] }}

PER USER BREAKDOWN
------------------
@forelse($stats['per_user'] as $user)
{{ $user['name'] }}: {{ $user['total'] }} total, {{ $user['in_progress'] }} active, {{ $user['overdue'] }} overdue
@empty
No user activity yet.
@endforelse

Total Contacts: {{ $stats['contacts']['total'] }}

--
This is an automated statistics report.
No activity details are shown for privacy.
(c) {{ date('Y') }} ManyCents
