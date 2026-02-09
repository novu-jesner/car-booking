<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Novu Booking') }}</title>
    @vite(['resources/sass/app.scss', 'resources/sass/style.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="auth">
        <main class="py-4">
            @yield('content') <!-- Yield content for specific pages -->
        </main>
    </div>
</body>
</html>
