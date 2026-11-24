@extends('layouts.app')
@section('title', 'Biodata - '.$profile->name.'.exe')
@section('icon', '📄')
@section('content')
    @php
        $can = fn (string $f) => $profile->canSee($f, $viewer);
        $tabs = ['profil' => 'Profil', 'sekarang' => 'Sekarang', 'kenangan' => 'Kenangan SMA', 'kontak' => 'Kontak', 'slam' => 'Slam Book'];
        $me = $viewer?->profile;
    @endphp

    <div class="tabs" role="tablist">
        @foreach ($tabs as $k => $l)
            <a class="tab {{ $tab === $k ? 'on' : '' }}" role="tab" href="{{ route('galeri.show', ['profile' => $profile, 'tab' => $k]) }}">{{ $l }}</a>
        @endforeach
    </div>

    <div class="tabpane">
        @if (! $profile->is_claimed)
            <p class="msg"><b>Profil ini belum diklaim.</b><br>
                Hanya nama, angkatan, dan kelas yang ditampilkan. Kalau ini kamu,
                <a href="{{ route('register') }}">daftar dan klaim profil</a> agar bisa melengkapi biodata.</p>
            <dl class="d">
                @include('gallery._row', ['l' => 'Nama', 'ok' => true, 'v' => $profile->name])
                @include('gallery._row', ['l' => 'Angkatan', 'ok' => true, 'v' => $profile->angkatan])
                @include('gallery._row', ['l' => 'Kelas', 'ok' => true, 'v' => $profile->kelas])
            </dl>

        @elseif ($tab === 'profil')
            <div class="bio">
                <div class="bphoto">
                    @if ($can('photo'))
                        <div class="cmp">
                            <img src="{{ $profile->photoUrl('now') }}" alt="Foto sekarang {{ $profile->name }}">
                            <img class="top" src="{{ $profile->photoUrl('then') }}" alt="Foto dulu {{ $profile->name }}" style="clip-path:inset(0 50% 0 0)">
                        </div>
                        <input type="range" min="0" max="100" value="50" data-cmp aria-label="Geser untuk membandingkan foto dulu dan sekarang">
                        <div class="small center">◀ Dulu · Sekarang ▶</div>
                    @else
                        <div class="cmp"><img src="{{ $profile->photoUrl('now') }}" alt="Foto disembunyikan"></div>
                        <div class="small center muted">Foto disembunyikan</div>
                    @endif
                </div>
                <dl class="d" style="flex:1;min-width:200px">
                    @include('gallery._row', ['l' => 'Nama lengkap', 'ok' => true, 'v' => $profile->name])
                    @include('gallery._row', ['l' => 'Panggilan SMA', 'ok' => true, 'v' => $profile->nickname])
                    @include('gallery._row', ['l' => 'Angkatan', 'ok' => true, 'v' => $profile->angkatan])
                    @include('gallery._row', ['l' => 'Kelas', 'ok' => true, 'v' => $profile->kelas])
                    @include('gallery._row', ['l' => 'No. absen', 'ok' => true, 'v' => $profile->absen])
                    @include('gallery._row', ['l' => 'Tanggal lahir', 'ok' => $can('birth'), 'v' => $profile->birthText($viewer)])
                    @include('gallery._row', ['l' => 'Salam diterima', 'ok' => true, 'v' => $salamCount.' 👋'])
                </dl>
            </div>

        @elseif ($tab === 'sekarang')
            <dl class="d">
                @include('gallery._row', ['l' => 'Kota domisili', 'ok' => $can('city'), 'v' => $profile->city])
                @include('gallery._row', ['l' => 'Pekerjaan', 'ok' => $can('job'), 'v' => $profile->occupation])
                @include('gallery._row', ['l' => 'Instansi', 'ok' => $can('job'), 'v' => $profile->company])
                @include('gallery._row', ['l' => 'Status', 'ok' => $can('job'), 'v' => $profile->status_text])
                @include('gallery._row', ['l' => 'Sosial media', 'ok' => $can('social'), 'v' => $profile->social])
            </dl>

        @elseif ($tab === 'kenangan')
            @if ($can('memories'))
                <dl class="d">
                    @include('gallery._row', ['l' => 'Ekskul', 'ok' => true, 'v' => $profile->ekskul])
                    @include('gallery._row', ['l' => 'Geng', 'ok' => true, 'v' => $profile->geng])
                    @include('gallery._row', ['l' => 'Guru favorit', 'ok' => true, 'v' => $profile->favorite_teacher])
                    @include('gallery._row', ['l' => 'Pelajaran favorit', 'ok' => true, 'v' => $profile->favorite_subject])
                    @include('gallery._row', ['l' => 'Cita-cita dulu', 'ok' => true, 'v' => $profile->dream_then])
                    @include('gallery._row', ['l' => 'Cita-cita / profesi kini', 'ok' => true, 'v' => $profile->dream_now])
                    @include('gallery._row', ['l' => 'Lagu favorit', 'ok' => true, 'v' => $profile->favorite_song])
                    @include('gallery._row', ['l' => 'Kenangan paling berkesan', 'ok' => true, 'v' => $profile->memory])
                </dl>
            @else
                <dl class="d">@include('gallery._row', ['l' => 'Kenangan SMA', 'ok' => false, 'v' => null])</dl>
            @endif

        @elseif ($tab === 'kontak')
            <dl class="d">
                @if ($can('wa'))
                    @php
                        $digits = preg_replace('/\D/', '', (string) $profile->wa);
                        $waLink = $digits !== '' ? 'https://wa.me/'.(str_starts_with($digits, '0') ? '62'.substr($digits, 1) : $digits) : null;
                    @endphp
                    <dt>WhatsApp</dt>
                    <dd>@if (filled($profile->wa)){{ $profile->wa }} @if ($waLink)<a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer">Buka WA</a>@endif @else<span class="muted">-</span>@endif</dd>
                @else
                    @include('gallery._row', ['l' => 'WhatsApp', 'ok' => false, 'v' => null])
                @endif
                @include('gallery._row', ['l' => 'Email', 'ok' => $can('email'), 'v' => $profile->email_public])
            </dl>
            <p class="small muted">Kontak hanya tampil kalau pemilik mengizinkan.</p>

        @else
            @if (! $viewer)
                <p class="small">🔒 <a href="{{ route('login') }}">Masuk</a> untuk membaca dan menulis di slam book.</p>
            @else
                @forelse ($messages as $m)
                    <div class="msg"><div class="by">{{ $m->from?->name ?? 'Alumni (akun dihapus)' }}</div>{{ $m->body }}
                        <div class="small muted">{{ $m->created_at->translatedFormat('j F Y') }}</div></div>
                @empty
                    <p class="muted">Belum ada tulisan di slam book ini.</p>
                @endforelse

                @if ($me && $me->id !== $profile->id)
                    <form method="POST" action="{{ route('slam.store') }}">
                        @csrf
                        <input type="hidden" name="to_profile_id" value="{{ $profile->id }}">
                        <label for="body">Tulis di slam book {{ $profile->nickname ?: $profile->name }}</label>
                        <textarea id="body" name="body" rows="3" maxlength="400" required></textarea>
                        <div class="acts"><button class="btn" type="submit">Tulis</button></div>
                    </form>
                @endif
            @endif
        @endif
    </div>

    <div class="acts">
        @auth
            @if ($me && $me->id !== $profile->id && $profile->is_claimed)
                <form method="POST" action="{{ route('salam.store', $profile) }}">@csrf<button class="btn" type="submit">👋 Kirim Salam</button></form>
                <a class="btn" href="{{ route('galeri.show', ['profile' => $profile, 'tab' => 'slam']) }}">✍️ Tulis Slam Book</a>
            @endif
            @if ($me && $me->id !== $profile->id)
                <details>
                    <summary class="btn">🚩 Laporkan</summary>
                    <form class="msg" method="POST" action="{{ route('report.profile', $profile) }}">
                        @csrf
                        <label for="reason">Alasan laporan</label>
                        <select id="reason" name="reason">
                            <option>Foto tidak pantas</option><option>Data pribadi disalahgunakan</option><option>Akun palsu</option><option>Lainnya</option>
                        </select>
                        <div class="acts"><button class="btn" type="submit">Kirim Laporan</button></div>
                    </form>
                </details>
            @endif
        @endauth
        <a class="btn" href="{{ route('galeri.index') }}">Tutup</a>
    </div>
@endsection
