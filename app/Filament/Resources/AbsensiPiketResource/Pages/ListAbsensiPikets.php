<?php

namespace App\Filament\Resources\AbsensiPiketResource\Pages;

use App\Filament\Resources\AbsensiPiketResource\AbsensiPiketResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListAbsensiPikets extends ListRecords
{
    protected static string $resource = AbsensiPiketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->url(route('admin.absensi.export', ['jenis' => 'piket']))
                ->openUrlInNewTab(),
        ];
    }
}
