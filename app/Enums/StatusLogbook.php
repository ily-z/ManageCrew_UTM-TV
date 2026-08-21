<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusLogbook: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Revisi = 'revisi';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Revisi => 'Perlu Revisi',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Revisi => 'danger',
        };
    }
}
