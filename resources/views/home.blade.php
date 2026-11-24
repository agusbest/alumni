@extends('layouts.app')
@section('title', 'Selamat Datang.html')
@section('icon', '🏠')
@section('content')
    <div class="panel">
        <p class="logo">{{ config('reuni.name') }}</p>
        <div class="px" style="color:#000080">{{ config('reuni.school') }} · {{ config('reuni.tagline') }}</div>
        <div class="marq" style="margin:8px 0"><span>{{ $marquee }}</span></div>
        <div>Anda pengunjung ke-<span class="counter">{{ str_pad((string) $visits, 6, '0', STR_PAD_LEFT) }}</span></div>
        <div class="badges">
            <span class="badge ie">Best viewed in IE 800x600</span>
            <span class="uc"><b>🚧 UNDER CONSTRUCTION</b></span>
        </div>
    </div>

    <div class="panel">
        <div class="ph">Alumni Berulang Tahun Bulan {{ now()->translatedFormat('F') }} 🎂</div>
        @forelse ($birthdays as $p)
            <div>🎈 <b>{{ $p->name }}</b> <span class="muted">({{ $p->angkatan }}) · tgl {{ $p->birth_date->day }}</span></div>
        @empty
            <div class="muted">Belum ada yang tampil bulan ini.</div>
        @endforelse
        @guest
            <p class="small muted">Masuk untuk melihat lebih banyak yang berulang tahun.</p>
        @endguest
    </div>

    <div class="panel">
        <div class="ph">Petunjuk</div>
        <div class="small">
            @auth
                Klik ikon di kiri untuk membuka menu. Mulai dari <b>Foto Alumni</b> atau lengkapi <b>Profil Saya</b>.
            @else
                Kamu sedang sebagai <b>pengunjung</b>. <a href="{{ route('login') }}">Masuk</a> atau <a href="{{ route('register') }}">daftar</a>
                untuk membuka semua menu. Pendaftaran disetujui admin angkatan.
            @endauth
        </div>
    </div>
@endsection
