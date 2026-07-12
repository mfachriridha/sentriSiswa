<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Status Terlambat dihapus. Sekolah hanya peduli siswa absen di dalam jam
     * absen atau tidak; memisahkan yang datang menit-menit terakhir tidak pernah
     * dipakai untuk apa pun.
     *
     * Catatan lama diubah jadi Hadir, bukan dibuang: orangnya memang datang dan
     * absen di dalam jam absen. Persentase kehadiran pun tidak berubah, karena
     * Terlambat selama ini memang sudah dihitung sebagai hadir.
     *
     * Toleransi keterlambatan ikut dihapus dari pengaturan: satu-satunya gunanya
     * adalah memisahkan Hadir dari Terlambat.
     */
    public function up(): void
    {
        DB::table('absensi')->where('status', 'terlambat')->update(['status' => 'hadir']);

        $this->ubahEnumStatus(['hadir', 'izin', 'sakit', 'alpha', 'belum_absen']);

        DB::table('pengaturan')
            ->whereIn('kunci', ['attendance_late_tolerance_minutes', 'attendance_late_time'])
            ->delete();
    }

    public function down(): void
    {
        $this->ubahEnumStatus(['hadir', 'terlambat', 'izin', 'sakit', 'alpha', 'belum_absen']);
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
