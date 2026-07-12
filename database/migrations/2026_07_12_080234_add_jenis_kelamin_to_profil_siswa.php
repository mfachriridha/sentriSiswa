<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Boleh kosong: berkas data siswa dari sekolah kadang punya baris yang kolom
     * L/P-nya belum terisi, dan baris itu tetap harus bisa diimpor.
     */
    public function up(): void
    {
        Schema::table('profil_siswa', function (Blueprint $table) {
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('nis');
        });
    }

    public function down(): void
    {
        Schema::table('profil_siswa', function (Blueprint $table) {
            $table->dropColumn('jenis_kelamin');
        });
    }
};
