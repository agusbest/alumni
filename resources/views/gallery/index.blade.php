@extends('layouts.app')
@section('title', 'Foto Alumni')
@section('icon', '📁')
@section('content')
    <form method="GET" action="{{ route('galeri.index') }}" class="toolbar">
        <input type="hidden" name="view" value="{{ $f['view'] }}">
        <input type="hidden" name="foto" value="{{ $f['foto'] }}">
        <div><label for="q">Cari nama</label><input type="search" id="q" name="q" value="{{ $f['q'] }}" style="width:130px"></div>
        <div><label for="ang">Angkatan</label>
            <select id="ang" name="ang"><option value="">Semua</option>
                @foreach ($angkatans as $a)<option value="{{ $a }}" @selected((string) $f['ang'] === (string) $a)>{{ $a }}</option>@endforeach
            </select></div>
        <div><label for="kelas">Kelas</label>
            <select id="kelas" name="kelas"><option value="">Semua</option>
                @foreach ($kelass as $k)<option @selected($f['kelas'] === $k)>{{ $k }}</option>@endforeach
            </select></div>
        <div><label for="kota">Kota</label>
            <select id="kota" name="kota"><option value="">Semua</option>
                @foreach ($kotas as $k)<option @selected($f['kota'] === $k)>{{ $k }}</option>@endforeach
            </select></div>
        <div><label for="prof">Profesi</label><input type="search" id="prof" name="prof" value="{{ $f['prof'] }}" style="width:110px"></div>
        <div><label for="sort">Urutkan</label>
            <select id="sort" name="sort">
                <option value="az" @selected($f['sort'] === 'az')>A-Z</option>
                <option value="ang" @selected($f['sort'] === 'ang')>Angkatan</option>
                <option value="new" @selected($f['sort'] === 'new')>Terbaru gabung</option>
            </select></div>
        <div><button class="btn" type="submit">Cari</button> <a class="btn" href="{{ route('galeri.index') }}">Reset</a></div>
    </form>

    <div class="toolbar">
        <span>Tampilan:</span>
        @foreach (['grid' => 'Grid Foto', 'list' => 'Daftar', 'kelas' => 'Per Kelas'] as $k => $l)
            <a class="btn {{ $f['view'] === $k ? 'on' : '' }}" href="{{ request()->fullUrlWithQuery(['view' => $k, 'page' => null]) }}">{{ $l }}</a>
        @endforeach
        <span style="margin-left:8px">Foto:</span>
        @foreach (['then' => 'Dulu', 'now' => 'Sekarang'] as $k => $l)
            <a class="btn {{ $f['foto'] === $k ? 'on' : '' }}" href="{{ request()->fullUrlWithQuery(['foto' => $k]) }}">{{ $l }}</a>
        @endforeach
    </div>

    @guest
        <p class="small muted">🔒 Pengunjung hanya melihat alumni yang memilih profil <i>Publik</i>. <a href="{{ route('login') }}">Masuk</a> untuk melihat semua alumni.</p>
    @endguest

    @if ($profiles->isEmpty())
        <p class="msg">Tidak ada alumni yang cocok dengan filter ini.</p>
    @else
        <p class="small">{{ $profiles->total() }} alumni</p>

        @if ($f['view'] === 'grid')
            <div class="grid">
                @foreach ($profiles as $p)@include('gallery._card', ['p' => $p, 'foto' => $f['foto']])@endforeach
            </div>
        @elseif ($f['view'] === 'list')
            <table class="rows">
                <tr><th></th><th>Nama</th><th>Angkatan</th><th>Kota</th><th>Profesi</th></tr>
                @foreach ($profiles as $p)
                    <tr>
                        <td><img loading="lazy" src="{{ $p->photoUrl($f['foto']) }}" alt=""></td>
                        <td><a href="{{ route('galeri.show', $p) }}"><b>{{ $p->name }}</b></a>@unless ($p->is_claimed) <i class="tag">belum gabung</i>@endunless</td>
                        <td>{{ $p->angkatan }} · {{ $p->kelas }}</td>
                        <td>@if ($p->canSee('city', $viewer)){{ $p->city }}@else<span class="muted">🔒</span>@endif</td>
                        <td>@if ($p->canSee('job', $viewer)){{ $p->occupation }}@else<span class="muted">🔒</span>@endif</td>
                    </tr>
                @endforeach
            </table>
        @else
            @foreach ($profiles->groupBy(fn ($p) => $p->angkatan.' · '.$p->kelas) as $label => $group)
                <div class="grp">Angkatan {{ $label }} ({{ $group->count() }})</div>
                <div class="grid">
                    @foreach ($group as $p)@include('gallery._card', ['p' => $p, 'foto' => $f['foto']])@endforeach
                </div>
            @endforeach
        @endif

        @include('partials.pager', ['paginator' => $profiles])
    @endif
@endsection
