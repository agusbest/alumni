<?php

return [
    'name' => env('REUNI_NAME', 'Reuni2K.net'),
    'school' => env('REUNI_SCHOOL', 'SMA Negeri 1 Contoh'),
    'tagline' => 'Balik ke 2000-an!',

    // Disk penyimpanan foto: 'local' = storage/app/private (privat, disajikan lewat controller)
    'disk' => env('REUNI_DISK', 'local'),

    // Tahun lulus yang tersedia (angkatan 2000-an)
    'years' => range(2000, 2009),

    'classes' => ['3 IPA 1', '3 IPA 2', '3 IPS 1', '3 IPS 2'],

    'ledger_target' => (int) env('REUNI_TARGET', 25000000),

    // Tingkat privasi per kolom
    'vis_levels' => [
        'public' => 'Publik',
        'alumni' => 'Hanya Alumni',
        'angkatan' => 'Hanya Angkatan Saya',
        'hidden' => 'Disembunyikan',
    ],

    // Koordinat kasar (bujur, lintang) untuk peta skematik
    'cities' => [
        'Jakarta' => [106.8, -6.2], 'Bekasi' => [107.0, -6.25], 'Bandung' => [107.6, -6.9],
        'Semarang' => [110.4, -7.0], 'Yogyakarta' => [110.4, -7.8], 'Surabaya' => [112.7, -7.25],
        'Malang' => [112.6, -7.98], 'Denpasar' => [115.2, -8.65], 'Medan' => [98.7, 3.6],
        'Palembang' => [104.7, -3.0], 'Balikpapan' => [116.8, -1.27], 'Makassar' => [119.4, -5.15],
        'Kuala Lumpur' => [101.7, 3.1], 'Pontianak' => [109.3, -0.03],
    ],

    'photo' => ['w' => 300, 'h' => 400, 'album_w' => 640, 'album_h' => 480, 'max_kb' => 5120],
];
