<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Novu Booking') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net"> <!-- DNS prefetch for faster font loading -->
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet"> <!-- Nunito font from Bunny Fonts -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> <!-- jQuery 3.6.4 (Ensure only one version is used) -->

    <!-- Icon Libraries -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'> <!-- Boxicons for vector icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> <!-- FontAwesome icons -->

    <!-- DataTables -->
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css"> <!-- DataTables CSS for table styling -->

    <!-- SweetAlert -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> <!-- SweetAlert for stylish popups -->

    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script> <!-- FullCalendar for event calendar functionality -->

    <!-- Vite Assets -->
    <!-- Vite for handling the app's assets (CSS and JS) -->
    @vite(['resources/sass/app.scss', 'resources/sass/style.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        @include('components.navbar') <!-- Include the navbar component -->
        <main class="py-4">
            @yield('content') <!-- Yield content for specific pages -->
        </main>
    </div>

    @yield('script') <!-- Yield page-specific scripts -->

    <!-- Additional Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script> <!-- AOS for scroll animations -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 for better alerts -->
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script> <!-- DataTables JS for table functionality -->
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script> <!-- Boxicons JS for interactive icons -->
</body>
</html>
