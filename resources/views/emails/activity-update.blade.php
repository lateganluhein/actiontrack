<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activity Update</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #d4a574; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #1a1a1a; }
        h1 { color: #1a1a1a; font-size: 22px; margin: 10px 0 0; }
        .role-badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; background: #d4a574; color: #fff; margin-top: 10px; }
        .activity-details { background: #f8f8f8; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .detail-row { margin: 10px 0; }
        .detail-label { font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase; }
        .detail-value { color: #1a1a1a; margin-top: 4px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-overdue { background: #fee2e2; color: #dc2626; }
        .badge-urgent { background: #ffedd5; color: #ea580c; }
        .badge-soon { background: #dbeafe; color: #2563eb; }
        .custom-message { background: #fffbeb; border-left: 4px solid #d4a574; padding: 15px; margin: 20px 0; }
        .custom-message-label { font-weight: 600; color: #92400e; font-size: 12px; text-transform: uppercase; margin-bottom: 8px; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">⚡ ActionTrack</div>
            <h1>{{ $activity->name }}</h1>
            <span class="role-badge">You are the {{ $role }}</span>
        </div>

        <p>Hi {{ $recipient->first_name }},</p>
        <p>Here's an update on an activity you're involved in:</p>

        @if($customMessage)
        <div class="custom-message">
            <div class="custom-message-label">Message</div>
            <p style="margin: 0; white-space: pre-wrap;">{{ $customMessage }}</p>
        </div>
        @endif

        <div class="activity-details">
            <div class="detail-row">
                <div class="detail-label">Activity</div>
                <div class="detail-value">{{ $activity->name }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Status</div>
                <div class="detail-value">{{ $activity->status_label }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Due Date</div>
                <div class="detail-value">
                    @if($activity->due_date)
                        {{ $activity->due_date->format('d M Y') }}
                        @if($activity->is_overdue)
                            <span class="badge badge-overdue">{{ abs($activity->days_until_due) }} day(s) overdue</span>
                        @elseif($activity->days_until_due <= 2)
                            <span class="badge badge-urgent">{{ $activity->days_until_due === 0 ? 'Due today' : $activity->days_until_due . ' day(s) left' }}</span>
                        @elseif($activity->days_until_due <= 7)
                            <span class="badge badge-soon">{{ $activity->days_until_due }} days left</span>
                        @endif
                    @else
                        No due date set
                    @endif
                </div>
            </div>

            @if($activity->lead)
            <div class="detail-row">
                <div class="detail-label">Lead</div>
                <div class="detail-value">{{ $activity->lead->full_name }}</div>
            </div>
            @endif

            @if($activity->next_step)
            <div class="detail-row">
                <div class="detail-label">Next Step</div>
                <div class="detail-value">{{ $activity->next_step }}</div>
            </div>
            @endif
        </div>

        <div class="footer">
            <p>This is an automated email from ActionTrack.</p>
            <p>&copy; {{ date('Y') }} ManyCents</p>
        </div>
    </div>
</body>
</html>
