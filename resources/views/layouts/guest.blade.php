<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ActionTrack') }} - @yield('title', 'Login')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="guest-body">
    <div class="guest-container">
        <div class="guest-card">
            <div class="guest-logo">
                <span class="logo-icon">⚡</span>
                <span class="logo-text">ActionTrack</span>
            </div>

            @yield('content')
        </div>

        <footer class="guest-footer">
            <p>&copy; {{ date('Y') }} ManyCents - ActionTrack</p>
        </footer>
    </div>
</body>
</html>
