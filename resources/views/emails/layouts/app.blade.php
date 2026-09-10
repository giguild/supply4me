<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUPPLY4ME</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; margin: 0; padding: 40px 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background: #ffffff; padding: 24px 32px; border-bottom: 3px solid #9f5124; }
        .header img { display: inline-block; max-width: 180px; height: auto; max-height: 56px; }
        .body { padding: 32px; }
        .body > :first-child { margin-top: 0; }
        .title { font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 12px; }
        .message { font-size: 15px; color: #475569; line-height: 1.6; margin: 0 0 20px; }
        .detail-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; }
        .detail-value { font-size: 15px; color: #1e293b; margin: 0 0 14px; }
        .detail-value.amount { font-size: 22px; font-weight: 700; }
        .items { width: 100%; border-collapse: collapse; margin: 16px 0 24px; font-size: 13px; }
        .items th { text-align: left; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
        .items td { padding: 8px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .items td.num, .items th.num { text-align: right; }
        .btn { display: inline-block; background: #9f5124; color: #ffffff !important; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 500; font-size: 14px; }
        .footer { padding: 24px 32px; border-top: 1px solid #e2e8f0; text-align: center; }
        .footer p { font-size: 12px; color: #94a3b8; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header" align="left">
            <img src="https://supply4me.ng/images/logo_dark.png" alt="SUPPLY4ME" />
        </div>
        <div class="body">
            @yield('content')
        </div>
        <div class="footer">
            <p>@yield('footer-text', 'You are receiving this because you have an account on SUPPLY4ME.')</p>
        </div>
    </div>
</body>
</html>