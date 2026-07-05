<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->resize(15);
    }

    public function down(): void
    {
        $this->resize(20);
    }

    private function resize(int $length): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('profil_guru', function (Blueprint $table) use ($length) {
                $table->string('telepon', $length)->nullable()->change();
            });

            Schema::table('profil_siswa', function (Blueprint $table) use ($length) {
                $table->string('telepon', $length)->nullable()->change();
                $table->string('nis', $length)->change();
            });

            Schema::table('biodata_siswa', function (Blueprint $table) use ($length) {
                $table->string('telepon_ortu', $length)->nullable()->change();
                $table->string('telepon_wali', $length)->nullable()->change();
            });

            Schema::table('pesan_whatsapp', function (Blueprint $table) use ($length) {
                $table->string('telepon_penerima', $length)->change();
            });

            return;
        }

        DB::statement("ALTER TABLE `profil_guru` MODIFY COLUMN `telepon` VARCHAR({$length}) NULL");
        DB::statement("ALTER TABLE `profil_siswa` MODIFY COLUMN `telepon` VARCHAR({$length}) NULL, MODIFY COLUMN `nis` VARCHAR({$length}) NOT NULL");
        DB::statement("ALTER TABLE `biodata_siswa` MODIFY COLUMN `telepon_ortu` VARCHAR({$length}) NULL, MODIFY COLUMN `telepon_wali` VARCHAR({$length}) NULL");
        DB::statement("ALTER TABLE `pesan_whatsapp` MODIFY COLUMN `telepon_penerima` VARCHAR({$length}) NOT NULL");
    }
};
