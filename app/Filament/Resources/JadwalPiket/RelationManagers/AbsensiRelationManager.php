<?php

namespace App\Filament\Resources\JadwalPiket\RelationManagers;

use App\Enums\StatusAbsensi;
use App\Enums\StatusApprovalIzin;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AbsensiRelationManager extends RelationManager
{
    protected static string $relationship = 'absensi';

    public static function canViewForRecord(Model $ownerRecord, string $panel): bool
    {
        return true;
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Anggota')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('waktu_scan')
                    ->label('Waktu Scan')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('keterangan')
                    ->limit(40)
                    ->placeholder('-')
                    ->toggleable(),
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
            ->filters([
                SelectFilter::make('status')
                    ->options(StatusAbsensi::class),
            ])
            ->recordActions($this->izinActions());
    }

    private function izinActions(): array
    {
        return [
            Action::make('approveIzin')
                ->label('Setujui Izin')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription('Setujui pengajuan izin/sakit anggota ini?')
                ->visible(
                    fn (Model $record) => $record->status_approval_izin === StatusApprovalIzin::Pending &&
                        in_array($record->status, [StatusAbsensi::Izin, StatusAbsensi::Sakit], true)
                )
                ->action(function (Model $record) {
                    $record->update(['status_approval_izin' => StatusApprovalIzin::Approved]);
                }),
            Action::make('rejectIzin')
                ->label('Tolak Izin')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalDescription('Tolak pengajuan? Status kembali ke Belum Absen dan bisa menjadi Alpa bila tidak absen.')
                ->visible(
                    fn (Model $record) => $record->status_approval_izin === StatusApprovalIzin::Pending &&
                        in_array($record->status, [StatusAbsensi::Izin, StatusAbsensi::Sakit], true)
                )
                ->action(function (Model $record) {
                    $record->update([
                        'status_approval_izin' => StatusApprovalIzin::Rejected,
                        'status' => StatusAbsensi::BelumAbsen,
                    ]);
                }),
        ];
    }
}
