<?php

use App\Http\Controllers\Admin\AbsensiExportController;
use App\Http\Controllers\QrDisplayController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/export/absensi', AbsensiExportController::class)
    ->middleware(['auth'])
    ->name('admin.absensi.export');

// Layar QR di kantor / lokasi kegiatan (khusus admin)
Route::get('/qr-display', QrDisplayController::class)->middleware(['auth']);

// Named login untuk redirect middleware auth (halaman yang sama dengan SPA)
Route::view('/login', 'app')->name('login');

// SPA anggota (React)
Route::view('/{any}', 'app')->where('any', '^(?!api|admin|storage|build|qr-display).*$');
