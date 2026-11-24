@extends('layouts.app')
@section('title', 'My Computer - Tentang')
@section('icon', '💻')
@section('content')
    <p class="logo" style="font-size:14px">{{ config('reuni.name') }}</p>
    <p>Aplikasi alumni <b>{{ config('reuni.school') }}</b> bernuansa Windows 98 untuk angkatan 2000-an.</p>
    <h3>Teknologi</h3>
    <ul><li>Laravel 12, PHP 8.2+, MySQL</li><li>Foto disimpan di disk privat dan dilayani lewat controller dengan aturan privasi</li><li>Privasi per kolom ditegakkan di server (tampilan, filter, peta, dan foto)</li></ul>
@endsection
