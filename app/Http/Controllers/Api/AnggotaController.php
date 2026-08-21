<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalPiket;
use App\Models\Kegiatan;
use App\Models\Nilai;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function piketSaya(Request $request): JsonResponse
    {
        $jadwal = JadwalPiket::with(['absensi' => fn ($q) => $q->where('user_id', $request->user()->id)])
            ->whereHas('anggota', fn ($q) => $q->where('users.id', $request->user()->id))
            ->orderByDesc('hari')
            ->get()
            ->map(fn (JadwalPiket $j) => [
                'id' => $j->id,
                'hari' => $j->hari->format('Y-m-d'),
                'jam_mulai' => $j->jam_mulai,
                'jam_selesai' => $j->jam_selesai,
                'absensi' => $j->absensi->first(),
            ]);

        return response()->json(['jadwal_piket' => $jadwal]);
    }

    public function kegiatanSaya(Request $request): JsonResponse
    {
        $kegiatan = Kegiatan::with([
            'absensi' => fn ($q) => $q->where('user_id', $request->user()->id),
            'logbook' => fn ($q) => $q->where('user_id', $request->user()->id),
            'nilai' => fn ($q) => $q->where('user_id', $request->user()->id),
        ])
            ->whereHas('anggota', fn ($q) => $q->where('users.id', $request->user()->id))
            ->orderByDesc('tanggal')
            ->get()
            ->map(function (Kegiatan $k) use ($request) {
                return [
                    'id' => $k->id,
                    'nama' => $k->nama,
                    'jenis' => $k->jenis,
                    'tanggal' => $k->tanggal->format('Y-m-d'),
                    'waktu_mulai' => $k->waktu_mulai,
                    'waktu_selesai' => $k->waktu_selesai,
                    'lokasi' => $k->lokasi,
                    'deskripsi' => $k->deskripsi,
                    'status' => $k->status,
                    'tugas_saya' => $k->anggota->firstWhere('id', $request->user()->id)?->pivot?->tugas
                        ?? \DB::table('kegiatan_anggota')
                            ->where('kegiatan_id', $k->id)
                            ->where('user_id', $request->user()->id)
                            ->value('tugas'),
                    'absensi' => $k->absensi->first()?->only(['status', 'waktu_scan']),
                    'logbook' => $k->logbook->first(),
                    'nilai' => $k->nilai->first(),
                ];
            });

        return response()->json(['kegiatan' => $kegiatan]);
    }

    public function nilaiSaya(Request $request): JsonResponse
    {
        $nilai = Nilai::with('kegiatan:id,nama,tanggal,jenis')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $rataRata = round((float) $nilai->avg('nilai_total'), 2);

        return response()->json(['nilai' => $nilai, 'rata_rata' => $rataRata]);
    }
}
