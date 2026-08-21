<?php

namespace App\Http\Controllers\Api;

use App\Enums\TipeQr;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        /** @var User|null $user */
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! $user->password || ! Auth::attempt($data)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        return response()->json($this->tokenPayload($user));
    }

    public function googleRedirect(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    public function googleCallback(Request $request): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Throwable $e) {
            $frontend = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173')), '/');
            return redirect()->away("{$frontend}/login?error=google");
        }

        /** @var User|null $user */
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'role' => 'anggota',
                'foto' => $googleUser->getAvatar(),
            ]);
        } else {
            if (! $user->status_aktif) {
                $frontend = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173')), '/');
                return redirect()->away("{$frontend}/login?error=nonaktif");
            }
            $user->google_id ??= $googleUser->getId();
            $user->save();
        }

        $payload = $this->tokenPayload($user);
        $frontend = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173')), '/');

        return redirect()->away(
            "{$frontend}/oauth/callback#token={$payload['token']}"
        );
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $request->user()]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    private function tokenPayload(User $user): array
    {
        $abilities = $user->isAdmin() ? ['admin'] : ['anggota'];

        return [
            'token' => $user->createToken('mobile-web', $abilities)->plainTextToken,
            'user' => $user,
        ];
    }
}
