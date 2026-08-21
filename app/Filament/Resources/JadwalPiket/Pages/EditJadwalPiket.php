<?php

namespace App\Filament\Resources\JadwalPiket\Pages;

use App\Filament\Resources\JadwalPiket\JadwalPiketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJadwalPiket extends EditRecord
{
    protected static string $resource = JadwalPiketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->syncAbsensiRows();
    }
}
