<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_poin', function (Blueprint $table) {
            $table->id();
            $table->string('profil_siswa_id', 10);
            $table->foreign('profil_siswa_id')->references('nisn')->on('profil_siswa')->cascadeOnDelete();
            $table->foreignId('diajukan_oleh_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->text('alasan');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->unsignedTinyInteger('jumlah_poin')->nullable();
            $table->foreignId('disetujui_oleh_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_poin');
    }
};
