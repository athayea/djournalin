<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Journalin — kelola aktivitas dan kegiatan harian.">
    <title>@yield('title') - Journalin</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <a class="brand" href="{{ route('dashboard') }}">Journalin.</a>
    <p class="tagline">Kelola aktivitas dan kegiatan harian.</p>
    <main class="card">
        @yield('content')
    </main>
</body>
</html>
