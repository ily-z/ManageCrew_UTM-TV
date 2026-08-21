<?php

namespace App\Filament\Resources\NilaiResource\Pages;

use App\Filament\Resources\NilaiResource\NilaiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNilai extends EditRecord
{
    protected static string $resource = NilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $kriteria = array_filter($data['kriteria'] ?? [], fn ($v) => $v !== null && $v !== '');
        $data['nilai_total'] = count($kriteria) > 0
            ? round(array_sum($kriteria) / count($kriteria), 2)
            : 0;

        return $data;
    }
}
