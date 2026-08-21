<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis', ['liputan', 'podcast', 'ngonten', 'lainnya'])->default('lainnya');
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('lokasi');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['draft', 'berjalan', 'selesai'])->default('draft');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tanggal', 'status']);
        });

        Schema::create('kegiatan_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tugas');
            $table->timestamps();

            $table->unique(['kegiatan_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('absensi_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('waktu_scan')->nullable();
            $table->enum('status', ['belum_absen', 'hadir', 'terlambat', 'izin', 'sakit', 'alpa'])->default('belum_absen');
            $table->text('keterangan')->nullable();
            $table->string('bukti_file')->nullable();
            $table->enum('status_approval_izin', ['pending', 'approved', 'rejected'])->nullable();
            $table->timestamps();

            $table->unique(['kegiatan_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_kegiatan');
        Schema::dropIfExists('kegiatan_anggota');
        Schema::dropIfExists('kegiatan');
    }
};
