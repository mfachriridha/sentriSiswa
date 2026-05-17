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
        Schema::create('biodata_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->unique()->constrained('siswa')->cascadeOnDelete();
            $table->string('foto')->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama', 20)->nullable();
            $table->string('status_keluarga', 25)->nullable();
            $table->tinyInteger('anak_ke')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp_rumah', 20)->nullable();
            $table->string('sekolah_asal', 100)->nullable();
            $table->string('diterima_kelas', 50)->nullable();
            $table->date('diterima_tanggal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biodata_siswa');
    }
};
