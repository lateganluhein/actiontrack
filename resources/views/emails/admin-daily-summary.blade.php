<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ActionTrack Daily Statistics</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .container { background: #fff; border-radius: 8px; padding: 30px; }
        .header { text-align: center; margin-bottom: 30px; background: #1a1a1a; margin: -30px -30px 30px; padding: 30px; border-radius: 8px 8px 0 0; }
        .logo { font-size: 24px; font-weight: bold; color: #d4a574; }
        h1 { color: #fff; font-size: 24px; margin: 10px 0 0; }
        .subtitle { color: #ccc; }
        h2 { color: #1a1a1a; font-size: 18px; border-bottom: 1px solid #eee; padding-bottom: 8px; }      
        .stat-grid { display: flex; flex-wrap: wrap; gap: 15px; margin: 20px 0; }
        .stat-box { background: #f8f8f8; padding: 15px; border-radius: 8px; text-align: center; min-width: 100px; }
        .stat-number { font-size: 28px; font-weight: bold; display: block; }
        .stat-label { font-size: 12px; color: #666; }
        .red { color: #dc2626; }
        .orange { color: #ea580c; }
        .blue { color: #3b82f6; }
        .green { color: #10b981; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f8f8; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">ActionTrack</div>
            <h1>Daily Statistics Report</h1>
            <p class="subtitle">{{ $stats['date'] }}</p>
        </div>

        <h2>User Statistics</h2>
        <div class="stat-grid">
            <div class="stat-box">
                <span class="stat-number blue">{{ $stats['users']['total'] }}</span>
                <span class="stat-label">Total Users</span>
            </div>
            <div class="stat-box">
                <span class="stat-number green">{{ $stats['users']['active'] }}</span>
                <span class="stat-label">Active Users</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">{{ $stats['users']['new_this_week'] }}</span>
                <span class="stat-label">New This Week</span>
            </div>
        </div>

        <h2>Activity Statistics</h2>
        <div class="stat-grid">
            <div class="stat-box">
                <span class="stat-number red">{{ $stats['activities']['overdue'] }}</span>
                <span class="stat-label">Overdue</span>
            </div>
            <div class="stat-box">
                <span class="stat-number orange">{{ $stats['activities']['due_soon'] }}</span>
                <span class="stat-label">Due Soon</span>
            </div>
            <div class="stat-box">
                <span class="stat-number blue">{{ $stats['activities']['in_progress'] }}</span>
                <span class="stat-label">In Progress</span>
            </div>
            <div class="stat-box">
                <span class="stat-number green">{{ $stats['activities']['completed'] }}</span>
                <span class="stat-label">Completed</span>
            </div>
        </div>

        <h2>Per User Breakdown</h2>
        @if(count($stats['per_user']) > 0)
        <table>
            <tr><th>User</th><th>Total</th><th>Active</th><th>Overdue</th><th>Done</th></tr>
            @foreach($stats['per_user'] as $user)
            <tr>
                <td>{{ $user['name'] }}</td>
                <td>{{ $user['total'] }}</td>
                <td>{{ $user['in_progress'] }}</td>
                <td class="red">{{ $user['overdue'] }}</td>
                <td class="green">{{ $user['completed'] }}</td>
            </tr>
            @endforeach
        </table>
        @else
        <p>No user activity yet.</p>
        @endif

        <p><strong>Total Contacts:</strong> {{ $stats['contacts']['total'] }}</p>

        <div class="footer">
            <p>This is an automated statistics report. No activity details are shown for privacy.</p>    
            <p>&copy; {{ date('Y') }} ManyCents</p>
        </div>
    </div>
</body>
</html>
