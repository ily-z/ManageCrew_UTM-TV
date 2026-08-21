<?php

namespace App\Filament\Resources\NilaiResource\Pages;

use App\Filament\Resources\NilaiResource\NilaiResource;
use App\Filament\Resources\NilaiResource\Schemas\NilaiForm;
use Filament\Resources\Pages\CreateRecord;

class CreateNilai extends CreateRecord
{
    protected static string $resource = NilaiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dinilai_oleh'] = auth()->id();

        $kriteria = array_filter($data['kriteria'] ?? [], fn ($v) => $v !== null && $v !== '');
        $data['nilai_total'] = count($kriteria) > 0
            ? round(array_sum($kriteria) / count($kriteria), 2)
            : 0;

        return $data;
    }
}
