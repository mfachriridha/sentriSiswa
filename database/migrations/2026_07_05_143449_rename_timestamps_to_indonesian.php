<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const array TABLES = [
        'pengguna',
        'profil_guru',
        'kelas',
        'profil_siswa',
        'biodata_siswa',
        'pengaturan',
        'absensi',
        'jenis_pelanggaran',
        'pelanggaran_siswa',
        'pesan_whatsapp',
        'tata_tertib',
        'token_otp',
        'token_akses_absensi',
        'pengajuan_poin',
    ];

    public function up(): void
    {
        $this->rename('created_at', 'dibuat_pada', 'updated_at', 'diperbarui_pada');
    }

    public function down(): void
    {
        $this->rename('dibuat_pada', 'created_at', 'diperbarui_pada', 'updated_at');
    }

    private function rename(string $fromCreated, string $toCreated, string $fromUpdated, string $toUpdated): void
    {
        $sqlite = DB::getDriverName() === 'sqlite';

        foreach (self::TABLES as $table) {
            if ($sqlite) {
                Schema::table($table, function (Blueprint $blueprint) use ($fromCreated, $toCreated, $fromUpdated, $toUpdated) {
                    $blueprint->renameColumn($fromCreated, $toCreated);
                    $blueprint->renameColumn($fromUpdated, $toUpdated);
                });
            } else {
                DB::statement("ALTER TABLE `{$table}`
                    RENAME COLUMN `{$fromCreated}` TO `{$toCreated}`,
                    RENAME COLUMN `{$fromUpdated}` TO `{$toUpdated}`
                ");
            }
        }
    }
};
