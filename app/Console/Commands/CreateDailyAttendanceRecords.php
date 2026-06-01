<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\StudentProfile;
use Illuminate\Console\Command;

class CreateDailyAttendanceRecords extends Command
{
    protected $signature = 'attendance:create-daily';

    protected $description = 'Create daily attendance records with belum_absen status for all students';

    public function handle(): int
    {
        if (now()->isWeekend()) {
            $this->info('Hari ini bukan hari aktif absensi. Tidak ada record yang dibuat.');

            return self::SUCCESS;
        }

        $today = now()->toDateString();

        $students = StudentProfile::all();
        $created = 0;

        foreach ($students as $student) {
            $exists = Attendance::where('student_profile_id', $student->id)
                ->where('date', $today)
                ->exists();

            if (! $exists) {
                Attendance::create([
                    'student_profile_id' => $student->id,
                    'date' => $today,
                    'status' => 'belum_absen',
                ]);
                $created++;
            }
        }

        $this->info("Created {$created} attendance records for {$today}.");

        return self::SUCCESS;
    }
}
