@extends('layouts.app')
@section('title', 'Peta Alumni')
@section('icon', '🗺️')
@section('content')
    @php
        $W = 560; $H = 280;
        $max = max(1, (int) $cities->max('total'));
        $x = fn ($lon) => ($lon - 94) / (122 - 94) * $W;
        $y = fn ($lat) => (6 - $lat) / 16 * $H;
    @endphp
    <p class="small">Sebaran kota domisili alumni (hanya level kota, dan hanya yang mengizinkan). Peta skematik tanpa peta dasar.</p>
    <div class="inset" style="padding:4px">
        <svg viewBox="0 0 {{ $W }} {{ $H }}" width="100%" role="img" aria-label="Peta skematik sebaran kota alumni">
            <rect width="{{ $W }}" height="{{ $H }}" fill="#e8f4ff"/>
            @foreach ([95, 100, 105, 110, 115, 120] as $lo)<line x1="{{ $x($lo) }}" y1="0" x2="{{ $x($lo) }}" y2="{{ $H }}" stroke="#bcd" stroke-width=".5"/>@endforeach
            @foreach ([5, 0, -5, -10] as $la)<line x1="0" y1="{{ $y($la) }}" x2="{{ $W }}" y2="{{ $y($la) }}" stroke="#bcd" stroke-width=".5"/>@endforeach
            @foreach ($cities as $c)
                @if (isset($coords[$c->city]))
                    @php [$lon, $lat] = $coords[$c->city]; $r = 6 + 10 * $c->total / $max; @endphp
                    <circle cx="{{ $x($lon) }}" cy="{{ $y($lat) }}" r="{{ $r }}" fill="#ff00aa" fill-opacity=".6" stroke="#000080"/>
                    <text x="{{ $x($lon) }}" y="{{ $y($lat) - $r - 2 }}" font-size="9" text-anchor="middle" font-family="Tahoma">{{ $c->city }} ({{ $c->total }})</text>
                @endif
            @endforeach
        </svg>
    </div>
    <table class="rows" style="margin-top:8px">
        <tr><th>Kota</th><th>Alumni</th><th></th></tr>
        @foreach ($cities as $c)
            <tr><td>{{ $c->city }}</td><td>{{ $c->total }}</td><td><span class="bar" style="width:{{ round($c->total / $max * 120) }}px"></span></td></tr>
        @endforeach
    </table>
@endsection
