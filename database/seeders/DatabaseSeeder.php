<?php

namespace Database\Seeders;

use App\Models\AbsensiKegiatan;
use App\Models\AbsensiPiket;
use App\Models\JadwalPiket;
use App\Models\Kegiatan;
use App\Models\Logbook;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@redaksi.test'],
            [
                'name' => 'Admin Redaksi',
                'password' => 'password',
                'role' => 'admin',
                'status_aktif' => true,
            ]
        );

        // Contoh anggota untuk development (login via password dev).
        $anggotaData = [
            ['name' => 'Budi Reporter', 'email' => 'budi@redaksi.test', 'divisi' => 'liputan', 'angkatan' => '2024', 'no_induk' => 'A001'],
            ['name' => 'Sari Podcaster', 'email' => 'sari@redaksi.test', 'divisi' => 'podcast', 'angkatan' => '2024', 'no_induk' => 'A002'],
            ['name' => 'Dewi Desainer', 'email' => 'dewi@redaksi.test', 'divisi' => 'desain', 'angkatan' => '2025', 'no_induk' => 'A003'],
            ['name' => 'Rizki Videografer', 'email' => 'rizki@redaksi.test', 'divisi' => 'liputan', 'angkatan' => '2025', 'no_induk' => 'A004'],
            ['name' => 'Nina Konten', 'email' => 'nina@redaksi.test', 'divisi' => 'ngonten', 'angkatan' => '2025', 'no_induk' => 'A005'],
        ];

        $anggota = [];
        foreach ($anggotaData as $a) {
            $anggota[] = User::updateOrCreate(
                ['email' => $a['email']],
                [...$a, 'password' => 'password', 'role' => 'anggota', 'status_aktif' => true]
            );
        }

        // Reset tabel demo (users dipertahankan) agar data showcase deterministik.
        Schema::disableForeignKeyConstraints();
        foreach (['nilai', 'logbook', 'absensi_kegiatan', 'kegiatan_anggota', 'kegiatan', 'absensi_piket', 'jadwal_piket_anggota', 'jadwal_piket'] as $table) {
            DB::table($table)->delete();
            DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
        }
        Schema::enableForeignKeyConstraints();

        // ── Kegiatan ────────────────────────────────────────────────
        $kegiatanData = [
            [
                'nama' => 'Liputan Wisuda Universitas',
                'jenis' => 'liputan',
                'tanggal' => now()->subDays(10),
                'waktu_mulai' => '07:30',
                'waktu_selesai' => '13:00',
                'lokasi' => 'Graha Widya Budhi',
                'deskripsi' => 'Dokumentasi prosesi wisuda periode II, liputan tokoh wisudawan terbaik.',
                'status' => 'selesai',
            ],
            [
                'nama' => 'Podcast "Kampus Bercerita" Ep. 12',
                'jenis' => 'podcast',
                'tanggal' => now()->subDays(5),
                'waktu_mulai' => '19:00',
                'waktu_selesai' => '21:00',
                'lokasi' => 'Studio UTM TV',
                'deskripsi' => 'Membahas persiapan karier mahasiswa tingkat akhir bersama alumni.',
                'status' => 'selesai',
            ],
            [
                'nama' => 'Konten Reels Hari Kemerdekaan',
                'jenis' => 'ngonten',
                'tanggal' => now()->subDays(2),
                'waktu_mulai' => '08:00',
                'waktu_selesai' => '12:00',
                'lokasi' => 'Lapangan Utama Kampus',
                'deskripsi' => 'Produksi reels tematis 17-an untuk Instagram dan TikTok.',
                'status' => 'berjalan',
            ],
            [
                'nama' => 'Liputan Seminar Teknologi AI',
                'jenis' => 'liputan',
                'tanggal' => now()->addDays(3),
                'waktu_mulai' => '09:00',
                'waktu_selesai' => '15:00',
                'lokasi' => 'Auditorium Pusat',
                'deskripsi' => 'Coverage seminar nasional AI bersama dosen tamu dari ITB.',
                'status' => 'draft',
            ],
        ];

        $kegiatans = [];
        foreach ($kegiatanData as $k) {
            $kegiatans[] = Kegiatan::create([...$k, 'dibuat_oleh' => $admin->id]);
        };

        // Peserta + absensi kegiatan.
        $tugasPerKegiatan = [
            [['tugas' => 'Kameramen'], ['tugas' => 'Reporter'], ['tugas' => 'Editor']],
            [['tugas' => 'Host'], ['tugas' => 'Sound Engineer'], ['tugas' => 'Editor']],
            [['tugas' => 'Talent'], ['tugas' => 'Kameramen'], ['tugas' => 'Koreografer']],
            [['tugas' => 'Reporter'], ['tugas' => 'Fotografer'], []],
        ];

        $statusPool = ['hadir', 'hadir', 'hadir', 'terlambat', 'izin'];
        foreach ($kegiatans as $index => $kegiatan) {
            $peserta = array_slice($anggota, 0, count($tugasPerKegiatan[$index]));
            foreach ($peserta as $i => $user) {
                if (empty($tugasPerKegiatan[$index][$i])) {
                    continue;
                }
                $kegiatan->anggota()->attach($user->id, ['tugas' => $tugasPerKegiatan[$index][$i]['tugas']]);

                if ($kegiatan->status !== 'draft') {
                    $status = $statusPool[array_rand($statusPool)];
                    AbsensiKegiatan::create([
                        'kegiatan_id' => $kegiatan->id,
                        'user_id' => $user->id,
                        'status' => $status,
                        'waktu_scan' => in_array($status, ['hadir', 'terlambat'])
                            ? $kegiatan->tanggal->setTime(7, rand(30, 59))
                            : null,
                        'keterangan' => $status === 'izin' ? 'Izin keperluan keluarga.' : null,
                        'status_approval_izin' => $status === 'izin' ? 'approved' : null,
                    ]);
                }
            }
            $kegiatan->syncAbsensiRows();
        }

        // ── Piket ───────────────────────────────────────────────────
        $piketSlots = [
            ['hari' => now()->startOfWeek(), 'jam_mulai' => '08:00', 'jam_selesai' => '12:00', 'users' => [$anggota[0], $anggota[1]]],
            ['hari' => now()->startOfWeek()->addDay(), 'jam_mulai' => '13:00', 'jam_selesai' => '17:00', 'users' => [$anggota[2], $anggota[3]]],
            ['hari' => now()->startOfWeek()->addDays(2), 'jam_mulai' => '08:00', 'jam_selesai' => '12:00', 'users' => [$anggota[4], $anggota[0]]],
        ];

        foreach ($piketSlots as $slot) {
            $jadwal = JadwalPiket::create([
                'hari' => $slot['hari'],
                'jam_mulai' => $slot['jam_mulai'],
                'jam_selesai' => $slot['jam_selesai'],
                'dibuat_oleh' => $admin->id,
            ]);
            $jadwal->anggota()->attach(array_column($slot['users'], 'id'));

            foreach ($slot['users'] as $user) {
                AbsensiPiket::create([
                    'jadwal_piket_id' => $jadwal->id,
                    'user_id' => $user->id,
                    'status' => 'hadir',
                    'waktu_scan' => $slot['hari']->copy()->setTimeFromTimeString($slot['jam_mulai'])->addMinutes(rand(0, 20)),
                ]);
            }
        }

        // ── Logbook ─────────────────────────────────────────────────
        $logbooks = [
            [
                'kegiatan' => 0, 'user' => 0, 'status' => 'approved',
                'isi' => 'Meliput prosesi wisuda dari gerbang utama. Mengambil 45 foto momen penting dan mewawancarai 2 wisudawan berprestasi.',
            ],
            [
                'kegiatan' => 0, 'user' => 1, 'status' => 'approved',
                'isi' => 'Mengoperasikan kamera utama di panggung. Koordinasi dengan tim regie berjalan lancar, footage sudah disalurkan untuk editing.',
            ],
            [
                'kegiatan' => 1, 'user' => 1, 'status' => 'approved',
                'isi' => 'Menjadi host episode 12. Rekaman selesai satu take, durasi final 58 menit.',
            ],
            [
                'kegiatan' => 1, 'user' => 2, 'status' => 'revisi',
                'isi' => 'Editing episode 12 progress 80%. Admin: perbaiki audio bagian tengah dan tambahkan intro jingle.',
                'catatan_admin' => 'Audio menit 23-27 pecah, mohon dirapikan lalu submit ulang.',
            ],
            [
                'kegiatan' => 2, 'user' => 4, 'status' => 'pending',
                'isi' => 'Syuting reels 17-an selesai. Total 5 klaster konten direkam, raw file sudah diupload ke Google Drive tim.',
            ],
            [
                'kegiatan' => 2, 'user' => 0, 'status' => 'pending',
                'isi' => 'Backup footage dan seleksi 30 best shots untuk proses color grading.',
            ],
        ];

        foreach ($logbooks as $lb) {
            Logbook::create([
                'kegiatan_id' => $kegiatans[$lb['kegiatan']]->id,
                'user_id' => $anggota[$lb['user']]->id,
                'isi' => $lb['isi'],
                'status' => $lb['status'],
                'catatan_admin' => $lb['catatan_admin'] ?? null,
                'submitted_at' => $kegiatans[$lb['kegiatan']]->tanggal->copy()->addDay(),
            ]);
        }
    }
}
