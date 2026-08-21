<?php

namespace App\Filament\Resources\JadwalPiket\Pages;

use App\Filament\Resources\JadwalPiket\JadwalPiketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJadwalPiket extends CreateRecord
{
    protected static string $resource = JadwalPiketResource::class;

    protected function afterCreate(): void
    {
        $this->record->syncAbsensiRows();
    }
}
