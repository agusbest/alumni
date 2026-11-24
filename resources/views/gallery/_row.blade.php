<dt>{{ $l }}</dt>
@if ($ok)
    <dd>@if (filled($v)){{ $v }}@else<span class="muted">-</span>@endif</dd>
@else
    <dd class="muted">🔒 Disembunyikan oleh pemilik</dd>
@endif
