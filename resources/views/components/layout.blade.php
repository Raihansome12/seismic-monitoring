<?php
// index.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seismic Monitoring Dashboard</title>
    @livewireStyles
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <link rel="icon" type="image/png" href="{{ asset('img/earthquake.png') }}">
</head>

<body class="bg-gray-100 " style="font-family: Arial, sans-serif;">
    
    <x-navbar>{{ $title }}</x-navbar>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    <x-sidebar></x-sidebar>

    @livewireScripts
</body>
</html>