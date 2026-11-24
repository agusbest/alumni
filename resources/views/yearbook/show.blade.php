@extends('layouts.app')
@section('title', 'Buku Tahunan Digital')
@section('icon', '📖')
@section('content')
    @if (! $cur)
        <p class="pad">Belum ada data.</p>
    @else
        <div class="toolbar">
            @if ($i > 0)<a class="btn" href="{{ route('yearbook.show', ['p' => $i - 1]) }}">◀ Sebelumnya</a>@else<span class="btn" aria-disabled="true">◀ Sebelumnya</span>@endif
            <form method="GET" action="{{ route('yearbook.show') }}" style="display:flex;gap:4px">
                <select name="p" aria-label="Pilih halaman">
                    @foreach ($pages as $idx => $pg)<option value="{{ $idx }}" @selected($idx === $i)>Angkatan {{ $pg->angkatan }} · {{ $pg->kelas }}</option>@endforeach
                </select>
                <button class="btn" type="submit">Buka</button>
            </form>
            @if ($i < $pages->count() - 1)<a class="btn" href="{{ route('yearbook.show', ['p' => $i + 1]) }}">Berikutnya ▶</a>@else<span class="btn" aria-disabled="true">Berikutnya ▶</span>@endif
            <span class="small">Halaman {{ $i + 1 }}/{{ $pages->count() }}</span>
        </div>

        <div class="book flip">
            <div class="pg">
                <div class="px" style="color:#6b4423">YEARBOOK {{ $cur->angkatan }}</div>
                <h3 style="color:#6b4423;margin-top:4px">{{ config('reuni.school') }} · {{ $cur->kelas }}</h3>
                <div class="yb">
                    @foreach ($members as $p)
                        <figure>
                            <a href="{{ route('galeri.show', $p) }}"><img loading="lazy" src="{{ $p->photoUrl('then') }}" alt="{{ $p->name }}"></a>
                            <figcaption>{{ \Illuminate\Support\Str::before($p->name, ' ') }}</figcaption>
                        </figure>
                    @endforeach
                </div>
                <p class="small muted">{{ $members->count() }} siswa</p>
            </div>
            <div class="pg">
                <h3 style="color:#6b4423">Pesan &amp; Kesan</h3>
                @forelse ($messages as $m)
                    <div class="msg"><div class="by">{{ $m->profile?->name ?? 'Alumni (akun dihapus)' }}</div>{{ $m->body }}</div>
                @empty
                    <p class="muted">Belum ada pesan. Jadi yang pertama!</p>
                @endforelse
                @if (auth()->user()->profile)
                    <form method="POST" action="{{ route('yearbook.store') }}">
                        @csrf
                        <input type="hidden" name="angkatan" value="{{ $cur->angkatan }}">
                        <input type="hidden" name="kelas" value="{{ $cur->kelas }}">
                        <input type="hidden" name="p" value="{{ $i }}">
                        <label for="body">Tulis pesan &amp; kesanmu</label>
                        <textarea id="body" name="body" rows="2" maxlength="300" required></textarea>
                        <div class="acts"><button class="btn" type="submit">Tulis</button></div>
                    </form>
                @endif
            </div>
        </div>
    @endif
@endsection
