<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Beranda') – {{ config('reuni.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/win98.css') }}">
</head>
<body>
@php
    $user = auth()->user();
    $menu = [
        ['galeri.index', '📁', 'Foto Alumni', 'galeri.*'],
        ['slam.index', '📒', 'Slam Book', 'slam.*'],
        ['yearbook.show', '📖', 'Buku Tahunan', 'yearbook.*'],
        ['mading.index', '📌', 'Mading', 'mading.*'],
        ['album.index', '📷', 'Album Kenangan', 'album.*'],
        ['events.index', '🎉', 'Reuni & Event', 'events.*'],
        ['guestbook.index', '✍️', 'Guestbook', 'guestbook.*'],
        ['playlist.index', '📼', 'Kaset Playlist', 'playlist.*'],
        ['kuis', '❓', 'Kuis Seru', 'kuis'],
        ['map.index', '🗺️', 'Peta Alumni', 'map.*'],
        ['ledger.index', '💰', 'Iuran Reuni', 'ledger.*'],
        ['snake', '🐍', 'Snake II', 'snake'],
        ['profile.edit', '🧑', 'Profil Saya', 'profile.*'],
        ['about', '💻', 'My Computer', 'about'],
    ];
    if ($user && $user->isAdmin()) {
        $menu[] = ['admin.index', '🛡️', 'Panel Admin', 'admin.*'];
    }
@endphp
<div id="desk">
    <nav id="icons" aria-label="Menu utama">
        @foreach ($menu as [$route, $icon, $label, $match])
            <a class="icon {{ request()->routeIs($match) ? 'active' : '' }}" href="{{ route($route) }}">
                <span class="g" aria-hidden="true">{{ $icon }}</span><span class="l">{{ $label }}</span>
            </a>
        @endforeach
    </nav>

    <div id="winwrap">
        <section class="win">
            <div class="tb">
                <span aria-hidden="true">@yield('icon', '🪟')</span>
                <span class="tt">@yield('title', 'Beranda')</span>
                <a class="tbb" href="{{ route('home') }}" aria-label="Tutup, kembali ke beranda">x</a>
            </div>
            <div class="navbar">
                <a class="btn ibtn" href="{{ url()->previous(route('home')) }}" title="Kembali" aria-label="Kembali">◀</a>
                <a class="btn ibtn" href="{{ route('home') }}" title="Beranda" aria-label="Beranda">🏠</a>
                <span class="path">{{ config('reuni.name') }} › @yield('title', 'Beranda')</span>
            </div>
            <div class="wb">
                @foreach (['ok' => '', 'info' => ''] as $key => $_)
                    @if (session($key))
                        <div class="flash" role="status">{{ session($key) }}</div>
                    @endif
                @endforeach
                @if ($errors->any())
                    <div class="flash err" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </section>
    </div>
</div>

<div id="taskbar">
    <details class="startmenu">
        <summary class="btn"><span aria-hidden="true">🪟</span> Start</summary>
        <div class="smenu">
            @foreach ($menu as [$route, $icon, $label, $match])
                <a href="{{ route($route) }}">{{ $icon }} {{ $label }}</a>
            @endforeach
            <hr>
            @if ($user)
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">🔑 Keluar</button></form>
            @else
                <a href="{{ route('login') }}">🔑 Masuk</a>
                <a href="{{ route('register') }}">📝 Daftar Alumni</a>
            @endif
        </div>
    </details>
    <div id="tasks" class="small">{{ config('reuni.school') }}</div>
    <div id="tray">
        @if ($user)
            <span>👤 {{ $user->isAdmin() ? 'Admin' : \Illuminate\Support\Str::limit($user->name, 18) }}</span>
        @else
            <a href="{{ route('login') }}">👤 Pengunjung (Masuk)</a>
        @endif
        <span class="clock" id="clock"></span>
    </div>
</div>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
