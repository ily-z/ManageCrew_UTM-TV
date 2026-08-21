<?php

namespace App\Filament\Resources\AbsensiPiketResource\Tables;

use App\Enums\StatusAbsensi;
use App\Filament\Resources\AbsensiRekap\Tables\AbsensiRekapTable;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AbsensiPiketTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Anggota')
                    ->searchable(),
                TextColumn::make('jadwalPiket.hari')
                    ->label('Tanggal Piket')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('jadwalPiket.jam_mulai')
                    ->label('Jam')
                    ->state(fn ($record) => $record->jadwalPiket
                        ? substr($record->jadwalPiket->jam_mulai, 0, 5).' - '.substr($record->jadwalPiket->jam_selesai, 0, 5)
                        : '-'),
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
                TextColumn::make('keterangan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters(AbsensiRekapTable::commonFilters(
                fn ($query, $date, $op) => $query->whereRelation('jadwalPiket', 'hari', $op, $date)
            ))
            ->recordActions([
                Action::make('setStatus')
                    ->label('Set Status Manual')
                    ->icon('heroicon-m-pencil-square')
                    ->form([
                        \Filament\Forms\Components\Select::make('status')
                            ->options(StatusAbsensi::class)
                            ->required(),
                    ])
                    ->action(fn (array $data, $record) => $record->update(['status' => $data['status']])),
                ...AbsensiRekapTable::approveRejectActions(),
            ]);
    }
}
