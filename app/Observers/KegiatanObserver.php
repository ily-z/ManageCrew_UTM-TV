<?php

namespace App\Observers;

use App\Models\Kegiatan;

class KegiatanObserver
{
    public function saved(Kegiatan $kegiatan): void
    {
        $kegiatan->syncAbsensiRows();
    }
}
