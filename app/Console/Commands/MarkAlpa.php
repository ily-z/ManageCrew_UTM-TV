<?php

namespace App\Console\Commands;

use App\Enums\StatusAbsensi;
use App\Models\AbsensiKegiatan;
use App\Models\AbsensiPiket;
use App\Models\JadwalPiket;
use App\Models\Kegiatan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkAlpa extends Command
{
    protected $signature = 'absensi:mark-alpa';

    protected $description = 'Set status Alpa untuk slot piket/kegiatan yang sudah lewat tanpa absen & tanpa izin approved';

    public function handle(): int
    {
        $now = now();

        // --- Backfill: pastikan tiap anggota terjadwal punya baris absensi ---
        JadwalPiket::query()->whereDate('hari', '<=', $now)->get()->each->syncAbsensiRows();
        Kegiatan::query()
            ->whereDate('tanggal', '<=', $now)
            ->whereIn('status', ['berjalan', 'selesai'])
            ->get()->each->syncAbsensiRows();

        // --- Piket ---
        $piketTerlambat = DB::table('absensi_piket as a')
            ->join('jadwal_piket as j', 'j.id', '=', 'a.jadwal_piket_id')
            ->where('a.status', StatusAbsensi::BelumAbsen->value)
            ->where(function ($q) use ($now) {
                $q->whereDate('j.hari', '<', $now->toDateString())
                    ->orWhere(function ($q2) use ($now) {
                        $q2->whereDate('j.hari', '=', $now->toDateString())
                            ->whereTime('j.jam_selesai', '<', $now->format('H:i:s'));
                    });
            })
            ->update([
                'a.status' => StatusAbsensi::Alpa->value,
                'a.updated_at' => $now,
            ]);

        // --- Kegiatan (kecuali masih draft) ---
        $kegiatanTerlambat = DB::table('absensi_kegiatan as a')
            ->join('kegiatan as k', 'k.id', '=', 'a.kegiatan_id')
            ->where('a.status', StatusAbsensi::BelumAbsen->value)
            ->whereIn('k.status', ['berjalan', 'selesai'])
            ->where(function ($q) use ($now) {
                $q->whereDate('k.tanggal', '<', $now->toDateString())
                    ->orWhere(function ($q2) use ($now) {
                        $q2->whereDate('k.tanggal', '=', $now->toDateString())
                            ->whereTime('k.waktu_selesai', '<', $now->format('H:i:s'));
                    });
            })
            ->update([
                'a.status' => StatusAbsensi::Alpa->value,
                'a.updated_at' => $now,
            ]);

        $this->info("Piket ditandai alpa: {$piketTerlambat}, kegiatan ditandai alpa: {$kegiatanTerlambat}");

        return self::SUCCESS;
    }
}
