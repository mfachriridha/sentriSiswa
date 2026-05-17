<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orang_tua_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->unique()->constrained('siswa')->cascadeOnDelete();
            $table->string('nama_ayah', 255)->nullable();
            $table->string('nama_ibu', 255)->nullable();
            $table->text('alamat_ortu')->nullable();
            $table->string('no_telp_ortu', 20)->nullable();
            $table->string('pekerjaan_ayah', 100)->nullable();
            $table->string('pekerjaan_ibu', 100)->nullable();
            $table->string('nama_wali', 255)->nullable();
            $table->text('alamat_wali')->nullable();
            $table->string('no_telp_wali', 20)->nullable();
            $table->string('pekerjaan_wali', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orang_tua_siswa');
    }
};
