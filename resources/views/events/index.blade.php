@extends('layouts.app')
@section('title', 'Reuni & Event')
@section('icon', '🎉')
@section('content')
    @forelse ($events as $e)
        @php $isGoing = auth()->check() && $e->relationLoaded('attendees') && $e->attendees->contains('id', auth()->id()); @endphp
        <div class="panel">
            <div class="ph">{{ $e->title }}</div>
            <div class="cal" data-at="{{ $e->starts_at->toIso8601String() }}">…</div>
            <p><b>{{ $e->starts_at->translatedFormat('j F Y, H:i') }}</b><br>📍 {{ $e->place }}</p>
            @if ($e->description)<p>{{ $e->description }}</p>@endif
            <p><b>{{ $e->attendees_count }}</b> alumni akan hadir @auth: {{ $e->attendees->pluck('name')->implode(', ') ?: '-' }} @endauth</p>
            <div class="acts">
                @auth
                    <form method="POST" action="{{ route('events.rsvp', $e) }}">@csrf
                        <button class="btn {{ $isGoing ? 'on' : '' }}" type="submit">{{ $isGoing ? '✔ Saya hadir (batalkan)' : 'RSVP: Saya hadir' }}</button></form>
                    @if (auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('events.destroy', $e) }}" data-confirm>@csrf @method('DELETE')<button class="btn" type="submit">Hapus acara</button></form>
                    @endif
                @else
                    <span class="small">🔒 <a href="{{ route('login') }}">Masuk</a> untuk RSVP.</span>
                @endauth
            </div>
        </div>
    @empty
        <p class="muted">Belum ada acara.</p>
    @endforelse

    @auth
        @if (auth()->user()->isAdmin())
            <form class="panel" method="POST" action="{{ route('events.store') }}">
                @csrf
                <div class="ph">Tambah Acara</div>
                <label for="title">Judul</label><input class="w" type="text" id="title" name="title" required maxlength="80">
                <label for="starts_at">Tanggal &amp; jam</label><input type="datetime-local" id="starts_at" name="starts_at" required>
                <label for="place">Lokasi</label><input class="w" type="text" id="place" name="place" required maxlength="100">
                <label for="description">Deskripsi</label><textarea id="description" name="description" rows="2" maxlength="300"></textarea>
                <div class="acts"><button class="btn" type="submit">Tambah</button></div>
            </form>
        @endif
    @endauth
@endsection
