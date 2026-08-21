<?php

namespace App\Filament\Resources\JadwalPiket\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JadwalPiketTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('hari', 'desc')
            ->columns([
                TextColumn::make('hari')
                    ->date('D, d M Y')
                    ->sortable()
                    ->badge(),
                TextColumn::make('jam_mulai')
                    ->label('Mulai'),
                TextColumn::make('jam_selesai')
                    ->label('Selesai'),
                TextColumn::make('anggota_count')
                    ->counts('anggota')
                    ->label('Petugas'),
                TextColumn::make('absensi_hadir_count')
                    ->counts('absensi')
                    ->label('Baris Absensi')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
