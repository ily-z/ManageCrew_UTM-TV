# Spesifikasi: Sistem Manajemen Anggota Redaksi

## 1. Ringkasan

Web app untuk mengelola anggota redaksi: jadwal piket jaga kantor + absensi QR, manajemen kegiatan (liputan/podcast/ngonten), logbook, dan penilaian.

**Tech stack:**
- Backend: Laravel + Filament (panel admin)
- Frontend anggota: React (konsumsi API Laravel, disarankan Laravel Sanctum untuk auth token)
- Database: MySQL
- Auth admin: login biasa (email/password) via Filament
- Auth anggota: Google OAuth (Laravel Socialite)

**Dua aktor:**
- **Admin** — mengelola lewat panel Filament
- **Anggota** — mengakses lewat web app React

---

## 2. Perbaikan Logika dari Catatan Awal

Beberapa hal saya tambahkan/ubah karena di catatan awal masih ada celah:

1. **Status absensi ditambah "Terlambat"** — selain hadir/izin-sakit/belum absen, perlu ada status terlambat supaya bisa dibedakan dari hadir tepat waktu.
2. **Status "Alpa" otomatis** — kalau jadwal piket/kegiatan sudah lewat dan anggota tidak absen & tidak izin, sistem otomatis set status jadi Alpa (lewat scheduled job), bukan selamanya "Belum Absen".
3. **Alur izin/sakit butuh approval** — bukan cuma anggota klaim status sendiri, tapi ajukan izin/sakit (dengan keterangan + opsional bukti), lalu admin approve/reject. Ini mencegah anggota asal-asalan set status sendiri.
4. **QR bersifat rotating/sesi, bukan statis** — supaya QR tidak bisa di-screenshot dan dipakai orang lain buat titip absen (lihat bagian 6).
5. **Kegiatan punya status lifecycle** (draft → berjalan → selesai) — supaya jelas kapan absensi & logbook dibuka/ditutup.
6. **Logbook punya status review** (pending → approved/revisi) — supaya "logbook check" di catatan admin punya alur yang jelas, bukan cuma "dicek" tanpa hasil.
7. **Nilai berbasis kegiatan, bukan global** — setiap anggota dinilai per kegiatan yang diikuti (dengan beberapa kriteria), bukan satu nilai umum. Ini karena tugas/role tiap anggota beda-beda per kegiatan.
8. **Tugas/role per anggota per kegiatan** dicatat eksplisit di tabel pivot, bukan cuma daftar anggota — supaya jelas siapa jadi reporter/fotografer/editor dsb.
9. **Absensi piket vs absensi kegiatan dipisah jadi 2 tabel** — karena konteks & jadwalnya beda (piket = rutin per jadwal, kegiatan = insidental per event).

Kalau ada bagian yang kamu maunya beda dari ini, tinggal edit sebelum dikasih ke coding agent.

---

## 3. Data Model (MySQL)

### `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| name | string | |
| email | string, unique | |
| google_id | string, nullable | untuk anggota (Socialite) |
| password | string, nullable | untuk admin |
| role | enum(admin, anggota) | |
| no_induk | string, nullable | NIS/nomor anggota |
| divisi | string, nullable | mis. liputan, desain, podcast |
| angkatan | string, nullable | |
| no_hp | string, nullable | |
| foto | string, nullable | |
| status_aktif | boolean, default true | |
| personal_qr_token | string, unique, nullable | lihat bagian 6 |

### `jadwal_piket`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| hari | date | tanggal piket (bukan hari rekuren, supaya bisa fleksibel per minggu) |
| jam_mulai | time | |
| jam_selesai | time | |
| dibuat_oleh | FK users | |

### `jadwal_piket_anggota` (pivot)
- jadwal_piket_id (FK)
- user_id (FK)

### `absensi_piket`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| jadwal_piket_id | FK | |
| user_id | FK | |
| waktu_scan | datetime, nullable | null jika belum absen |
| status | enum(belum_absen, hadir, terlambat, izin, sakit, alpa) | default belum_absen |
| keterangan | text, nullable | isi saat izin/sakit |
| bukti_file | string, nullable | |
| status_approval_izin | enum(pending, approved, rejected), nullable | hanya relevan kalau status izin/sakit |

### `kegiatan`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| nama | string | |
| jenis | enum(liputan, podcast, ngonten, lainnya) | |
| tanggal | date | |
| waktu_mulai | time | |
| waktu_selesai | time | |
| lokasi | string | |
| deskripsi | text, nullable | |
| status | enum(draft, berjalan, selesai) | |
| dibuat_oleh | FK users | |

### `kegiatan_anggota` (pivot)
| Kolom | Tipe | Keterangan |
|---|---|---|
| kegiatan_id | FK | |
| user_id | FK | |
| tugas | string | mis. "reporter", "fotografer", "editor" |

### `absensi_kegiatan`
Struktur sama seperti `absensi_piket`, tapi `kegiatan_id` menggantikan `jadwal_piket_id`.

### `logbook`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| kegiatan_id | FK | |
| user_id | FK | |
| isi | text | |
| file_lampiran | string, nullable | |
| status | enum(pending, approved, revisi) | default pending |
| catatan_admin | text, nullable | |
| submitted_at | datetime | |

