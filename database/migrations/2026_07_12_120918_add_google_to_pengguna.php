<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Google memberi id yang tidak pernah berubah meski pemiliknya mengganti
     * alamat Gmail. Itulah yang dipakai mencocokkan akun, bukan emailnya, karena
     * email Google boleh berbeda dari email akun di sekolah ini.
     *
     * Kata sandi dibuat boleh kosong: yang mendaftar lewat Google memang tidak
     * punya kata sandi sampai ia membuatnya sendiri lewat Lupa Kata Sandi atau
     * Ganti Kata Sandi. Membiarkan kata sandi bawaan dari data impor tetap
     * menempel di akun seperti itu sama saja meninggalkan pintu belakang.
     */
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->string('id_google')->nullable()->unique()->after('email');
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropUnique(['id_google']);
            $table->dropColumn('id_google');
        });
    }
};
