<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'SUPPLY4ME') }}</title>
    <link rel="icon" type="image/png" href="/images/logo_dark.png" />
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-gray-50">
    <script>
        window.Ziggy = @json((new \Tighten\Ziggy\Ziggy)->toArray(), JSON_UNESCAPED_SLASHES);
    </script>
    @inertia
</body>
</html>
