<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Message from ActionTrack</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #d4a574; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #1a1a1a; }
        .message-content { background: #f8f8f8; border-radius: 8px; padding: 20px; margin: 20px 0; white-space: pre-wrap; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">⚡ ActionTrack</div>
        </div>

        <p>Hi {{ $recipient->first_name }},</p>

        <div class="message-content">{{ $emailMessage }}</div>

        <div class="footer">
            <p>This email was sent via ActionTrack.</p>
            <p>&copy; {{ date('Y') }} ManyCents</p>
        </div>
    </div>
</body>
</html>
