<?php

namespace App\Console\Commands;

use App\Models\Pengaturan;
use App\Models\Presensi;
use Illuminate\Console\Command;

class UpdateUnmarkedAttendance extends Command
{
    protected $signature = 'attendance:update-unmarked';

    protected $description = 'Update belum_absen records to alpha after attendance time ends';

    public function handle(): int
    {
        if (! Pengaturan::hariAbsenAktif()) {
            $this->info('Hari ini bukan hari aktif absensi. Tidak ada status yang diperbarui.');

            return self::SUCCESS;
        }

        $endTime = Pengaturan::get('attendance_end_time', '07:00');
        $graceMinutes = 5;
        $updateAfter = now()->setTimeFromTimeString($endTime)->addMinutes($graceMinutes);

        if (now()->lessThan($updateAfter)) {
            $this->info('Belum waktunya update status (setelah '.$endTime.' + '.$graceMinutes.' menit).');

            return self::SUCCESS;
        }

        $today = now()->toDateString();

        $updated = Presensi::whereDate('tanggal', $today)
            ->where('status', 'belum_absen')
            ->update(['status' => 'alpha']);

        $this->info("Updated {$updated} records from belum_absen to alpha for {$today}.");

        return self::SUCCESS;
    }
}
