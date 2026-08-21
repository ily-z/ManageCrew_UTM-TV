<?php

namespace App\Models;

use App\Enums\TipeQr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QrSession extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'tipe',
        'reference_id',
        'token',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'tipe' => TipeQr::class,
            'expired_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('expired_at', '>', now());
    }
}
