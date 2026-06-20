<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biodata_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('agama', 50)->nullable();
            $table->string('anak_ke', 10)->nullable();
            $table->string('sekolah_asal', 255)->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->string('nama_ayah', 255)->nullable();
            $table->string('pekerjaan_ayah', 255)->nullable();
            $table->string('nama_ibu', 255)->nullable();
            $table->string('pekerjaan_ibu', 255)->nullable();
            $table->text('alamat_ortu')->nullable();
            $table->string('telepon_ortu', 20)->nullable();
            $table->string('nama_wali', 255)->nullable();
            $table->string('pekerjaan_wali', 255)->nullable();
            $table->text('alamat_wali')->nullable();
            $table->string('telepon_wali', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biodata_siswa');
    }
};
