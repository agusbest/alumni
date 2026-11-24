@extends('layouts.app')
@section('title', 'Guestbook')
@section('icon', '✍️')
@section('content')
    <form class="panel" method="POST" action="{{ route('guestbook.store') }}">
        @csrf
        <div class="ph">Tanda Tangan Buku Tamu</div>
        <label for="name">Nama</label>
        <input class="w" type="text" id="name" name="name" value="{{ old('name', auth()->user()?->isAdmin() ? '' : auth()->user()?->name) }}" required maxlength="40">
        <label for="angkatan">Angkatan</label>
        <select id="angkatan" name="angkatan">
            <option value="">(bukan alumni)</option>
            @foreach (config('reuni.years') as $y)<option value="{{ $y }}" @selected((int) old('angkatan', auth()->user()?->profile?->angkatan) === $y)>{{ $y }}</option>@endforeach
        </select>
        <label for="body">Pesan (maks 200 huruf)</label>
        <textarea id="body" name="body" rows="2" maxlength="200" required>{{ old('body') }}</textarea>
        <div class="hp" aria-hidden="true"><label>Jangan diisi <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
        <div class="acts"><button class="btn" type="submit">Tanda Tangan</button></div>
    </form>

    <form method="GET" action="{{ route('guestbook.index') }}" class="toolbar">
        <div><label for="ang">Tampilkan angkatan</label>
            <select id="ang" name="ang"><option value="">Semua</option>
                @foreach (config('reuni.years') as $y)<option value="{{ $y }}" @selected((string) $ang === (string) $y)>{{ $y }}</option>@endforeach
            </select></div>
        <button class="btn" type="submit">Filter</button>
    </form>

    @forelse ($entries as $g)
        <div class="msg">
            <span class="by">{{ $g->name }}</span> @if ($g->angkatan)<span class="small muted">(angkatan {{ $g->angkatan }})</span>@endif<br>
            {{ $g->body }}
            <div class="small muted">{{ $g->created_at->translatedFormat('j F Y') }}
                @auth
                    @if (auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('guestbook.destroy', $g) }}" style="display:inline">@csrf @method('DELETE')<button class="btn" type="submit">Hapus</button></form>
                    @endif
                @endauth
            </div>
        </div>
    @empty
        <p class="muted">Belum ada tanda tangan.</p>
    @endforelse
    @include('partials.pager', ['paginator' => $entries])
@endsection
