<?php

namespace App\Filament\Widgets;

use App\Enums\StatusApprovalIzin;
use App\Enums\StatusKegiatan;
use App\Models\AbsensiKegiatan;
use App\Models\AbsensiPiket;
use App\Models\JadwalPiket;
use App\Models\Kegiatan;
use App\Models\Logbook;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $izinPending = AbsensiPiket::where('status_approval_izin', StatusApprovalIzin::Pending->value)->count()
            + AbsensiKegiatan::where('status_approval_izin', StatusApprovalIzin::Pending->value)->count();

        return [
            Stat::make('Anggota Aktif', \App\Models\User::anggota()->aktif()->count())
                ->icon('heroicon-m-users')
                ->color('success'),
            Stat::make('Piket Minggu Ini', JadwalPiket::whereBetween('hari', [$startOfWeek, $endOfWeek])->count())
                ->icon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('Kegiatan Berjalan', Kegiatan::where('status', StatusKegiatan::Berjalan)->count())
                ->icon('heroicon-m-megaphone')
                ->color('warning'),
            Stat::make('Izin Menunggu Approval', $izinPending)
                ->icon('heroicon-m-clock')
                ->color($izinPending > 0 ? 'danger' : 'gray'),
            Stat::make('Logbook Pending', Logbook::where('status', 'pending')->count())
                ->icon('heroicon-m-book-open')
                ->color('info'),
        ];
    }
}
