<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'no_hp' => ['nullable', 'string', 'max:20'],
            'divisi' => ['nullable', 'string', 'max:100'],
            'angkatan' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        unset($data['foto']);

        if ($request->hasFile('foto')) {
            if ($user->foto && ! str_starts_with($user->foto, 'http')) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('profil', 'public');
        }

        $user->fill($data)->save();

        return response()->json([
            'message' => 'Profil diperbarui.',
            'user' => $user->fresh(),
        ]);
    }
}
