<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('attendances', function (Blueprint $table) {
                $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha', 'belum_absen'])
                    ->default('belum_absen')
                    ->change();
            });

            return;
        }

        DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('hadir','terlambat','izin','sakit','alpha','belum_absen') NOT NULL DEFAULT 'belum_absen'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('attendances', function (Blueprint $table) {
                $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha'])
                    ->default('hadir')
                    ->change();
            });

            return;
        }

        DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('hadir','terlambat','izin','sakit','alpha') NOT NULL DEFAULT 'hadir'");
    }
};
