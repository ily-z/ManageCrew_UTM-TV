<?php

namespace App\Services;

use App\Enums\StatusAbsensi;
use App\Enums\TipeQr;
use App\Models\AbsensiKegiatan;
use App\Models\AbsensiPiket;
use App\Models\JadwalPiket;
use App\Models\Kegiatan;
use App\Models\QrSession;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AbsensiService
{
    public const QR_TTL_SECONDS = 45;

    public const TOLERANSI_TERLAMBAT_MENIT = 15;

    public const BUKA_SEBELUM_MENIT = 60;

    public function getOrCreateActiveToken(TipeQr $tipe, int $referenceId): QrSession
    {
        return QrSession::where('tipe', $tipe->value)
            ->where('reference_id', $referenceId)
            ->aktif()
            ->first()
            ?? $this->generateToken($tipe, $referenceId);
    }

    public function generateToken(TipeQr $tipe, int $referenceId): QrSession
    {
        return QrSession::create([
            'tipe' => $tipe,
            'reference_id' => $referenceId,
            'token' => Str::random(48),
            'expired_at' => now()->addSeconds(self::QR_TTL_SECONDS),
        ]);
    }

    /**
     * Proses scan QR oleh anggota. Mengembalikan model absensi yang tercatat.
     *
     * @return array{0: AbsensiPiket|AbsensiKegiatan, 1: string}
     */
    public function scan(User $user, string $token): array
    {
        $session = QrSession::where('token', trim($token))->first();

        if (! $session || $session->expired_at->isPast()) {
            throw ValidationException::withMessages([
                'token' => 'QR tidak valid atau sudah kedaluwarsa. Scan ulang QR terbaru.',
            ]);
        }

        if (! $user->status_aktif) {
            throw ValidationException::withMessages([
                'token' => 'Akun Anda nonaktif. Hubungi admin.',
            ]);
        }

        return match ($session->tipe) {
            TipeQr::Piket => $this->scanPiket($user, $session),
            TipeQr::Kegiatan => $this->scanKegiatan($user, $session),
        };
    }

    private function scanPiket(User $user, QrSession $session): array
    {
        /** @var JadwalPiket|null $jadwal */
        $jadwal = JadwalPiket::find($session->reference_id);

        if (! $jadwal) {
            throw ValidationException::withMessages(['token' => 'Jadwal piket tidak ditemukan.']);
        }

        $terjadwal = $jadwal->anggota()->where('users.id', $user->id)->exists();
        if (! $terjadwal) {
            throw ValidationException::withMessages([
                'token' => 'Anda tidak terjadwal pada piket ini.',
            ]);
        }

        [$mulai, $selesai] = $this->windowWaktu(
            $jadwal->hari->copy()->setTimeFromTimeString($jadwal->jam_mulai),
            $jadwal->hari->copy()->setTimeFromTimeString($jadwal->jam_selesai)
        );

        $now = now();
        if ($now->lt($mulai)) {
            throw ValidationException::withMessages([
                'token' => 'Window absensi piket belum dibuka ('.$mulai->format('H:i').').',
            ]);
        }
        if ($now->gt($selesai)) {
            throw ValidationException::withMessages([
                'token' => 'Window absensi piket sudah ditutup.',
            ]);
        }

        /** @var AbsensiPiket $absensi */
        $absensi = AbsensiPiket::firstOrCreate(
            ['jadwal_piket_id' => $jadwal->id, 'user_id' => $user->id],
            ['status' => StatusAbsensi::BelumAbsen]
        );

        if (! in_array($absensi->status, [StatusAbsensi::BelumAbsen, StatusAbsensi::Alpa], true)) {
            throw ValidationException::withMessages([
                'token' => "Anda sudah tercatat dengan status '{$absensi->status->getLabel()}' pada piket ini.",
            ]);
        }

        return [$this->catatKehadiran($absensi, $now, $mulai), 'piket'];
    }

    private function scanKegiatan(User $user, QrSession $session): array
    {
        /** @var Kegiatan|null $kegiatan */
        $kegiatan = Kegiatan::find($session->reference_id);

        if (! $kegiatan || $kegiatan->status->value === 'draft') {
            throw ValidationException::withMessages([
                'token' => 'Kegiatan tidak ditemukan / belum aktif.',
            ]);
        }

        $terjadwal = DB::table('kegiatan_anggota')
            ->where('kegiatan_id', $kegiatan->id)
            ->where('user_id', $user->id)
            ->exists();
        if (! $terjadwal) {
            throw ValidationException::withMessages([
                'token' => 'Anda bukan peserta kegiatan ini.',
            ]);
        }

        [$mulai, $selesai] = $this->windowWaktu(
            $kegiatan->tanggal->copy()->setTimeFromTimeString($kegiatan->waktu_mulai),
            $kegiatan->tanggal->copy()->setTimeFromTimeString($kegiatan->waktu_selesai)
        );

        $now = now();
        if ($now->lt($mulai)) {
            throw ValidationException::withMessages([
                'token' => 'Window absensi kegiatan belum dibuka ('.$mulai->format('H:i').').',
            ]);
        }
        if ($now->gt($selesai)) {
            throw ValidationException::withMessages([
                'token' => 'Window absensi kegiatan sudah ditutup.',
            ]);
        }

        /** @var AbsensiKegiatan $absensi */
        $absensi = AbsensiKegiatan::firstOrCreate(
            ['kegiatan_id' => $kegiatan->id, 'user_id' => $user->id],
            ['status' => StatusAbsensi::BelumAbsen]
        );

        if (! in_array($absensi->status, [StatusAbsensi::BelumAbsen, StatusAbsensi::Alpa], true)) {
            throw ValidationException::withMessages([
                'token' => "Anda sudah tercatat dengan status '{$absensi->status->getLabel()}' pada kegiatan ini.",
            ]);
        }

        return [$this->catatKehadiran($absensi, $now, $mulai), 'kegiatan'];
    }

    private function catatKehadiran(AbsensiPiket|AbsensiKegiatan $absensi, CarbonInterface $now, CarbonInterface $mulai): AbsensiPiket|AbsensiKegiatan
    {
        $batasTerlambat = $mulai->copy()->addMinutes(self::TOLERANSI_TERLAMBAT_MENIT);
        $absensi->waktu_scan = $now;
        $absensi->status = $now->lte($batasTerlambat) ? StatusAbsensi::Hadir : StatusAbsensi::Terlambat;
        $absensi->save();

        return $absensi;
    }

    /**
     * @return array{0: \Carbon\CarbonImmutable|\Carbon\Carbon, 1: \Carbon\CarbonImmutable|\Carbon\Carbon}
     */
    private function windowWaktu(CarbonInterface $mulai, CarbonInterface $selesai): array
    {
        return [
            $mulai->copy()->subMinutes(self::BUKA_SEBELUM_MENIT),
            $selesai,
        ];
    }
}
