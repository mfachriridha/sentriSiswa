<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->ubahEnumStatus(['hadir', 'izin', 'sakit', 'dispensasi', 'alpha', 'belum_absen']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->ubahEnumStatus(['hadir', 'izin', 'sakit', 'alpha', 'belum_absen']);
    }

    /**
     * @param  list<string>  $nilai
     */
    private function ubahEnumStatus(array $nilai): void
    {
        // SQLite (dipakai pengujian) tidak punya enum, jadi tidak ada yang perlu
        // diubah di sana.
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        $daftar = collect($nilai)->map(fn (string $status): string => "'{$status}'")->implode(', ');

        DB::statement("ALTER TABLE absensi MODIFY COLUMN status ENUM({$daftar}) NOT NULL DEFAULT 'belum_absen'");
    }
};
