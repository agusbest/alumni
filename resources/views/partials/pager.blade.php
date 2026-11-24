@if ($paginator->hasPages())
    @php
        $cur = $paginator->currentPage();
        $last = $paginator->lastPage();
        $from = max(1, $cur - 3);
        $to = min($last, $cur + 3);
    @endphp
    <div class="pager">
        <span>Halaman:</span>
        @if (! $paginator->onFirstPage())
            <a class="btn" href="{{ $paginator->previousPageUrl() }}">&lt;&lt;</a>
        @endif
        @if ($from > 1)
            <a class="btn" href="{{ $paginator->url(1) }}">1</a><span>…</span>
        @endif
        @for ($i = $from; $i <= $to; $i++)
            <a class="btn {{ $i === $cur ? 'on' : '' }}" href="{{ $paginator->url($i) }}">{{ $i }}</a>
        @endfor
        @if ($to < $last)
            <span>…</span><a class="btn" href="{{ $paginator->url($last) }}">{{ $last }}</a>
        @endif
        @if ($paginator->hasMorePages())
            <a class="btn" href="{{ $paginator->nextPageUrl() }}">&gt;&gt;</a>
        @endif
    </div>
@endif
