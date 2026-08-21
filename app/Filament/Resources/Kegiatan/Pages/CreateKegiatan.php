<?php

namespace App\Filament\Resources\Kegiatan\Pages;

use App\Filament\Resources\Kegiatan\KegiatanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKegiatan extends CreateRecord
{
    protected static string $resource = KegiatanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dibuat_oleh'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncAbsensiRows();
    }
}
