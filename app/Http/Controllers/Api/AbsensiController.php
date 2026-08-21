<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusAbsensi;
use App\Enums\StatusApprovalIzin;
use App\Enums\TipeQr;
use App\Http\Controllers\Controller;
use App\Models\AbsensiKegiatan;
use App\Models\AbsensiPiket;
use App\Models\User;
use App\Services\AbsensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function __construct(private AbsensiService $absensiService)
    {
    }

    public function scan(Request $request): JsonResponse
    {
        $data = $request->validate(['token' => ['required', 'string']]);

        [$absensi, $tipe] = $this->absensiService->scan($request->user(), $data['token']);

        return response()->json([
            'message' => "Absensi {$tipe} berhasil dicatat.",
            'status' => $absensi->status,
            'waktu_scan' => optional($absensi->waktu_scan)->format('Y-m-d H:i:s'),
            'tipe' => $tipe,
        ]);
    }

    public function ajukanIzin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tipe' => ['required', 'in:piket,kegiatan'],
            'reference_id' => ['required', 'integer'],
            'jenis' => ['required', 'in:izin,sakit'],
            'keterangan' => ['required', 'string', 'max:2000'],
            'bukti' => ['nullable', 'file', 'image', 'max:4096'],
        ]);

        /** @var User $user */
        $user = $request->user();

        if ($data['tipe'] === 'piket') {
            $absensi = AbsensiPiket::firstOrCreate(
                ['jadwal_piket_id' => $data['reference_id'], 'user_id' => $user->id],
                ['status' => StatusAbsensi::BelumAbsen]
            );
        } else {
            $absensi = AbsensiKegiatan::firstOrCreate(
                ['kegiatan_id' => $data['reference_id'], 'user_id' => $user->id],
                ['status' => StatusAbsensi::BelumAbsen]
            );
        }

        if (in_array($absensi->status, [StatusAbsensi::Hadir, StatusAbsensi::Terlambat], true)) {
            return response()->json([
                'message' => 'Anda sudah absen pada slot ini, tidak perlu mengajukan izin.',
            ], 422);
        }

        if ($request->hasFile('bukti')) {
            $path = $request->file('bukti')->store('izin', 'public');
            $absensi->bukti_file = $path;
        }

        $absensi->status = $data['jenis'] === 'sakit' ? StatusAbsensi::Sakit : StatusAbsensi::Izin;
        $absensi->keterangan = $data['keterangan'];
        $absensi->status_approval_izin = StatusApprovalIzin::Pending;
        $absensi->save();

        return response()->json([
            'message' => 'Pengajuan izin terkirim. Menunggu persetujuan admin.',
            'status_approval_izin' => $absensi->status_approval_izin,
        ]);
    }

    /**
     * QR aktif untuk layar display di kantor/lokasi kegiatan (khusus admin).
     */
    public function qrAktif(Request $request): JsonResponse
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'tipe' => ['required', 'in:piket,kegiatan'],
            'id' => ['required', 'integer'],
        ]);

        $session = $this->absensiService->getOrCreateActiveToken(
            TipeQr::from($data['tipe']),
            (int) $data['id']
        );

        return response()->json([
            'token' => $session->token,
            'expired_at' => $session->expired_at->toIso8601String(),
            'ttl' => max(0, round(now()->diffInSeconds($session->expired_at))),
        ]);
    }

    public function riwayatSaya(Request $request): JsonResponse
    {
        $user = $request->user();

        $piket = AbsensiPiket::with('jadwalPiket:id,hari,jam_mulai,jam_selesai')
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->get();

        $kegiatan = AbsensiKegiatan::with('kegiatan:id,nama,tanggal,waktu_mulai,waktu_selesai')
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->get();

        return response()->json(compact('piket', 'kegiatan'));
    }
}
