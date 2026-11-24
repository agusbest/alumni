@extends('layouts.app')
@section('title', 'Album Kenangan')
@section('icon', '📷')
@section('content')
    <details class="panel" @if ($errors->any()) open @endif>
        <summary><b>⬆ Unggah foto kenangan</b></summary>
        <form method="POST" action="{{ route('album.store') }}" enctype="multipart/form-data">
            @csrf
            <label for="photo">Foto (JPG/PNG/WebP, maks 5 MB)</label>
            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" required>
            <label for="caption">Keterangan</label>
            <input class="w" type="text" id="caption" name="caption" value="{{ old('caption') }}" maxlength="100" required>
            <label for="year">Tahun</label>
            <input type="number" id="year" name="year" min="1995" max="2015" value="{{ old('year', 2004) }}">
            <label for="tags">Tag teman (tahan Ctrl/Cmd untuk pilih banyak)</label>
            <select class="w" id="tags" name="tags[]" multiple size="5">
                @foreach ($people as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
            </select>
            @if ($moderated)<p class="small muted">Moderasi aktif: foto tampil setelah disetujui admin.</p>@endif
            <div class="acts"><button class="btn" type="submit">Unggah</button></div>
        </form>
    </details>

    <div class="albumgrid">
        @forelse ($photos as $a)
            <figure class="polaroid" style="--r:{{ ($a->id % 5) - 2 }}deg">
                <img loading="lazy" src="{{ route('album.image', $a) }}" alt="{{ $a->caption }}">
                <span class="stamp">'{{ substr((string) $a->year, -2) }}</span>
                <figcaption style="margin-top:6px">
                    <b>{{ $a->caption }}</b><br>
                    <span class="small muted">
                        @if ($a->tags->isNotEmpty())Bersama: {{ $a->tags->map(fn ($t) => \Illuminate\Support\Str::before($t->name, ' '))->implode(', ') }}<br>@endif
                        oleh {{ $a->user?->name ?? 'Alumni (akun dihapus)' }}
                    </span>
                    @if ($a->status !== 'ok')<br><i class="tag">menunggu persetujuan</i>@endif
                </figcaption>
                @if ($a->user_id === auth()->id() || auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('album.destroy', $a) }}" data-confirm>@csrf @method('DELETE')<button class="btn" type="submit">Hapus</button></form>
                @endif
                @if (auth()->user()->isAdmin() && $a->status !== 'ok')
                    <form method="POST" action="{{ route('album.approve', $a) }}">@csrf<button class="btn" type="submit">Setujui</button></form>
                @endif
            </figure>
        @empty
            <p class="muted">Belum ada foto.</p>
        @endforelse
    </div>
    @include('partials.pager', ['paginator' => $photos])
@endsection
