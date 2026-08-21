<?php

namespace App\Filament\Resources\AbsensiRekap\Tables;

use App\Enums\StatusAbsensi;
use App\Enums\StatusApprovalIzin;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class AbsensiRekapTable
{
    /**
     * Actions approve/reject pengajuan izin pada baris absensi.
     */
    public static function approveRejectActions(): array
    {
        return [
            Action::make('approveIzin')
                ->label('Setujui Izin')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(
                    fn ($record) => $record->status_approval_izin === StatusApprovalIzin::Pending &&
                        in_array($record->status, [StatusAbsensi::Izin, StatusAbsensi::Sakit], true)
                )
                ->action(fn ($record) => $record->update(['status_approval_izin' => StatusApprovalIzin::Approved])),
            Action::make('rejectIzin')
                ->label('Tolak Izin')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(
                    fn ($record) => $record->status_approval_izin === StatusApprovalIzin::Pending &&
                        in_array($record->status, [StatusAbsensi::Izin, StatusAbsensi::Sakit], true)
                )
                ->action(fn ($record) => $record->update([
                    'status_approval_izin' => StatusApprovalIzin::Rejected,
                    'status' => StatusAbsensi::BelumAbsen,
                ])),
        ];
    }

    /**
     * Filter umum rekap absensi.
     *
     * @param  callable(Builder, mixed): Builder  $tanggalQuery  fn($query, $date) memodifikasi query berdasarkan tanggal slot
     */
    public static function commonFilters(callable $tanggalQuery): array
    {
        return [
            SelectFilter::make('user')
                ->relationship('user', 'name')
                ->searchable()
                ->preload(),
            SelectFilter::make('status')
                ->options(StatusAbsensi::class),
            Filter::make('rentang_tanggal')
                ->form([
                    DatePicker::make('dari'),
                    DatePicker::make('sampai'),
                ])
                ->query(function (Builder $query, array $data) use ($tanggalQuery) {
                    if ($data['dari'] ?? null) {
                        $query = $tanggalQuery($query, $data['dari'], '>=');
                    }
                    if ($data['sampai'] ?? null) {
                        $query = $tanggalQuery($query, $data['sampai'], '<=');
                    }

                    return $query;
                }),
        ];
    }
}
