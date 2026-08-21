<?php

namespace App\Providers;

use App\Models\JadwalPiket;
use App\Models\Kegiatan;
use App\Observers\JadwalPiketObserver;
use App\Observers\KegiatanObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        JadwalPiket::observe(JadwalPiketObserver::class);
        Kegiatan::observe(KegiatanObserver::class);
    }
}
