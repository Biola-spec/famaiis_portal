<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { background: #0a1f44; color: #fff; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 30px; }
        .footer { text-align: center; font-size: 0.8rem; color: #888; padding: 20px; }
        .btn { display: inline-block; padding: 12px 24px; background: #1e8fc4; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .event-info { background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Event Reminder</h1>
        </div>
        <div class="content">
            <h2>Hello {{ $registration->name }},</h2>
            <p>This is a friendly reminder that the event you registered for, <strong>{{ $event->title }}</strong>, is <strong>{{ $daysToGo }} day(s)</strong> away!</p>
            
            <div class="event-info">
                <p><strong>🗓️ Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('d F, Y') }} {{ $event->event_time ? 'at ' . \Carbon\Carbon::parse($event->event_time)->format('h:i A') : '' }}</p>
                @if($event->location)
                    <p><strong>📍 Location:</strong> {{ $event->location }}</p>
                @endif
            </div>

            <p>We look forward to seeing you there!</p>
            
            <p>If you have any questions, feel free to contact us via WhatsApp or reply to this email.</p>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/') }}" class="btn">Visit School Website</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} FAMA Islamic International School. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
