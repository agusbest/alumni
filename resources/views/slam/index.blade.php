@extends('layouts.app')
@section('title', 'Slam Book')
@section('icon', '📒')
@section('content')
    <h3>✍️ Tulis di slam book teman</h3>
    @if ($me)
        <form method="POST" action="{{ route('slam.store') }}">
            @csrf
            <label for="to_profile_id">Pilih teman</label>
            <select class="w" id="to_profile_id" name="to_profile_id">
                @foreach ($friends as $fr)<option value="{{ $fr->id }}" @selected((int) old('to_profile_id') === $fr->id)>{{ $fr->name }} ({{ $fr->angkatan }})</option>@endforeach
            </select>
            <label for="body">Pesanmu</label>
            <textarea id="body" name="body" rows="3" maxlength="400" required placeholder="Eh, masih inget gue?">{{ old('body') }}</textarea>
            <div class="acts"><button class="btn" type="submit">Tulis</button></div>
        </form>
    @else
        <p class="muted">Akun admin tidak bisa menulis di slam book.</p>
    @endif
    <hr>
    <h3>📒 Slam book saya</h3>
    <p class="small">Biodata slam book (cita-cita, geng, guru favorit, dll.) diisi di <a href="{{ route('profile.edit') }}">Profil Saya</a>.</p>
    @forelse ($mine as $m)
        <div class="msg"><div class="by">{{ $m->from?->name ?? 'Alumni (akun dihapus)' }}</div>{{ $m->body }}
            <div class="small muted">{{ $m->created_at->translatedFormat('j F Y') }}</div></div>
    @empty
        <p class="muted">Belum ada tulisan dari teman. Colek mereka dulu!</p>
    @endforelse
@endsection
