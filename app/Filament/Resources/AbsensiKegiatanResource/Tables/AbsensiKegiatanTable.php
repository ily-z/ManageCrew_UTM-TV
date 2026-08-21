<?php

namespace App\Filament\Resources\AbsensiKegiatanResource\Tables;

use App\Enums\StatusAbsensi;
use App\Filament\Resources\AbsensiRekap\Tables\AbsensiRekapTable;
use Filament\Actions\Action;
use Filament\Forms\Components\Select as FormSelect;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AbsensiKegiatanTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Anggota')
                    ->searchable(),
                TextColumn::make('kegiatan.nama')
                    ->label('Kegiatan')
                    ->searchable(),
                TextColumn::make('kegiatan.tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('waktu_scan')
                    ->label('Waktu Scan')
                    ->dateTime('H:i')
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->badge(),
                IconColumn::make('bukti_file')
                    ->label('Bukti')
                    ->boolean()
                    ->state(fn ($record) => filled($record->bukti_file))
                    ->url(fn ($record) => $record->bukti_file ? asset('storage/'.$record->bukti_file) : null, shouldOpenInNewTab: true),
                TextColumn::make('status_approval_izin')
                    ->label('Approval')
                    ->badge()
                    ->placeholder('-'),
            ])
            ->filters(AbsensiRekapTable::commonFilters(
                fn ($query, $date, $op) => $query->whereRelation('kegiatan', 'tanggal', $op, $date)
            ))
            ->recordActions([
                Action::make('setStatus')
                    ->label('Set Status Manual')
                    ->icon('heroicon-m-pencil-square')
                    ->form([
                        FormSelect::make('status')
                            ->options(StatusAbsensi::class)
                            ->required(),
                    ])
                    ->action(fn (array $data, $record) => $record->update(['status' => $data['status']])),
                ...AbsensiRekapTable::approveRejectActions(),
            ]);
    }
}
