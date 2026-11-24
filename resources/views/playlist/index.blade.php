@extends('layouts.app')
@section('title', 'Kaset Playlist Angkatan')
@section('icon', '📼')
@section('content')
    <form method="GET" action="{{ route('playlist.index') }}" class="toolbar">
        <div><label for="ang">Angkatan</label>
            <select id="ang" name="ang"><option value="">Semua</option>
                @foreach ($angkatans as $y)<option value="{{ $y }}" @selected((string) $ang === (string) $y)>{{ $y }}</option>@endforeach
            </select></div>
        <button class="btn" type="submit">Filter</button>
    </form>

    <div class="kgrid">
        @forelse ($tracks as $t)
            <div class="kaset">
                <div class="lab"><b>{{ $t->title }}</b><span class="small">{{ $t->artist }}</span><br>
                    <span class="small muted">Angkatan {{ $t->angkatan }} · dari {{ $t->user?->name ?? 'Alumni' }}</span></div>
                <div class="reels"><i class="reel"></i><i class="reel"></i></div>
                @if ($t->url && preg_match('#^https?://#i', $t->url))
                    <a class="btn" href="{{ $t->url }}" target="_blank" rel="noopener noreferrer">▶ Putar</a>
                @endif
                @if ($t->user_id === auth()->id() || auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('playlist.destroy', $t) }}">@csrf @method('DELETE')<button class="btn" type="submit">Hapus</button></form>
                @endif
            </div>
        @empty
            <p class="muted">Belum ada kaset.</p>
        @endforelse
    </div>

    <form class="panel" method="POST" action="{{ route('playlist.store') }}" style="margin-top:12px">
        @csrf
        <div class="ph">Sumbang Lagu 2000-an</div>
        <label for="title">Judul lagu</label><input class="w" type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="60">
        <label for="artist">Artis</label><input class="w" type="text" id="artist" name="artist" value="{{ old('artist') }}" required maxlength="60">
        <label for="url">Tautan YouTube/Spotify (opsional)</label><input class="w" type="url" id="url" name="url" value="{{ old('url') }}" maxlength="200" placeholder="https://">
        <div class="acts"><button class="btn" type="submit">Masukkan ke Kaset</button></div>
    </form>
@endsection
