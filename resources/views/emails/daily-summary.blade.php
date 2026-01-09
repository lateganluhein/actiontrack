<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Activity Summary</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #d4a574; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #1a1a1a; }
        .logo-icon { margin-right: 8px; }
        h1 { color: #1a1a1a; font-size: 24px; margin: 0; }
        h2 { color: #1a1a1a; font-size: 18px; margin: 25px 0 15px; padding-bottom: 8px; border-bottom: 1px solid #eee; }
        .stats { display: flex; justify-content: space-around; text-align: center; margin: 20px 0; padding: 15px; background: #f8f8f8; border-radius: 8px; }
        .stat { padding: 0 15px; }
        .stat-number { font-size: 28px; font-weight: bold; display: block; }
        .stat-label { font-size: 12px; color: #666; text-transform: uppercase; }
        .stat-overdue .stat-number { color: #ef4444; }
        .stat-due-soon .stat-number { color: #f59e0b; }
        .stat-in-progress .stat-number { color: #3b82f6; }
        .activity-list { list-style: none; padding: 0; margin: 0; }
        .activity-item { padding: 12px; border-bottom: 1px solid #eee; }
        .activity-item:last-child { border-bottom: none; }
        .activity-name { font-weight: 600; color: #1a1a1a; }
        .activity-meta { font-size: 13px; color: #666; margin-top: 4px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-overdue { background: #fee2e2; color: #dc2626; }
        .badge-urgent { background: #ffedd5; color: #ea580c; }
        .badge-soon { background: #dbeafe; color: #2563eb; }
        .section-overdue h2 { color: #dc2626; }
        .section-due-soon h2 { color: #ea580c; }
        .empty-message { color: #666; font-style: italic; padding: 15px; text-align: center; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><span class="logo-icon">⚡</span>ActionTrack</div>
            <h1>Daily Activity Summary</h1>
            <p style="color: #666; margin: 10px 0 0;">{{ now()->format('l, d F Y') }}</p>
        </div>

        <p>Hi {{ $user->name }},</p>
        <p>Here's your activity summary for today:</p>

        <!-- Stats -->
        <div class="stats">
            <div class="stat stat-overdue">
                <span class="stat-number">{{ $overdue->count() }}</span>
                <span class="stat-label">Overdue</span>
            </div>
            <div class="stat stat-due-soon">
                <span class="stat-number">{{ $dueSoon->count() }}</span>
                <span class="stat-label">Due Soon</span>
            </div>
            <div class="stat stat-in-progress">
                <span class="stat-number">{{ $inProgress->count() }}</span>
                <span class="stat-label">In Progress</span>
            </div>
        </div>

        <!-- Overdue Activities -->
        @if($overdue->isNotEmpty())
        <div class="section-overdue">
            <h2>🚨 Overdue Activities</h2>
            <ul class="activity-list">
                @foreach($overdue as $activity)
                <li class="activity-item">
                    <div class="activity-name">{{ $activity->name }}</div>
                    <div class="activity-meta">
                        <span class="badge badge-overdue">{{ abs($activity->days_until_due) }} day(s) overdue</span>
                        @if($activity->lead)
                            &middot; Lead: {{ $activity->lead->full_name }}
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Due Soon Activities -->
        @if($dueSoon->isNotEmpty())
        <div class="section-due-soon">
            <h2>⏰ Due Soon</h2>
            <ul class="activity-list">
                @foreach($dueSoon as $activity)
                <li class="activity-item">
                    <div class="activity-name">{{ $activity->name }}</div>
                    <div class="activity-meta">
                        @if($activity->days_until_due === 0)
                            <span class="badge badge-urgent">Due today</span>
                        @elseif($activity->days_until_due <= 2)
                            <span class="badge badge-urgent">{{ $activity->days_until_due }} day(s) left</span>
                        @else
                            <span class="badge badge-soon">{{ $activity->days_until_due }} days left</span>
                        @endif
                        @if($activity->lead)
                            &middot; Lead: {{ $activity->lead->full_name }}
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- All In Progress -->
        @if($inProgress->isNotEmpty() && $overdue->isEmpty() && $dueSoon->isEmpty())
        <div>
            <h2>🔄 In Progress</h2>
            <ul class="activity-list">
                @foreach($inProgress->take(10) as $activity)
                <li class="activity-item">
                    <div class="activity-name">{{ $activity->name }}</div>
                    <div class="activity-meta">
                        @if($activity->due_date)
                            Due: {{ $activity->due_date->format('d M Y') }}
                        @else
                            No due date
                        @endif
                        @if($activity->lead)
                            &middot; Lead: {{ $activity->lead->full_name }}
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
            @if($inProgress->count() > 10)
                <p style="color: #666; font-size: 13px; text-align: center;">
                    And {{ $inProgress->count() - 10 }} more activities...
                </p>
            @endif
        </div>
        @endif

        @if($totalCount === 0)
        <p class="empty-message">No active activities. Enjoy your day!</p>
        @endif

        <div class="footer">
            <p>This is an automated email from ActionTrack.</p>
            <p>&copy; {{ date('Y') }} ManyCents</p>
        </div>
    </div>
</body>
</html>
