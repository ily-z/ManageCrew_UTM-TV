<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusKegiatan: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Berjalan = 'berjalan';
    case Selesai = 'selesai';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Berjalan => 'Berjalan',
            self::Selesai => 'Selesai',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Berjalan => 'warning',
            self::Selesai => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft => 'heroicon-m-pencil-square',
            self::Berjalan => 'heroicon-m-play-circle',
            self::Selesai => 'heroicon-m-check-circle',
        };
    }
}
