@extends('layouts.app')
@section('title', 'Masuk')
@section('icon', '🔑')
@section('content')
    <div style="max-width:420px">
        <p><b>Selamat datang, alumni!</b></p>
        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <label for="email">Email</label>
            <input class="w" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            <label for="password">Password</label>
            <input class="w" type="password" id="password" name="password" required autocomplete="current-password">
            <label style="font-weight:normal"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
            <div class="acts"><button class="btn" type="submit">Masuk</button>
                <a class="btn" href="{{ route('register') }}">Daftar Alumni Baru / Klaim Profil</a></div>
        </form>
        <p class="small muted">Pengunjung hanya melihat info umum: beranda, mading, acara reuni, guestbook, kuis, dan alumni yang memilih profil <i>Publik</i>.</p>
    </div>
@endsection
