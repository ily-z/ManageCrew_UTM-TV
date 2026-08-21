<?php

namespace App\Models;

use App\Enums\StatusLogbook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logbook extends Model
{
    protected $table = 'logbook';

    protected $fillable = [
        'kegiatan_id',
        'user_id',
        'isi',
        'file_lampiran',
        'status',
        'catatan_admin',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'status' => StatusLogbook::class,
        ];
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
