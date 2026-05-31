<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('hadir','terlambat','izin','sakit','alpha','belum_absen') NOT NULL DEFAULT 'belum_absen'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('hadir','terlambat','izin','sakit','alpha') NOT NULL DEFAULT 'hadir'");
    }
};
