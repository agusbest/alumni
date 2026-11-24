<a class="pol" href="{{ route('galeri.show', $p) }}">
    <img loading="lazy" src="{{ $p->photoUrl($foto) }}" alt="Foto {{ $p->name }}">
    <b>{{ $p->name }}</b>
    <small>{{ $p->angkatan }} · {{ $p->kelas }}</small>
    @unless ($p->is_claimed)<i class="tag">belum gabung</i>@endunless
</a>
