<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ 'SUPPLY4ME' }}</title>
    <link rel="icon" type="image/png" href="/images/logo_dark.png" />

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#9F5124">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Supply4Me">
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
    <meta name="description" content="Nigeria's trusted B2B platform for quality FMCG products">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-gray-50">
    <script>
        window.Ziggy = @json((new \Tighten\Ziggy\Ziggy)->toArray(), JSON_UNESCAPED_SLASHES);
    </script>
    @inertia
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW registered:', reg.scope))
                    .catch(err => console.log('SW registration failed:', err));
            });
        }

        // Redirect to /shop when launched as PWA from home screen
        if (window.matchMedia('(display-mode: standalone)').matches && window.location.pathname === '/') {
            window.location.href = '/shop';
        }
    </script>
</body>
</html>
