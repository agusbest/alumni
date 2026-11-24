@extends('layouts.app')
@section('title', 'Iuran & Donasi Reuni')
@section('icon', '💰')
@section('content')
    @php
        $pc = min(100, (int) round($in / max(1, $target) * 100));
        $rp = fn ($n) => 'Rp '.number_format($n, 0, ',', '.');
    @endphp
    <p class="small">Pencatatan transparan. Aplikasi ini <b>tidak memproses pembayaran</b>; admin mencatat pemasukan/pengeluaran secara manual.</p>
    <div class="panel">
        <b>Terkumpul:</b> {{ $rp($in) }} dari target {{ $rp($target) }} ({{ $pc }}%)
        <div class="prog" style="margin:6px 0">@for ($i = 0; $i < 20; $i++)<i class="{{ $i < round($pc / 5) ? '' : 'off' }}"></i>@endfor</div>
        <b>Pengeluaran:</b> {{ $rp($out) }} · <b>Saldo:</b> {{ $rp($in - $out) }}
    </div>

    <table class="rows">
        <tr><th>Tanggal</th><th>Nama</th><th>Keterangan</th><th>Jumlah</th></tr>
        @foreach ($entries as $x)
            <tr>
                <td>{{ $x->date->translatedFormat('j M Y') }}</td><td>{{ $x->name }}</td><td>{{ $x->note }}</td>
                <td style="color:{{ $x->type === 'in' ? '#006400' : '#a00000' }}">{{ $x->type === 'in' ? '+' : '-' }}{{ $rp($x->amount) }}
                    @if (auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('ledger.destroy', $x) }}" style="display:inline" data-confirm>@csrf @method('DELETE')<button class="btn" type="submit" aria-label="Hapus catatan">x</button></form>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    @if (auth()->user()->isAdmin())
        <form class="panel" method="POST" action="{{ route('ledger.store') }}" style="margin-top:10px">
            @csrf
            <div class="ph">Catat Transaksi</div>
            <label for="name">Nama</label><input type="text" id="name" name="name" required maxlength="60">
            <label for="amount">Jumlah (Rp)</label><input type="number" id="amount" name="amount" min="1" required>
            <label for="type">Jenis</label><select id="type" name="type"><option value="in">Pemasukan</option><option value="out">Pengeluaran</option></select>
            <label for="note">Keterangan</label><input class="w" type="text" id="note" name="note" maxlength="80">
            <div class="acts"><button class="btn" type="submit">Catat</button></div>
        </form>
    @endif
@endsection
