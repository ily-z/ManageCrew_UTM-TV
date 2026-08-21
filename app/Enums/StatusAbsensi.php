<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusAbsensi: string implements HasColor, HasLabel
{
    case BelumAbsen = 'belum_absen';
    case Hadir = 'hadir';
    case Terlambat = 'terlambat';
    case Izin = 'izin';
    case Sakit = 'sakit';
    case Alpa = 'alpa';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BelumAbsen => 'Belum Absen',
            self::Hadir => 'Hadir',
            self::Terlambat => 'Terlambat',
            self::Izin => 'Izin',
            self::Sakit => 'Sakit',
            self::Alpa => 'Alpa',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::BelumAbsen => 'gray',
            self::Hadir => 'success',
            self::Terlambat => 'warning',
            self::Izin => 'info',
            self::Sakit => 'info',
            self::Alpa => 'danger',
        };
    }
}
