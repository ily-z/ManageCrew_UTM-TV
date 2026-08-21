<?php

namespace App\Filament\Resources\Kegiatan\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KegiatanTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('nama')
                    ->searchable()
                    ->weight('semibold'),
                TextColumn::make('jenis')
                    ->badge(),
                TextColumn::make('tanggal')
                    ->date('D, d M Y')
                    ->sortable(),
                TextColumn::make('waktu_mulai')
                    ->label('Mulai')
                    ->time('H:i'),
                TextColumn::make('lokasi')
                    ->limit(30)
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('peserta_count')
                    ->counts('peserta')
                    ->label('Peserta'),
            ])
            ->filters([
                SelectFilter::make('jenis')
                    ->options(\App\Enums\JenisKegiatan::class),
                SelectFilter::make('status')
                    ->options(\App\Enums\StatusKegiatan::class),
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
