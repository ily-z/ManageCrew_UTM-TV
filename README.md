# Sistem Manajemen Anggota Redaksi

Web app untuk mengelola anggota redaksi: jadwal piket + absensi QR rotasi, kegiatan (liputan/podcast/ngonten), logbook, dan penilaian.

Spesifikasi lengkap: [`spesifikasi-sistem-manajemen-anggota.md`](spesifikasi-sistem-manajemen-anggota.md)

## Tech Stack

| Bagian | Teknologi |
|---|---|
| Panel admin | Laravel 12 + Filament v4 (`/admin`) |
| Web app anggota | React 19 + React Router, dibundel Vite di dalam project (`/`) |
| API | Laravel Sanctum (token) — `routes/api.php` |
| Database | MySQL/MariaDB |
| Auth admin | Email/password via login Filament |
| Auth anggota | Google OAuth (Laravel Socialite) + fallback email/password untuk dev |
| Export absensi | Excel `.xlsx` (maatwebsite/excel) |

## Struktur

```
app/
├── Console/Commands/        # absensi:mark-alpa, qr:prune
├── Enums/                   # StatusAbsensi, StatusKegiatan, StatusApprovalIzin, dll
├── Exports/                 # AbsensiExport (xlsx)
├── Filament/
│   ├── Resources/           # Users, JadwalPiket, Kegiatan, Logbook, Nilai,
│   │                        # AbsensiPiketResource, AbsensiKegiatanResource (rekap)
│   └── Widgets/             # StatsOverview dashboard
├── Http/Controllers/
│   ├── Admin/               # export excel rekap
│   └── Api/                 # Auth, Absensi (scan/izin/QR), Anggota, Logbook, Profil
├── Models/
├── Observers/               # sinkron baris absensi saat jadwal/kegiatan berubah
└── Services/AbsensiService.php   # inti logika QR rotasi, window waktu, toleransi telat
resources/js/                # SPA React anggota (login, scan, izin, logbook, nilai, profil)
resources/views/app.blade.php    # shell SPA
resources/views/qr-display.blade.php # layar QR di kantor (admin)
routes/api.php               # endpoint anggota
routes/console.php           # scheduler: mark-alpa tiap 30 menit, qr:prune harian
```

## Setup (Windows/XAMPP)

```bash
# 1. Dependensi PHP
composer install

# 2. Database: buat database 'utmtv3' lalu
php artisan migrate --seed          # seeder membuat akun contoh
php artisan storage:link            # upload foto/bukti/lampiran

# 3. Frontend
npm install
npm run build                       # produksi; atau npm run dev saat development

# 4. Jalankan
php artisan serve                   # http://localhost:8000
php artisan schedule:work           # terminal terpisah: auto-alpa & prune QR
```

Akun bawaan (password semua `password`):

| Email | Role |
|---|---|
| `admin@redaksi.test` | admin (akses `/admin`) |
| `budi@redaksi.test`, `sari@redaksi.test`, `dewi@redaksi.test` | anggota |

## Google OAuth

1. Buat OAuth Client ID (type *Web application*) di Google Cloud Console.
2. Redirect URI: `http://localhost:8000/api/auth/google/callback`
3. Isi `.env`:

```env
GOOGLE_CLIENT_ID=xxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxxx
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback
FRONTEND_URL=http://localhost:5173   # URL SPA saat dev (vite)
```

Sampai credentials diisi, login email/password tetap tersedia untuk anggota.

## Alur Absen QR (rotasi)

1. Buka layar **`/qr-display?tipe=piket&id=<jadwal>`** (atau `tipe=kegiatan`) di tablet/monitor — login sebagai admin. QR otomatis refresh tiap ±45 detik.
2. Anggota buka web app → **Scan QR** → kamera memindai.
3. Backend validasi: token masih aktif, anggota terjadwal, belum absen, dalam window waktu (dibuka 60 menit sebelum mulai).
4. Status: `hadir` jika ≤15 menit dari jam mulai, `terlambat` setelahnya.
5. Token kedaluwarsa tidak bisa dipakai ulang (anti titip absen / screenshot).

Scheduler `absensi:mark-alpa` (tiap 30 menit) mengubah slot yang sudah lewat dari `belum_absen` → `alpa`.

## Keputusan yang Dipakai (dari konfirmasi)

- `nilai_total` = rata-rata sederhana 4 kriteria (partisipasi, ketepatan waktu, kualitas tugas, kualitas logbook).
- Toleransi terlambat = 15 menit (konstanta di `AbsensiService`).
- Export rekap = Excel saja, per jenis (piket/kegiatan), tombol di halaman rekap.
- Anggota nonaktif tidak dihapus — histori tetap ada.

## Development

```bash
npm run dev        # vite dev server :5173 dengan proxy /api ke :8000
php artisan serve  # backend
```

Atau tanpa vite dev server: `npm run build` sekali, akses semuanya lewat `http://localhost:8000`.