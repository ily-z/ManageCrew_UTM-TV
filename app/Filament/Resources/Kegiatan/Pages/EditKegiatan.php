<?php

namespace App\Filament\Resources\Kegiatan\Pages;

use App\Filament\Resources\Kegiatan\KegiatanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKegiatan extends EditRecord
{
    protected static string $resource = KegiatanResource::class;

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
