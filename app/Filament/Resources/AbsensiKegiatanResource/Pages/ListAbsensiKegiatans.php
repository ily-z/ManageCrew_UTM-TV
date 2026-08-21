<?php

namespace App\Filament\Resources\AbsensiKegiatanResource\Pages;

use App\Filament\Resources\AbsensiKegiatanResource\AbsensiKegiatanResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListAbsensiKegiatans extends ListRecords
{
    protected static string $resource = AbsensiKegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->url(route('admin.absensi.export', ['jenis' => 'kegiatan']))
                ->openUrlInNewTab(),
        ];
    }
}
