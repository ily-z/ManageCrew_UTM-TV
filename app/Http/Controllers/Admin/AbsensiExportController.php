<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusAbsensi;
use App\Http\Controllers\Controller;
use App\Models\AbsensiKegiatan;
use App\Models\AbsensiPiket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AbsensiExportController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        $jenis = $request->query('jenis', 'piket');
        $rows = $jenis === 'kegiatan' ? $this->rowsKegiatan() : $this->rowsPiket();

        return Excel::download(
            new \App\Exports\AbsensiExport($rows),
            'rekap-absensi-'.$jenis.'-'.now()->format('Ymd_His').'.xlsx'
        );
    }

    private function rowsPiket(): array
    {
        return AbsensiPiket::with(['user:id,name,no_induk,divisi', 'jadwalPiket:id,hari,jam_mulai,jam_selesai'])
            ->get()
            ->sortBy(fn ($a) => [$a->jadwalPiket?->hari, $a->user->name])
            ->values()
            ->map(fn ($a) => $this->row(
                $a->user,
                $a->jadwalPiket ? "Piket {$a->jadwalPiket->jam_mulai} - {$a->jadwalPiket->jam_selesai}" : '-',
                $a->jadwalPiket?->hari,
                $a
            ))
            ->all();
    }

    private function rowsKegiatan(): array
    {
        return AbsensiKegiatan::with(['user:id,name,no_induk,divisi', 'kegiatan:id,nama,tanggal,lokasi'])
            ->get()
            ->sortBy(fn ($a) => [$a->kegiatan?->tanggal, $a->user->name])
            ->values()
            ->map(fn ($a) => $this->row(
                $a->user,
                $a->kegiatan ? "{$a->kegiatan->nama} ({$a->kegiatan->lokasi})" : '-',
                $a->kegiatan?->tanggal,
                $a
            ))
            ->all();
    }

    private function row(User $user, string $slot, ?Carbon $tanggal, $absensi): array
    {
        return [
            null, // nomor urut diisi di bawah
            $user->name,
            $user->no_induk ?? '-',
            $user->divisi ?? '-',
            $slot,
            $tanggal?->format('d/m/Y') ?? '-',
            optional($absensi->waktu_scan)->format('H:i') ?? '-',
            is_object($absensi->status) ? $absensi->status->getLabel() : $absensi->status,
            is_object($absensi->status_approval_izin) ? ucfirst($absensi->status_approval_izin->value) : '-',
            str_replace("\n", ' ', (string) $absensi->keterangan),
        ];
    }
}
