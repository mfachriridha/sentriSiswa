<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppNotification;
use App\Models\Kelas;
use App\Models\Pengaturan;
use App\Models\PesanWhatsapp;
use App\Services\AttendanceReportMessageBuilder;
use App\Services\FonnteService;
use Illuminate\Console\Command;

class SendAttendanceReport extends Command
{
    protected $signature = 'app:send-attendance-report
        {--force : Kirim laporan tanpa mengecek waktu}';

    protected $description = 'Kirim laporan absensi harian ke wali kelas via WhatsApp';

    public function handle(FonnteService $whatsapp, AttendanceReportMessageBuilder $messageBuilder): int
    {
        if (! Pengaturan::hariAbsenAktif()) {
            $this->info('Hari ini bukan hari aktif absensi. Laporan tidak dikirim.');

            return self::SUCCESS;
        }

        if (! $whatsapp->isConfigured()) {
            $this->warn('Token Fonnte belum dikonfigurasi. Lewati pengiriman laporan.');

            return self::SUCCESS;
        }

        $endTime = Pengaturan::get('attendance_end_time', '07:00');

        if (! $this->option('force')) {
            $graceMinutes = 5;
            $sendAfter = now()->setTimeFromTimeString($endTime)->addMinutes($graceMinutes);
            $sendBefore = now()->setTimeFromTimeString($endTime)->addMinutes(30);

            if (now()->lessThan($sendAfter)) {
                $this->info('Belum waktunya kirim laporan (sesudah '.$endTime.' + '.$graceMinutes.' menit).');

                return self::SUCCESS;
            }

            if (now()->greaterThan($sendBefore)) {
                $this->info('Lewat batas waktu pengiriman (30 menit setelah '.$endTime.').');

                return self::SUCCESS;
            }
        }

        $today = now()->toDateString();
        $classes = Kelas::with(['waliKelas.profilGuru', 'siswa.pengguna'])
            ->whereNotNull('wali_kelas_id')
            ->get();

        if ($classes->isEmpty()) {
            $this->info('Tidak ada kelas dengan wali kelas.');

            return self::SUCCESS;
        }

        $sentCount = 0;
        $delaySeconds = 0;

        foreach ($classes as $kelas) {
            $alreadySent = PesanWhatsapp::where('kelas_id', $kelas->id)
                ->where('tipe_pesan', 'attendance_report')
                ->whereDate('dibuat_pada', $today)
                ->whereIn('status', ['pending', 'processing', 'sent'])
                ->exists();

            if ($alreadySent) {
                continue;
            }

            $built = $messageBuilder->build($kelas, $today);

            if ($built === null) {
                continue;
            }

            if (blank($built['telepon_penerima'])) {
                $this->warn("Wali kelas {$kelas->nama} tidak punya nomor HP. Link absensi tetap dibuat, laporan WA dilewati.");

                continue;
            }

            $pesanWa = PesanWhatsapp::create([
                'kelas_id' => $kelas->id,
                'telepon_penerima' => $built['telepon_penerima'],
                'nama_penerima' => $built['nama_penerima'],
                'tipe_pesan' => 'attendance_report',
                'isi_pesan' => $built['isi_pesan'],
                'status' => 'pending',
            ]);

            SendWhatsAppNotification::dispatch($pesanWa)
                ->delay(now()->addSeconds($delaySeconds));

            $sentCount++;
            $delaySeconds += $whatsapp->messageDelaySeconds();
        }

        $this->info("Laporan absensi dikirim ke {$sentCount} wali kelas.");

        return self::SUCCESS;
    }
}
