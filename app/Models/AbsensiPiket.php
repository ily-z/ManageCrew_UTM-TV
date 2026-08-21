<?php

namespace App\Models;

use App\Enums\StatusAbsensi;
use App\Enums\StatusApprovalIzin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiPiket extends Model
{
    protected $table = 'absensi_piket';
    protected $fillable = [
        'jadwal_piket_id',
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

    public function jadwalPiket(): BelongsTo
    {
        return $this->belongsTo(JadwalPiket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
