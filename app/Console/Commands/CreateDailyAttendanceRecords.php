<?php

namespace App\Console\Commands;

use App\Models\Pengaturan;
use App\Models\Presensi;
use App\Models\ProfilSiswa;
use Illuminate\Console\Command;

class CreateDailyAttendanceRecords extends Command
{
    protected $signature = 'attendance:create-daily';

    protected $description = 'Create daily attendance records with belum_absen status for all students';

    public function handle(): int
    {
        if (! Pengaturan::hariAbsenAktif()) {
            $this->info('Hari ini bukan hari aktif absensi. Tidak ada record yang dibuat.');

            return self::SUCCESS;
        }

        $today = now()->toDateString();

        $students = ProfilSiswa::all();
        $created = 0;

        foreach ($students as $student) {
            $exists = Presensi::where('profil_siswa_id', $student->nisn)
                ->where('tanggal', $today)
                ->exists();

            if (! $exists) {
                Presensi::create([
                    'profil_siswa_id' => $student->nisn,
                    'tanggal' => $today,
                    'status' => 'belum_absen',
                ]);
                $created++;
            }
        }

        $this->info("Created {$created} attendance records for {$today}.");

        return self::SUCCESS;
    }
}
