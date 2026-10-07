<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Journalin')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="logo">Journalin.</div>
        <nav>
            <a class="nav-item {{ request()->routeIs('dashboard', 'dashboard.index') ? 'active' : '' }}" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>Dashboard</a>
            <a class="nav-item {{ request()->routeIs('tasks.index') ? 'active' : '' }}" href="{{ route('tasks.index') }}"><svg viewBox="0 0 24 24"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 3h6v3H9zM9 14l2 2 4-4"/></svg>Semua Tugas</a>
            <button type="button" class="nav-item nav-toggle" id="cat-toggle" aria-expanded="false" aria-controls="cat-list"><svg viewBox="0 0 24 24"><path d="M3 6a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>Kategori<svg class="chev" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></button>
            <div id="cat-list" hidden>
                <a class="sub-item{{ request()->routeIs('tasks.index') && request()->query('group') === 'today' ? ' active' : '' }}" href="{{ route('tasks.index', ['group' => 'today']) }}" data-group="today">Hari Ini</a>
                <a class="sub-item{{ request()->routeIs('tasks.index') && request()->query('group') === 'tomorrow' ? ' active' : '' }}" href="{{ route('tasks.index', ['group' => 'tomorrow']) }}" data-group="tomorrow">Besok</a>
                <a class="sub-item{{ request()->routeIs('tasks.index') && request()->query('group') === 'later' ? ' active' : '' }}" href="{{ route('tasks.index', ['group' => 'later']) }}" data-group="later">Mendatang</a>
            </div>
        </nav>
        
        @guest
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="side-login">Login</a>
            @endif
        @else
            @if (Route::has('logout'))
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="side-login">Keluar</button></form>
            @endif
        @endguest
    </aside>
    <div class="mobile-overlay" id="mobile-overlay"></div>
    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" id="mobile-menu-btn" aria-label="Menu" class="mobile-menu-btn"><svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
            </div>
            <div class="topbar-right">
                <div class="date-pill" id="topbar-date"><svg viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M9 3v4M15 3v4"/></svg>{{ now()->format('d/m/Y') }}</div>
                <div class="user-wrap">
                <div class="user">
                    @auth
                        <strong>{{ auth()->user()->name }}</strong>
                        <span>{{ auth()->user()->email }}</span>
                    @endauth
                </div>
                @auth
                    <span class="avatar">{{ mb_strtoupper(collect(preg_split('/\s+/', trim(auth()->user()->name)))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('')) }}</span>
                @endauth
            </div>
            </div>
        </header>
        <main class="content @yield('content_class')">@yield('content')</main>
    </div>
</div>
<script src="{{ asset('js/sidebar.js') }}"></script>
@stack('scripts')
</body>
</html>