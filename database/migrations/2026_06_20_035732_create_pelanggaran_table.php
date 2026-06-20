<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();
            $table->foreignId('jenis_id')
                ->nullable()
                ->constrained('jenis_pelanggaran')
                ->nullOnDelete();
            $table->foreignId('dicatat_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->date('tanggal')->index();
            $table->string('nama_pelanggaran');
            $table->enum('kategori', ['ringan', 'sedang', 'berat', 'amat_berat'])->index();
            $table->unsignedTinyInteger('poin');
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('disetujui')->index();
            $table->foreignId('disetujui_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->index(['siswa_id', 'tanggal']);
            $table->index(['jenis_id', 'tanggal']);
            $table->index(['kategori', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran');
    }
};
