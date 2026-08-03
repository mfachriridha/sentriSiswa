<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ResetAbsensiHariIniCommand extends Command
{
    /**
     * Nama dan tanda tangan perintah console.
     *
     * @var string
     */
    protected $signature = 'absensi:reset-hari-ini
                            {--siswa= : NISN atau nama siswa tertentu (opsional, tanpa ini = semua siswa)}';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Reset semua data presensi hari ini supaya bisa testing ulang (DEV only)';

    /**
     * Eksekusi perintah console.
     */
    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Command ini tidak boleh dijalankan di production!');

            return self::FAILURE;
        }

        $tanggal = now()->toDateString();

        $query = Absensi::whereDate('tanggal', $tanggal);

        $siswaFilter = $this->option('siswa');
        if ($siswaFilter) {
            $query->whereHas('profilSiswa', function ($q) use ($siswaFilter) {
                $q->where('nisn', $siswaFilter)
                    ->orWhere('nama', 'like', "%{$siswaFilter}%");
            });
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            $this->info('Tidak ada data presensi hari ini yang perlu direset.');

            return self::SUCCESS;
        }

        // Hapus file selfie terkait
        $deletedFiles = 0;
        foreach ($records as $record) {
            if ($record->path_selfie && Storage::disk('public')->exists($record->path_selfie)) {
                Storage::disk('public')->delete($record->path_selfie);
                $deletedFiles++;
            }
        }

        $deletedCount = $query->delete();

        $this->info("✅ Berhasil reset {$deletedCount} data presensi hari ini ({$tanggal}).");

        if ($deletedFiles > 0) {
            $this->line("<fg=gray>   {$deletedFiles} file selfie dihapus.</>");
        }

        if ($siswaFilter) {
            $this->line("<fg=gray>   Filter: {$siswaFilter}</>");
        }

        return self::SUCCESS;
    }
}
