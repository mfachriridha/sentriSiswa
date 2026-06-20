<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['ringan', 'sedang', 'berat', 'amat_berat'])->index();
            $table->string('nama')->unique();
            $table->unsignedTinyInteger('poin');
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true)->index();
            $table->timestamps();
            $table->index(['kategori', 'aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pelanggaran');
    }
};
