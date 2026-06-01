<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppNotification;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\WhatsappMessage;
use App\Services\FonnteService;
use Illuminate\Console\Command;

class SendAttendanceReport extends Command
{
    protected $signature = 'app:send-attendance-report
        {--force : Kirim laporan tanpa mengecek waktu}';

    protected $description = 'Kirim laporan absensi harian ke wali kelas via WhatsApp';

    public function handle(FonnteService $fonnte): int
    {
        if (now()->isWeekend()) {
            $this->info('Hari ini bukan hari aktif absensi. Laporan tidak dikirim.');

            return self::SUCCESS;
        }

        if (! $fonnte->isConfigured()) {
            $this->warn('Fonnte token belum dikonfigurasi. Lewati pengiriman laporan.');

            return self::SUCCESS;
        }

        $endTime = Setting::get('attendance_end_time', '07:00');

        if (! $this->option('force')) {
            $graceMinutes = 5;
            $sendAfter = now()->setTimeFromTimeString($endTime)->addMinutes($graceMinutes);

            if (now()->lessThan($sendAfter)) {
                $this->info('Belum waktunya kirim laporan (sesudah '.$endTime.' + '.$graceMinutes.' menit).');

                return self::SUCCESS;
            }
        }

        $today = now()->toDateString();
        $classes = SchoolClass::with(['homeroomTeacher.teacherProfile', 'students.user', 'students.biodata'])
            ->whereNotNull('homeroom_teacher_id')
            ->get();

        if ($classes->isEmpty()) {
            $this->info('Tidak ada kelas dengan wali kelas.');

            return self::SUCCESS;
        }

        $sentCount = 0;

        foreach ($classes as $class) {
            $teacher = $class->homeroomTeacher;
            $phone = $teacher?->teacherProfile?->phone;

            if (blank($phone)) {
                $this->warn("Wali kelas {$class->name} tidak punya nomor HP. Lewati.");

                continue;
            }

            $studentProfiles = $class->students;
            $totalStudents = $studentProfiles->count();

            if ($totalStudents === 0) {
                continue;
            }

            $profileIds = $studentProfiles->pluck('id');
            $attendances = Attendance::whereIn('student_profile_id', $profileIds)
                ->whereDate('date', $today)
                ->get()
                ->keyBy('student_profile_id');

            $presentStudents = $studentProfiles->filter(fn ($sp) => $attendances->has($sp->id)
                && in_array($attendances[$sp->id]->status, ['hadir', 'terlambat']));

            $absentStudents = $studentProfiles->reject(fn ($sp) => $attendances->has($sp->id)
                && in_array($attendances[$sp->id]->status, ['hadir', 'terlambat']));

            $presentCount = $presentStudents->count();
            $absentCount = $absentStudents->count();

            $message = "Laporan Absensi Harian\n";
            $message .= "Kelas: {$class->name}\n";
            $message .= 'Tanggal: '.now()->locale('id')->translatedFormat('l, d F Y')."\n";
            $message .= "Waktu: {$endTime}\n";
            $message .= "\n";
            $message .= "Hadir: {$presentCount} dari {$totalStudents}\n";
            $message .= "Tidak Absen: {$absentCount}\n";
            $message .= "\n";

            if ($absentCount > 0) {
                $message .= "Siswa Tidak Absen:\n";
                foreach ($absentStudents as $sp) {
                    $studentName = $sp->user?->name ?? 'Siswa #'.$sp->id;
                    $message .= "- {$studentName}\n";
                }
            } else {
                $message .= "Absen semua.\n";
            }

            $whatsappMessage = WhatsappMessage::create([
                'school_class_id' => $class->id,
                'recipient_phone' => $phone,
                'recipient_name' => $teacher->name,
                'message_type' => 'attendance_report',
                'message' => $message,
                'status' => 'pending',
            ]);

            SendWhatsAppNotification::dispatch($whatsappMessage)
                ->delay(now()->addSeconds($sentCount * 8));

            $sentCount++;
        }

        $this->info("Laporan absensi dikirim ke {$sentCount} wali kelas.");

        return self::SUCCESS;
    }
}
