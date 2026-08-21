<?php

namespace App\Models;

use App\Enums\StatusAbsensi;
use App\Models\AbsensiPiket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalPiket extends Model
{
    protected $table = 'jadwal_piket';

    protected $fillable = [
        'hari',
        'jam_mulai',
        'jam_selesai',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'hari' => 'date',
        ];
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function anggota(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'jadwal_piket_anggota');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(AbsensiPiket::class, 'jadwal_piket_id');
    }

    /**
     * Sinkronkan baris absensi dengan daftar anggota piket saat ini.
     */
    public function syncAbsensiRows(): void
    {
        $userIds = $this->anggota()->pluck('users.id');

        AbsensiPiket::where('jadwal_piket_id', $this->id)
            ->whereNotIn('user_id', $userIds)
            ->delete();

        foreach ($userIds as $userId) {
            AbsensiPiket::firstOrCreate(
                ['jadwal_piket_id' => $this->id, 'user_id' => $userId],
                ['status' => StatusAbsensi::BelumAbsen]
            );
        }
    }
}
