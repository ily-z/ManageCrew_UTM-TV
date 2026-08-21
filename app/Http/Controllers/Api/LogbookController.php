<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\StatusLogbook;
use App\Models\Logbook;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogbookController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $logbook = Logbook::with('kegiatan:id,nama,tanggal,status')
            ->where('user_id', $request->user()->id)
            ->latest('submitted_at')
            ->get();

        return response()->json(['logbook' => $logbook]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kegiatan_id' => ['required', 'integer', 'exists:kegiatan,id'],
            'isi' => ['required', 'string', 'min:10'],
            'lampiran' => ['nullable', 'file', 'max:10240'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $peserta = \DB::table('kegiatan_anggota')
            ->where('kegiatan_id', $data['kegiatan_id'])
            ->where('user_id', $user->id)
            ->exists();

        if (! $peserta) {
            return response()->json(['message' => 'Anda bukan peserta kegiatan ini.'], 403);
        }

        if ($request->hasFile('lampiran')) {
            $data['file_lampiran'] = $request->file('lampiran')->store('logbook', 'public');
        }

        $logbook = Logbook::create([
            'kegiatan_id' => $data['kegiatan_id'],
            'user_id' => $user->id,
            'isi' => $data['isi'],
            'file_lampiran' => $data['file_lampiran'] ?? null,
            'status' => StatusLogbook::Pending,
            'submitted_at' => now(),
        ]);

        return response()->json(['message' => 'Logbook terkirim.', 'logbook' => $logbook], 201);
    }

    public function update(Request $request, Logbook $logbook): JsonResponse
    {
        if ($logbook->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($logbook->status === StatusLogbook::Approved) {
            return response()->json(['message' => 'Logbook sudah approved dan tidak bisa diubah.'], 422);
        }

        $data = $request->validate([
            'isi' => ['required', 'string', 'min:10'],
            'lampiran' => ['nullable', 'file', 'max:10240'],
        ]);

        if ($request->hasFile('lampiran')) {
            if ($logbook->file_lampiran) {
                Storage::disk('public')->delete($logbook->file_lampiran);
            }
            $logbook->file_lampiran = $request->file('lampiran')->store('logbook', 'public');
        }

        $logbook->isi = $data['isi'];
        // Setelah revisi & submit ulang, kembali ke pending untuk direview lagi.
        $logbook->status = StatusLogbook::Pending;
        $logbook->submitted_at = now();
        $logbook->save();

        return response()->json(['message' => 'Logbook diperbarui.', 'logbook' => $logbook]);
    }
}
