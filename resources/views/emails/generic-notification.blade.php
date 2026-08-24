<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; margin: 0; padding: 40px 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background: #9f5124; padding: 24px 32px; }
        .header h1 { color: #ffffff; font-size: 20px; margin: 0; font-weight: 600; }
        .body { padding: 32px; }
        .title { font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 12px; }
        .message { font-size: 15px; color: #475569; line-height: 1.6; margin: 0 0 24px; }
        .btn { display: inline-block; background: #9f5124; color: #ffffff !important; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 500; font-size: 14px; }
        .footer { padding: 24px 32px; border-top: 1px solid #e2e8f0; text-align: center; }
        .footer p { font-size: 12px; color: #94a3b8; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SUPPLY4ME</h1>
        </div>
        <div class="body">
            <h2 class="title">{{ $data['title'] }}</h2>
            <p class="message">{{ $data['message'] }}</p>
            @if(!empty($data['action_url']))
                <a href="{{ $data['action_url'] }}" class="btn">{{ $data['action_label'] ?? 'View' }}</a>
            @endif
        </div>
        <div class="footer">
            <p>You are receiving this because you have an account on SUPPLY4ME.</p>
        </div>
    </div>
</body>
</html>
