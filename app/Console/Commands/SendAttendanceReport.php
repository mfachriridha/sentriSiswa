<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppNotification;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\TokenAksesAbsensi;
use App\Models\WhatsappMessage;
use App\Services\FonnteService;
use Illuminate\Console\Command;

class SendAttendanceReport extends Command
{
    protected $signature = 'app:send-attendance-report
        {--force : Kirim laporan tanpa mengecek waktu}';

    protected $description = 'Kirim laporan absensi harian ke wali kelas via WhatsApp';

    public function handle(FonnteService $whatsapp): int
    {
        if (now()->isWeekend()) {
            $this->info('Hari ini bukan hari aktif absensi. Laporan tidak dikirim.');

            return self::SUCCESS;
        }

        if (! $whatsapp->isConfigured()) {
            $this->warn('Token Fonnte belum dikonfigurasi. Lewati pengiriman laporan.');

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
        $classes = SchoolClass::with(['homeroomTeacher.teacherProfile', 'students.user'])
            ->whereNotNull('homeroom_teacher_id')
            ->get();

        if ($classes->isEmpty()) {
            $this->info('Tidak ada kelas dengan wali kelas.');

            return self::SUCCESS;
        }

        $sentCount = 0;
        $delaySeconds = 0;

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

            $sudahAbsen = $studentProfiles->filter(fn ($sp) => $attendances->has($sp->id)
                && in_array($attendances[$sp->id]->status, ['hadir', 'terlambat', 'izin', 'sakit']));

            $belumAbsen = $studentProfiles->reject(fn ($sp) => $attendances->has($sp->id)
                && in_array($attendances[$sp->id]->status, ['hadir', 'terlambat', 'izin', 'sakit']));

            $sudahCount = $sudahAbsen->count();
            $belumCount = $belumAbsen->count();

            // Generate atau perbarui token akses publik untuk kelas ini hari ini
            $aksesToken = TokenAksesAbsensi::buatAtauPerbarui($class->id, $today);
            $linkAbsensi = route('absensi.publik', $aksesToken->token);

            $hari = now()->locale('id')->translatedFormat('l');
            $tanggal = now()->locale('id')->translatedFormat('d F Y');
            $waktu = now()->format('H:i') . ' WIB';

            $message = "{$hari}, {$tanggal}\n";
            $message .= "{$waktu}\n";
            $message .= "=================\n";
            $message .= "Wali Kelas: {$teacher->name}\n";
            $message .= "Kelas: {$class->name}\n";
            $message .= "\n";
            $message .= "Yang sudah absen : {$sudahCount}\n";
            $message .= "Yang belum absen : {$belumCount}\n";
            $message .= "\n";
            $message .= "Ket:\n";

            if ($belumCount === 0) {
                $message .= "Seluruh siswa telah melakukan absensi hari ini.\n";
            } else {
                foreach ($belumAbsen as $sp) {
                    $message .= '- '.($sp->user?->name ?? 'Siswa #'.$sp->id)."\n";
                }
            }

            $message .= "\n==================\n";
            $message .= "Cek absensi siswa:\n{$linkAbsensi}";

            $whatsappMessage = WhatsappMessage::create([
                'school_class_id' => $class->id,
                'recipient_phone' => $phone,
                'recipient_name' => $teacher->name,
                'message_type' => 'attendance_report',
                'message' => $message,
                'status' => 'pending',
            ]);

            SendWhatsAppNotification::dispatch($whatsappMessage)
                ->delay(now()->addSeconds($delaySeconds));

            $sentCount++;
            $delaySeconds += $whatsapp->messageDelaySeconds();
        }

        $this->info("Laporan absensi dikirim ke {$sentCount} wali kelas.");

        return self::SUCCESS;
    }
}
