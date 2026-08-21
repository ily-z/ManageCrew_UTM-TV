<?php

namespace App\Models;

use App\Enums\JenisKegiatan;
use App\Enums\StatusAbsensi;
use App\Enums\StatusKegiatan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    protected $fillable = [
        'nama',
        'jenis',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'deskripsi',
        'status',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jenis' => JenisKegiatan::class,
            'status' => StatusKegiatan::class,
        ];
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function anggota(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kegiatan_anggota')->withPivot('tugas');
    }

    public function peserta()
    {
        return $this->hasMany(KegiatanAnggota::class, 'kegiatan_id');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(AbsensiKegiatan::class, 'kegiatan_id');
    }

    public function logbook(): HasMany
    {
        return $this->hasMany(Logbook::class, 'kegiatan_id');
    }

    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    /**
     * Sinkronkan baris absensi kegiatan dengan daftar peserta saat ini.
     */
    public function syncAbsensiRows(): void
    {
        $userIds = $this->anggota()->pluck('users.id');

        AbsensiKegiatan::where('kegiatan_id', $this->id)
            ->whereNotIn('user_id', $userIds)
            ->delete();

        foreach ($userIds as $userId) {
            AbsensiKegiatan::firstOrCreate(
                ['kegiatan_id' => $this->id, 'user_id' => $userId],
                ['status' => StatusAbsensi::BelumAbsen]
            );
        }
    }
}
