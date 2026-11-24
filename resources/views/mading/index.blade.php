@extends('layouts.app')
@section('title', 'Mading Online')
@section('icon', '📌')
@section('content')
    @auth
        <form class="panel" method="POST" action="{{ route('mading.store') }}">
            @csrf
            <div class="ph">Tempel di Mading</div>
            <label for="title">Judul</label><input class="w" type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="80">
            <label for="body">Isi</label><textarea id="body" name="body" rows="3" maxlength="600" required>{{ old('body') }}</textarea>
            <div class="acts"><button class="btn" type="submit">Tempel</button></div>
        </form>
    @else
        <p class="small">🔒 Pengunjung hanya bisa membaca. <a href="{{ route('login') }}">Masuk</a> untuk menulis, memberi reaksi, dan berkomentar.</p>
    @endauth

    @foreach ($posts as $post)
        <article class="post {{ $post->pinned ? 'pin' : '' }}">
            <div class="px" style="color:#000080">{{ $post->pinned ? '📌 ' : '' }}{{ $post->title }}</div>
            <div class="small muted">{{ $post->user?->isAdmin() ? 'Admin Reuni' : $post->user?->name }} · {{ $post->created_at->translatedFormat('j F Y') }}</div>
            <p>{!! nl2br(e($post->body)) !!}</p>

            <div class="rx">
                @foreach (\App\Models\Post::EMOJIS as $e)
                    @php
                        $count = $post->reactions->where('emoji', $e)->count();
                        $mine = auth()->check() && $post->reactions->where('emoji', $e)->where('user_id', auth()->id())->isNotEmpty();
                    @endphp
                    @auth
                        <form method="POST" action="{{ route('mading.react', $post) }}">@csrf<input type="hidden" name="emoji" value="{{ $e }}">
                            <button class="btn {{ $mine ? 'on' : '' }}" type="submit" aria-label="Reaksi {{ $e }}">{{ $e }} {{ $count }}</button></form>
                    @else
                        <span class="btn" aria-label="{{ $count }} reaksi {{ $e }}">{{ $e }} {{ $count }}</span>
                    @endauth
                @endforeach
                @auth
                    @if (auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('mading.pin', $post) }}">@csrf<button class="btn" type="submit">{{ $post->pinned ? 'Lepas Pin' : '📌 Pin' }}</button></form>
                    @endif
                    @if ($post->user_id !== auth()->id() && ! auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('mading.report', $post) }}">@csrf<button class="btn" type="submit" aria-label="Laporkan postingan">🚩</button></form>
                    @endif
                    @if ($post->user_id === auth()->id() || auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('mading.destroy', $post) }}" data-confirm>@csrf @method('DELETE')<button class="btn" type="submit">Hapus</button></form>
                    @endif
                @endauth
            </div>

            @foreach ($post->comments as $c)
                <div class="msg"><span class="by">{{ $c->user?->isAdmin() ? 'Admin Reuni' : $c->user?->name }}</span>: {{ $c->body }}
                    <span class="small muted">{{ $c->created_at->translatedFormat('j F Y') }}</span>
                    @auth
                        @if ($c->user_id === auth()->id() || auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('mading.comment.destroy', $c) }}" style="display:inline">@csrf @method('DELETE')<button class="btn" type="submit" aria-label="Hapus komentar">x</button></form>
                        @endif
                    @endauth
                </div>
            @endforeach
            @auth
                <form method="POST" action="{{ route('mading.comment', $post) }}" style="display:flex;gap:4px">
                    @csrf
                    <input type="text" name="body" required maxlength="200" placeholder="Tulis komentar..." aria-label="Komentar" style="flex:1">
                    <button class="btn" type="submit">Kirim</button>
                </form>
            @endauth
        </article>
    @endforeach

    @include('partials.pager', ['paginator' => $posts])
@endsection
