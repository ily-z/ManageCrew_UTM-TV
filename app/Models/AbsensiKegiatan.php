<?php

namespace App\Models;

use App\Enums\StatusAbsensi;
use App\Enums\StatusApprovalIzin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiKegiatan extends Model
{
    protected $table = 'absensi_kegiatan';
    protected $fillable = [
        'kegiatan_id',
        'user_id',
        'waktu_scan',
        'status',
        'keterangan',
        'bukti_file',
        'status_approval_izin',
    ];

    protected function casts(): array
    {
        return [
            'waktu_scan' => 'datetime',
            'status' => StatusAbsensi::class,
            'status_approval_izin' => StatusApprovalIzin::class,
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
