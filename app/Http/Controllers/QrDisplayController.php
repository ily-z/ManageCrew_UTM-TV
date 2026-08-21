<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QrDisplayController extends Controller
{
    public function __invoke(Request $request)
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $token = $request->user()->createToken('qr-display', ['admin'])->plainTextToken;

        return view('qr-display')->with('qrToken', $token);
    }
}
