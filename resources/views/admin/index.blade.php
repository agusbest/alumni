@extends('layouts.app')
@section('title', 'Panel Admin')
@section('icon', '🛡️')
@section('content')
    @php
        $tabs = [
            'verifikasi' => 'Verifikasi ('.$pending->count().')',
            'laporan' => 'Laporan ('.$reports->where('is_done', false)->count().')',
            'moderasi' => 'Moderasi',
            'data' => 'Data',
        ];
    @endphp
    <div class="tabs" role="tablist">
        @foreach ($tabs as $k => $l)
            <a class="tab {{ $tab === $k ? 'on' : '' }}" role="tab" href="{{ route('admin.index', ['tab' => $k]) }}">{{ $l }}</a>
        @endforeach
    </div>

    <div class="tabpane">
        @if ($tab === 'verifikasi')
            @forelse ($pending as $u)
                <div class="msg">
                    <b>{{ $u->name }}</b> · angkatan {{ $u->reg_angkatan }}, {{ $u->reg_kelas }}<br>
                    @if ($u->claim_profile_id && $claims->has($u->claim_profile_id))
                        <span class="tag">Klaim profil lama: {{ $claims[$u->claim_profile_id]->name }}</span>
                    @else
                        <span class="tag">Profil baru</span>
                    @endif
                    <span class="small muted">{{ $u->email }} · {{ $u->created_at->translatedFormat('j F Y') }}</span>
                    <div class="acts">
                        <form method="POST" action="{{ route('admin.approve', $u) }}">@csrf<button class="btn" type="submit">✔ Setujui</button></form>
                        <form method="POST" action="{{ route('admin.reject', $u) }}">@csrf<button class="btn" type="submit">✖ Tolak</button></form>
                    </div>
                </div>
            @empty
                <p class="muted">Tidak ada pendaftaran menunggu.</p>
            @endforelse

        @elseif ($tab === 'laporan')
            @forelse ($reports as $r)
                <div class="msg" @if ($r->is_done) style="opacity:.55" @endif>
                    <b>{{ $r->label }}</b><br>
                    Alasan: {{ $r->reason }} · pelapor: {{ $r->reporter?->name ?? '-' }} · {{ $r->created_at->translatedFormat('j F Y') }}
                    <div class="acts">
                        @if ($r->type === 'profile')<a class="btn" href="{{ route('galeri.show', $r->target_id) }}">Lihat profil</a>@endif
                        @if ($r->type === 'post')<a class="btn" href="{{ route('mading.index') }}">Buka mading</a>@endif
                        @if ($r->is_done)
                            <span class="tag">selesai</span>
                        @else
                            <form method="POST" action="{{ route('admin.report.done', $r) }}">@csrf<button class="btn" type="submit">Tandai selesai</button></form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="muted">Belum ada laporan.</p>
            @endforelse

        @elseif ($tab === 'moderasi')
            <form method="POST" action="{{ route('admin.moderation') }}">
                @csrf
                <p>Moderasi foto album: <b>{{ $moderated ? 'AKTIF (foto baru perlu disetujui)' : 'nonaktif' }}</b></p>
                <button class="btn" type="submit">{{ $moderated ? 'Nonaktifkan' : 'Aktifkan' }} moderasi</button>
            </form>
            <h3>Foto menunggu ({{ $pendingPhotos->count() }})</h3>
            @forelse ($pendingPhotos as $a)
                <div class="msg">
                    <img src="{{ route('album.image', $a) }}" alt="" style="width:80px;vertical-align:middle">
                    {{ $a->caption }} · {{ $a->user?->name }}
                    <form method="POST" action="{{ route('album.approve', $a) }}" style="display:inline">@csrf<button class="btn" type="submit">Setujui</button></form>
                    <form method="POST" action="{{ route('album.destroy', $a) }}" style="display:inline">@csrf @method('DELETE')<button class="btn" type="submit">Tolak</button></form>
                </div>
            @empty
                <p class="muted">Tidak ada.</p>
            @endforelse

        @else
            <h3>Tambah data alumni lama (belum bergabung)</h3>
            <form method="POST" action="{{ route('admin.old') }}">
                @csrf
                <label for="name">Nama lengkap</label><input class="w" type="text" id="name" name="name" required maxlength="80">
                <label for="angkatan">Angkatan</label>
                <select id="angkatan" name="angkatan">@foreach (config('reuni.years') as $y)<option value="{{ $y }}">{{ $y }}</option>@endforeach</select>
                <label for="kelas">Kelas</label>
                <select id="kelas" name="kelas">@foreach (config('reuni.classes') as $k)<option>{{ $k }}</option>@endforeach</select>
                <div class="acts"><button class="btn" type="submit">Tambah</button></div>
            </form>
            <hr>
            <h3>Daftar kelas (teks)</h3>
            <form method="GET" action="{{ route('admin.index') }}" class="toolbar">
                <input type="hidden" name="tab" value="data">
                @php $selKey = request('angkatan').'|'.request('kelas'); @endphp
                <select id="cl" aria-label="Pilih kelas" onchange="var v=this.value.split('|');this.form.angkatan.value=v[0];this.form.kelas.value=v[1];">
                    <option value="">Pilih kelas...</option>
                    @foreach ($pages as $pg)<option value="{{ $pg->angkatan }}|{{ $pg->kelas }}" @selected($selKey === $pg->angkatan.'|'.$pg->kelas)>Angkatan {{ $pg->angkatan }} · {{ $pg->kelas }}</option>@endforeach
                </select>
                <input type="hidden" name="angkatan" value="{{ request('angkatan') }}">
                <input type="hidden" name="kelas" value="{{ request('kelas') }}">
                <button class="btn" type="submit">Buat daftar</button>
            </form>
            @if ($classList)<textarea readonly rows="8" aria-label="Daftar nama">{{ $classList }}</textarea>@endif
            <p class="small muted">Hanya nama, tanpa data pribadi. Ekspor PDF buku tahunan belum tersedia.</p>
        @endif
    </div>
@endsection
