<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KegiatanAnggota extends Model
{
    protected $table = 'kegiatan_anggota';

    public $timestamps = true;

    protected $fillable = [
        'kegiatan_id',
        'user_id',
        'tugas',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
