<?php

namespace App\Observers;

use App\Models\JadwalPiket;

class JadwalPiketObserver
{
    public function saved(JadwalPiket $jadwal): void
    {
        $jadwal->syncAbsensiRows();
    }
}
