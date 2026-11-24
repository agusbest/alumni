<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>404 - Not Found</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/win98.css') }}">
</head>
<body>
<div id="desk" style="justify-content:center">
    <div id="winwrap" style="max-width:420px">
        <section class="win">
            <div class="tb"><span aria-hidden="true">🗑️</span><span class="tt">404 - Not Found</span><a class="tbb" href="{{ url('/') }}" aria-label="Tutup">x</a></div>
            <div class="wb">
                <div class="center"><div class="px" style="color:#a00000">ERROR 404</div>
                    <p>File yang kamu cari tidak ditemukan di Recycle Bin.<br>Daripada bengong, main Snake dulu!</p></div>
                @include('partials.snake')
                <div class="acts" style="justify-content:center"><a class="btn" href="{{ url('/') }}">🏠 Ke Beranda</a></div>
            </div>
        </section>
    </div>
</div>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
