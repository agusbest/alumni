@extends('layouts.app')
@section('title', 'Profil Saya')
@section('icon', '🧑')
@section('content')
    @php
        $pc = $profile->completeness();
        $on = (int) round($pc / 100 * 20);
        $levels = config('reuni.vis_levels');
        $vis = array_merge(\App\Models\AlumniProfile::defaultVisibility(), $profile->visibility ?? []);
        $visSelect = function (string $key) use ($levels, $vis) {
            $o = '';
            foreach ($levels as $val => $label) {
                $o .= '<option value="'.e($val).'"'.(old('vis.'.$key, $vis[$key] ?? 'hidden') === $val ? ' selected' : '').'>'.e($label).'</option>';
            }
            return new \Illuminate\Support\HtmlString('<select class="vis" name="vis['.e($key).']" aria-label="Siapa yang boleh melihat">'.$o.'</select>');
        };
    @endphp

    <p><b>Profil kamu {{ $pc }}% lengkap</b></p>
    <div class="prog" role="progressbar" aria-valuenow="{{ $pc }}" aria-valuemin="0" aria-valuemax="100">
        @for ($i = 0; $i < 20; $i++)<i class="{{ $i < $on ? '' : 'off' }}"></i>@endfor
    </div>

    <h3>Foto</h3>
    <div class="acts" style="align-items:flex-start">
        @foreach (['then' => 'Foto SMA (dulu)', 'now' => 'Foto sekarang'] as $kind => $label)
            <div class="center">
                <div class="small"><b>{{ $label }}</b></div>
                <img src="{{ $profile->photoUrl($kind) }}?v={{ $profile->updated_at?->timestamp }}" alt="" style="width:90px;height:120px;object-fit:cover;border:2px solid #fff">
                <form method="POST" action="{{ route('profile.photo', $kind) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required aria-label="{{ $label }}" style="max-width:150px">
                    <button class="btn" type="submit">Unggah</button>
                </form>
            </div>
        @endforeach
        <div class="small" style="flex:1;min-width:150px">JPG/PNG/WebP, maks 5 MB. Otomatis dipotong rasio 3:4 dan metadata lokasi dibuang.</div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf @method('PUT')
        <div class="two">
            <div>
                <h3>Data Diri</h3>
                <label for="name">Nama lengkap</label><input class="w" type="text" id="name" name="name" value="{{ old('name', $profile->name) }}" required maxlength="80">
                <label for="nickname">Nama panggilan SMA</label><input class="w" type="text" id="nickname" name="nickname" value="{{ old('nickname', $profile->nickname) }}" maxlength="40">
                <label for="absen">No. absen (opsional)</label><input type="text" id="absen" name="absen" value="{{ old('absen', $profile->absen) }}" maxlength="3" size="4">
                <label for="birth_date">Tanggal lahir</label><input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $profile->birth_date?->format('Y-m-d')) }}">
                <div class="small">Siapa boleh lihat? {{ $visSelect('birth') }}<br><span class="muted">Orang lain hanya melihat tanggal dan bulan.</span></div>

                <h3>Foto &amp; Sekarang</h3>
                <div class="small">Siapa boleh lihat foto? {{ $visSelect('photo') }}</div>
                <label for="city">Kota domisili</label><input class="w" type="text" id="city" name="city" value="{{ old('city', $profile->city) }}" maxlength="80">
                <div class="small">{{ $visSelect('city') }}</div>
                <label for="occupation">Pekerjaan / profesi</label><input class="w" type="text" id="occupation" name="occupation" value="{{ old('occupation', $profile->occupation) }}" maxlength="120">
                <label for="company">Instansi</label><input class="w" type="text" id="company" name="company" value="{{ old('company', $profile->company) }}" maxlength="120">
                <label for="status_text">Status (opsional)</label><input class="w" type="text" id="status_text" name="status_text" value="{{ old('status_text', $profile->status_text) }}" maxlength="120">
                <div class="small">{{ $visSelect('job') }}</div>
                <label for="social">Sosial media (IG/LinkedIn)</label><input class="w" type="text" id="social" name="social" value="{{ old('social', $profile->social) }}" maxlength="120">
                <div class="small">{{ $visSelect('social') }}</div>
            </div>
            <div>
                <h3>Kenangan SMA</h3>
                <label for="ekskul">Ekskul</label><input class="w" type="text" id="ekskul" name="ekskul" value="{{ old('ekskul', $profile->ekskul) }}" maxlength="120">
                <label for="geng">Geng</label><input class="w" type="text" id="geng" name="geng" value="{{ old('geng', $profile->geng) }}" maxlength="120">
                <label for="favorite_teacher">Guru favorit</label><input class="w" type="text" id="favorite_teacher" name="favorite_teacher" value="{{ old('favorite_teacher', $profile->favorite_teacher) }}" maxlength="120">
                <label for="favorite_subject">Pelajaran favorit</label><input class="w" type="text" id="favorite_subject" name="favorite_subject" value="{{ old('favorite_subject', $profile->favorite_subject) }}" maxlength="120">
                <label for="dream_then">Cita-cita dulu</label><input class="w" type="text" id="dream_then" name="dream_then" value="{{ old('dream_then', $profile->dream_then) }}" maxlength="120">
                <label for="dream_now">Cita-cita / profesi kini</label><input class="w" type="text" id="dream_now" name="dream_now" value="{{ old('dream_now', $profile->dream_now) }}" maxlength="120">
                <label for="favorite_song">Lagu favorit saat itu</label><input class="w" type="text" id="favorite_song" name="favorite_song" value="{{ old('favorite_song', $profile->favorite_song) }}" maxlength="120">
                <label for="memory">Kenangan paling berkesan</label><textarea id="memory" name="memory" rows="3" maxlength="400">{{ old('memory', $profile->memory) }}</textarea>
                <div class="small">Siapa boleh lihat bagian Kenangan? {{ $visSelect('memories') }}</div>

                <h3>Kontak</h3>
                <label for="wa">WhatsApp</label><input class="w" type="text" id="wa" name="wa" value="{{ old('wa', $profile->wa) }}" maxlength="20">
                <div class="small">{{ $visSelect('wa') }}</div>
                <label for="email_public">Email untuk dilihat teman</label><input class="w" type="email" id="email_public" name="email_public" value="{{ old('email_public', $profile->email_public) }}" maxlength="120">
                <div class="small">{{ $visSelect('email') }}</div>
            </div>
        </div>
        <div class="acts"><button class="btn" type="submit">💾 Simpan Profil</button></div>
    </form>

    <hr>
    <h3>Hapus Akun</h3>
    <p class="small">Menghapus profil, foto, tulisan, dan semua datamu dari {{ config('reuni.name') }}. Tidak bisa dibatalkan.</p>
    <form method="POST" action="{{ route('profile.destroy') }}">
        @csrf @method('DELETE')
        <label for="confirm">Ketik HAPUS untuk konfirmasi</label>
        <input type="text" id="confirm" name="confirm" autocomplete="off">
        <button class="btn" type="submit">Hapus akun dan semua data saya</button>
    </form>
@endsection
