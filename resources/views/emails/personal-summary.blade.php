<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Activity Summary</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #d4a574; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #1a1a1a; }
        h1 { color: #1a1a1a; font-size: 24px; margin: 10px 0 0; }
        h2 { color: #1a1a1a; font-size: 18px; margin: 25px 0 15px; padding-bottom: 8px; border-bottom: 1px solid #eee; }
        .activity-list { list-style: none; padding: 0; margin: 0; }
        .activity-item { padding: 12px; border-bottom: 1px solid #eee; }
        .activity-item:last-child { border-bottom: none; }
        .activity-name { font-weight: 600; color: #1a1a1a; }
        .activity-meta { font-size: 13px; color: #666; margin-top: 4px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-overdue { background: #fee2e2; color: #dc2626; }
        .badge-urgent { background: #ffedd5; color: #ea580c; }
        .badge-soon { background: #dbeafe; color: #2563eb; }
        .badge-normal { background: #f3f4f6; color: #6b7280; }
        .empty-message { color: #666; font-style: italic; padding: 15px; text-align: center; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">⚡ ActionTrack</div>
            <h1>Your Activity Summary</h1>
        </div>

        <p>Hi {{ $person->first_name }},</p>
        <p>Here's a summary of your activities:</p>

        @if($asLead->isNotEmpty())
        <h2>📋 Activities You Lead ({{ $asLead->count() }})</h2>
        <ul class="activity-list">
            @foreach($asLead as $activity)
            <li class="activity-item">
                <div class="activity-name">{{ $activity->name }}</div>
                <div class="activity-meta">
                    @if($activity->is_overdue)
                        <span class="badge badge-overdue">{{ abs($activity->days_until_due) }} day(s) overdue</span>
                    @elseif($activity->days_until_due !== null && $activity->days_until_due <= 2)
                        <span class="badge badge-urgent">{{ $activity->days_until_due === 0 ? 'Due today' : $activity->days_until_due . ' day(s) left' }}</span>
                    @elseif($activity->days_until_due !== null && $activity->days_until_due <= 7)
                        <span class="badge badge-soon">{{ $activity->days_until_due }} days left</span>
                    @elseif($activity->due_date)
                        <span class="badge badge-normal">Due: {{ $activity->due_date->format('d M Y') }}</span>
                    @else
                        <span class="badge badge-normal">No due date</span>
                    @endif
                </div>
                @if($activity->next_step)
                <div class="activity-meta" style="margin-top: 8px;">
                    <strong>Next step:</strong> {{ Str::limit($activity->next_step, 100) }}
                </div>
                @endif
            </li>
            @endforeach
        </ul>
        @endif

        @if($asParty->isNotEmpty())
        <h2>👥 Activities You Participate In ({{ $asParty->count() }})</h2>
        <ul class="activity-list">
            @foreach($asParty as $activity)
            <li class="activity-item">
                <div class="activity-name">{{ $activity->name }}</div>
                <div class="activity-meta">
                    @if($activity->is_overdue)
                        <span class="badge badge-overdue">{{ abs($activity->days_until_due) }} day(s) overdue</span>
                    @elseif($activity->days_until_due !== null && $activity->days_until_due <= 7)
                        <span class="badge badge-soon">{{ $activity->days_until_due }} days left</span>
                    @elseif($activity->due_date)
                        Due: {{ $activity->due_date->format('d M Y') }}
                    @endif
                    @if($activity->lead)
                        &middot; Lead: {{ $activity->lead->full_name }}
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
        @endif

        @if($totalCount === 0)
        <p class="empty-message">You have no active activities at this time.</p>
        @endif

        <div class="footer">
            <p>This is an automated email from ActionTrack.</p>
            <p>&copy; {{ date('Y') }} ManyCents</p>
        </div>
    </div>
</body>
</html>
