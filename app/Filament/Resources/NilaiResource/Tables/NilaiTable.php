<?php

namespace App\Filament\Resources\NilaiResource\Tables;

use App\Models\Nilai;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NilaiTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('kegiatan.nama')
                    ->label('Kegiatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Anggota')
                    ->searchable(),
                TextColumn::make('partisipasi')
                    ->state(fn (Nilai $record) => $record->kriteria['partisipasi'] ?? '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ketepatan_waktu')
                    ->state(fn (Nilai $record) => $record->kriteria['ketepatan_waktu'] ?? '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('kualitas_tugas')
                    ->state(fn (Nilai $record) => $record->kriteria['kualitas_tugas'] ?? '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('kualitas_logbook')
                    ->state(fn (Nilai $record) => $record->kriteria['kualitas_logbook'] ?? '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nilai_total')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('penilai.name')
                    ->label('Dinilai Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }
}
