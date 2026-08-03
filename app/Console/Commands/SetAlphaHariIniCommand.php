<?php

namespace App\Console\Commands;

use App\Models\Pengaturan;
use App\Models\Presensi;
use App\Models\ProfilSiswa;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SetAlphaHariIniCommand extends Command
{
    /**
     * Nama dan tanda tangan perintah console.
     *
     * @var string
     */
    protected $signature = 'absensi:set-alpha
                            {--tanggal= : Tanggal presensi (YYYY-MM-DD), default: hari ini}
                            {--siswa= : NISN atau nama siswa tertentu (opsional)}
                            {--force : Abaikan pengecekan hari aktif absensi}';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Set status presensi semua siswa (atau siswa tertentu) menjadi Alpha untuk keperluan testing';

    /**
     * Eksekusi perintah console.
     */
    public function handle(): int
    {
        $tanggal = $this->option('tanggal') ?: now()->toDateString();

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $this->error('Format tanggal tidak valid. Gunakan format YYYY-MM-DD.');

            return self::FAILURE;
        }

        $carbonDate = Carbon::parse($tanggal);

        if (! $this->option('force') && ! Pengaturan::hariAbsenAktif($carbonDate)) {
            $this->info("Tanggal {$tanggal} bukan hari aktif absensi. Gunakan --force untuk memaksa.");

            return self::SUCCESS;
        }

        $siswaQuery = ProfilSiswa::query();

        $siswaFilter = $this->option('siswa');
        if ($siswaFilter) {
            $siswaQuery->where(function ($q) use ($siswaFilter) {
                $q->where('nisn', $siswaFilter)
                    ->orWhere('nis', $siswaFilter)
                    ->orWhereHas('pengguna', function ($pq) use ($siswaFilter) {
                        $pq->where('nama', 'like', "%{$siswaFilter}%");
                    });
            });
        }

        $students = $siswaQuery->get();

        if ($students->isEmpty()) {
            $this->warn('Tidak ada siswa yang ditemukan.');

            return self::SUCCESS;
        }

        $count = 0;
        foreach ($students as $student) {
            $attendance = Presensi::query()
                ->where('profil_siswa_id', $student->nisn)
                ->whereDate('tanggal', $tanggal)
                ->first();

            if (! $attendance) {
                $attendance = new Presensi([
                    'profil_siswa_id' => $student->nisn,
                    'tanggal' => $tanggal,
                ]);
            }

            $attendance->status = 'alpha';
            $attendance->save();
            $count++;
        }

        $tanggalFormatted = $carbonDate->locale('id')->translatedFormat('d F Y');
        $this->info("✅ Berhasil mengubah status presensi {$count} siswa menjadi Alpha untuk tanggal {$tanggalFormatted} ({$tanggal}).");

        if ($siswaFilter) {
            $this->line("<fg=gray>   Filter siswa: {$siswaFilter}</>");
        }

        return self::SUCCESS;
    }
}
