<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_piket', function (Blueprint $table) {
            $table->id();
            $table->date('hari');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('jadwal_piket_anggota', function (Blueprint $table) {
            $table->foreignId('jadwal_piket_id')->constrained('jadwal_piket')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['jadwal_piket_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('absensi_piket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_piket_id')->constrained('jadwal_piket')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('waktu_scan')->nullable();
            $table->enum('status', ['belum_absen', 'hadir', 'terlambat', 'izin', 'sakit', 'alpa'])->default('belum_absen');
            $table->text('keterangan')->nullable();
            $table->string('bukti_file')->nullable();
            $table->enum('status_approval_izin', ['pending', 'approved', 'rejected'])->nullable();
            $table->timestamps();

            $table->unique(['jadwal_piket_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_piket');
        Schema::dropIfExists('jadwal_piket_anggota');
        Schema::dropIfExists('jadwal_piket');
    }
};
