<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'no_induk',
        'divisi',
        'angkatan',
        'no_hp',
        'foto',
        'status_aktif',
        'personal_qr_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status_aktif' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function scopeAnggota($query)
    {
        return $query->where('role', UserRole::Anggota);
    }

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    public function jadwalPiket()
    {
        return $this->belongsToMany(JadwalPiket::class, 'jadwal_piket_anggota');
    }

    public function kegiatan()
    {
        return $this->belongsToMany(Kegiatan::class, 'kegiatan_anggota')->withPivot('tugas');
    }

    public function absensiPiket()
    {
        return $this->hasMany(AbsensiPiket::class);
    }

    public function absensiKegiatan()
    {
        return $this->hasMany(AbsensiKegiatan::class);
    }

    public function logbook()
    {
        return $this->hasMany(Logbook::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }
}