### `nilai`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| kegiatan_id | FK | |
| user_id | FK | |
| kriteria | json | mis. {"partisipasi":85,"ketepatan_waktu":90,"kualitas_tugas":80,"kualitas_logbook":88} |
| nilai_total | decimal | rata-rata atau weighted, dihitung dari kriteria |
| catatan | text, nullable | |
| dinilai_oleh | FK users | |

### `qr_sessions`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| tipe | enum(piket, kegiatan) | |
| reference_id | bigint | jadwal_piket_id atau kegiatan_id |
| token | string, unique | |
| expired_at | datetime | token rotasi tiap beberapa menit |

---

## 4. Fitur Admin (panel Filament)

1. **Manajemen Anggota**
   - CRUD anggota (nama, email, no induk, divisi, angkatan, no HP, status aktif/nonaktif)
   - Nonaktifkan anggota (bukan hapus, supaya histori absensi/nilai tetap ada)

2. **Jadwal Piket**
   - Buat jadwal piket: pilih tanggal, jam, dan anggota yang bertugas
   - Lihat status absensi tiap slot piket (hadir/terlambat/izin/sakit/alpa/belum absen)
   - Approve/reject pengajuan izin-sakit anggota

3. **Kegiatan**
   - Buat kegiatan: nama, jenis, tanggal, waktu, lokasi, deskripsi
   - Assign anggota + tugas/role masing-masing
   - Ubah status kegiatan (draft → berjalan → selesai)
   - Review logbook per anggota per kegiatan (approve/minta revisi + catatan)
   - Input nilai per anggota per kegiatan (per kriteria)

4. **Absensi**
   - Lihat rekap absensi (piket & kegiatan), filter per anggota/tanggal/jenis/status
   - Cetak/export absensi ke PDF atau Excel

---

## 5. Fitur Anggota (web app React)

1. **Login** via Google OAuth
2. **Absen harian (piket)** — scan QR yang tampil di kantor redaksi, hanya bisa absen kalau sedang terjadwal piket
3. **Ajukan izin/sakit** — kalau tidak bisa piket/hadir kegiatan, isi form + opsional lampiran, status pending sampai admin approve
4. **Absen kegiatan** — scan QR di lokasi kegiatan
5. **Logbook** — isi & submit logbook untuk kegiatan yang diikuti, bisa lihat status review (pending/approved/revisi) dan catatan admin
6. **Lihat nilai** — anggota bisa lihat nilai per kegiatan yang sudah dinilai
7. **Edit data diri** — ubah no HP, foto, dsb (email/nama dari Google tidak bisa diubah manual)

---

## 6. Mekanisme Absen QR

Supaya tidak bisa dititipkan (satu orang scan buat banyak orang), pakai skema **QR rotasi**, bukan QR statis per anggota:

1. Saat jadwal piket/kegiatan aktif dan dalam window waktu absen, sistem generate token unik di tabel `qr_sessions` dengan masa berlaku singkat (mis. 30–60 detik).
2. QR ditampilkan di layar/tablet di kantor redaksi (atau lokasi kegiatan), berisi token tsb, dan otomatis refresh sebelum expired.
3. Anggota membuka web app di HP-nya (sudah login), scan QR pakai kamera (browser `getUserMedia` + library seperti `html5-qrcode` di React).
4. App kirim token + user_id (dari sesi login) ke backend → backend validasi: token masih berlaku, anggota memang terjadwal, dan belum absen di slot itu → catat `absensi_piket`/`absensi_kegiatan` dengan status `hadir` atau `terlambat` (kalau waktu scan lewat dari jam_mulai + toleransi, mis. 15 menit).
5. Token lama tidak bisa dipakai ulang setelah expired, mencegah screenshot QR dipakai lagi nanti.

**Catatan implementasi:** ini asumsi desain saya karena "scan QR pakai kamera HP anggota" bisa berarti QR ditampilkan di lokasi (yang saya pakai di atas) — kalau ternyata maksudnya tiap anggota punya QR pribadi yang di-scan oleh admin/petugas, tinggal balik alurnya pakai kolom `personal_qr_token` yang sudah disiapkan di tabel `users`.

---

## 7. Job Terjadwal (Scheduler)

- **Auto-alpa**: setiap slot piket/kegiatan yang window absennya sudah lewat tapi status masih `belum_absen` dan tidak ada pengajuan izin/sakit yang approved → ubah jadi `alpa`.
- **Rotasi QR**: generate token baru di `qr_sessions` tiap X detik selama window absen kegiatan/piket sedang aktif.
- (Opsional, untuk pengembangan lanjutan) reminder H-1 ke anggota yang terjadwal piket besok.

---

## 8. Hal yang Perlu Dikonfirmasi Lagi ke Coding Agent / Saat Development

- Bobot/weighting tiap kriteria nilai (partisipasi, ketepatan waktu, dll) — apa disamakan rata atau ada bobot beda?
- Toleransi keterlambatan (berapa menit dianggap masih "hadir" vs "terlambat")?
- Format cetak absensi (PDF layout seperti apa, per anggota atau per tanggal)?
- Apakah anggota nonaktif tetap muncul di histori kegiatan lama (untuk integritas data) — disarankan iya, soft-delete saja.
