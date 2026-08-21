<?php

use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\AnggotaController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LogbookController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::get('/google/redirect', [AuthController::class, 'googleRedirect']);
    Route::get('/google/callback', [AuthController::class, 'googleCallback']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// QR aktif untuk layar display — butuh token admin.
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/me', [ProfileController::class, 'update']);
    Route::post('/absen/scan', [AbsensiController::class, 'scan']);
    Route::post('/absen/izin', [AbsensiController::class, 'ajukanIzin']);
    Route::get('/absen/riwayat', [AbsensiController::class, 'riwayatSaya']);
    Route::get('/qr/aktif', [AbsensiController::class, 'qrAktif'])->middleware('abilities:admin');

    Route::get('/piket/saya', [AnggotaController::class, 'piketSaya']);
    Route::get('/kegiatan/saya', [AnggotaController::class, 'kegiatanSaya']);
    Route::get('/nilai/saya', [AnggotaController::class, 'nilaiSaya']);

    Route::get('/logbook', [LogbookController::class, 'index']);
    Route::post('/logbook', [LogbookController::class, 'store']);
    Route::put('/logbook/{logbook}', [LogbookController::class, 'update']);
});
