@extends('layouts.app')
@section('title', 'Pendaftaran Alumni')
@section('icon', '📝')
@section('content')
    <div style="max-width:460px">
        <p class="small">Isi data di bawah. Akun baru aktif setelah <b>disetujui admin angkatan</b>.</p>
        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <label for="name">Nama lengkap</label>
            <input class="w" type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="80">
            <label for="email">Email (tidak ditampilkan ke publik)</label>
            <input class="w" type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            <label for="password">Password (min. 8 karakter)</label>
            <input class="w" type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
            <label for="password_confirmation">Ulangi password</label>
            <input class="w" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
            <label for="angkatan">Tahun lulus (angkatan)</label>
            <select id="angkatan" name="angkatan">
                @foreach (config('reuni.years') as $y)
                    <option value="{{ $y }}" @selected((int) old('angkatan', 2004) === $y)>{{ $y }}</option>
                @endforeach
            </select>
            <label for="kelas">Kelas</label>
            <select id="kelas" name="kelas">
                @foreach (config('reuni.classes') as $k)
                    <option @selected(old('kelas') === $k)>{{ $k }}</option>
                @endforeach
            </select>
            <label for="claim_profile_id">Sudah ada di data lama? Klaim profil:</label>
            <select class="w" id="claim_profile_id" name="claim_profile_id">
                <option value="">Bukan, buat profil baru</option>
                @foreach ($claimable as $c)
                    <option value="{{ $c->id }}" @selected((int) old('claim_profile_id') === $c->id)>{{ $c->name }} ({{ $c->angkatan }}, {{ $c->kelas }})</option>
                @endforeach
            </select>
            <div class="acts"><button class="btn" type="submit">Kirim Pendaftaran</button></div>
        </form>
    </div>
@endsection
