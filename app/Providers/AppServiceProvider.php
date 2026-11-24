<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Nama bulan/hari berbahasa Indonesia untuk translatedFormat().
        Carbon::setLocale(config('app.locale', 'id'));

        // Di balik proxy hosting (Render/Railway) semua link harus https.
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
