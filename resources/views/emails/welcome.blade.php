<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to ActionTrack</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #d4a574; padding-bottom: 20px; }
        .logo { font-size: 28px; font-weight: bold; color: #1a1a1a; }
        h1 { color: #1a1a1a; font-size: 24px; margin: 20px 0; }
        .warning-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .warning-title { font-weight: bold; color: #856404; margin-bottom: 8px; }
        .warning-text { color: #856404; }
        .features { background: #f8f8f8; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .features h3 { margin-top: 0; color: #1a1a1a; }
        .features ul { margin: 0; padding-left: 20px; }
        .features li { margin: 8px 0; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
        .afrikaans { font-style: italic; color: #d4a574; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">ActionTrack</div>
        </div>

        <h1>Welcome, {{ $user->name }}!</h1>

        <p>We trust your daily productivity will skyrocket with ActionTrack!</p>

        <div class="features">
            <h3>What you can do:</h3>
            <ul>
                <li>Track activities and tasks with due dates</li>
                <li>Manage your contacts and team members</li>
                <li>Receive daily summary emails at 07:00</li>
                <li>Send broadcast messages to your team</li>
            </ul>
        </div>

        <div class="warning-box">
            <div class="warning-title">Important - Save Your Password!</div>
            <div class="warning-text">
                Please store your login details safely. Due to our strict security measures, we cannot recover or reset passwords. If you lose access, your entire profile must be deleted.
            </div>
        </div>

        <p class="afrikaans">Lekker werk!</p>

        <p>The ActionTrack Team</p>

        <div class="footer">
            <p>This is an automated email from ActionTrack.</p>
            <p>&copy; {{ date('Y') }} ManyCents</p>
        </div>
    </div>
</body>
</html>
