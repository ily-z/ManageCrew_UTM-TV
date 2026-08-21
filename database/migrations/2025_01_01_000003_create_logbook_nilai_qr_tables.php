<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbook', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('isi');
            $table->string('file_lampiran')->nullable();
            $table->enum('status', ['pending', 'approved', 'revisi'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->dateTime('submitted_at');
            $table->timestamps();

            $table->index(['kegiatan_id', 'user_id']);
        });

        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->json('kriteria');
            $table->decimal('nilai_total', 5, 2);
            $table->text('catatan')->nullable();
            $table->foreignId('dinilai_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['kegiatan_id', 'user_id']);
        });

        Schema::create('qr_sessions', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['piket', 'kegiatan']);
            $table->unsignedBigInteger('reference_id');
            $table->string('token')->unique();
            $table->dateTime('expired_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tipe', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_sessions');
        Schema::dropIfExists('nilai');
        Schema::dropIfExists('logbook');
    }
};
