<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_pengajuan_poin', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('grup'); // Eksternal, Internal Sekolah, Kontribusi & Kedisiplinan
            $table->integer('poin');
            $table->integer('urutan')->default(0);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::table('pengajuan_poin', function (Blueprint $table) {
            $table->foreignId('kategori_pengajuan_poin_id')->nullable()->after('profil_siswa_id')->constrained('kategori_pengajuan_poin')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_poin', function (Blueprint $table) {
            $table->dropForeign(['kategori_pengajuan_poin_id']);
            $table->dropColumn('kategori_pengajuan_poin_id');
        });

        Schema::dropIfExists('kategori_pengajuan_poin');
    }
};
