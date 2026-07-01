<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppNotification;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Pengaturan;
use App\Models\PesanWhatsapp;
use App\Models\TokenAksesAbsensi;
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

        $endTime = Pengaturan::get('attendance_end_time', '07:00');

        if (! $this->option('force')) {
            $graceMinutes = 5;
            $sendAfter = now()->setTimeFromTimeString($endTime)->addMinutes($graceMinutes);

            if (now()->lessThan($sendAfter)) {
                $this->info('Belum waktunya kirim laporan (sesudah '.$endTime.' + '.$graceMinutes.' menit).');

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
            $waliKelas = $kelas->waliKelas;
            $phone = $waliKelas?->profilGuru?->telepon;

            if (blank($phone)) {
                $this->warn("Wali kelas {$kelas->nama} tidak punya nomor HP. Lewati.");

                continue;
            }

            $alreadySent = PesanWhatsapp::where('kelas_id', $kelas->id)
                ->where('tipe_pesan', 'attendance_report')
                ->whereDate('created_at', $today)
                ->whereIn('status', ['pending', 'processing', 'sent'])
                ->exists();

            if ($alreadySent) {
                continue;
            }

            $studentProfiles = $kelas->siswa;
            $totalStudents = $studentProfiles->count();

            if ($totalStudents === 0) {
                continue;
            }

            $profileIds = $studentProfiles->pluck('id');
            $attendances = Absensi::whereIn('profil_siswa_id', $profileIds)
                ->whereDate('tanggal', $today)
                ->get()
                ->keyBy('profil_siswa_id');

            $sudahAbsen = $studentProfiles->filter(fn ($sp) => $attendances->has($sp->id)
                && in_array($attendances[$sp->id]->status, ['hadir', 'terlambat', 'izin', 'sakit']));

            $belumAbsen = $studentProfiles->reject(fn ($sp) => $attendances->has($sp->id)
                && in_array($attendances[$sp->id]->status, ['hadir', 'terlambat', 'izin', 'sakit']));

            $sudahCount = $sudahAbsen->count();
            $belumCount = $belumAbsen->count();

            $aksesToken = TokenAksesAbsensi::buatAtauPerbarui($kelas->id, $today);
            $linkAbsensi = route('absensi.publik', $aksesToken->token);

            $hari = now()->locale('id')->translatedFormat('l');
            $tanggal = now()->locale('id')->translatedFormat('d F Y');
            $waktu = now()->format('H:i').' WIB';

            $message = "{$hari}, {$tanggal}\n";
            $message .= "{$waktu}\n";
            $message .= "=================\n";
            $message .= "Wali Kelas: {$waliKelas->nama}\n";
            $message .= "Kelas: {$kelas->nama}\n";
            $message .= "\n";
            $message .= "Yang sudah absen : {$sudahCount}\n";
            $message .= "Yang belum absen : {$belumCount}\n";
            $message .= "\n";
            $message .= "Ket:\n";

            if ($belumCount === 0) {
                $message .= "Seluruh siswa telah melakukan absensi hari ini.\n";
            } else {
                foreach ($belumAbsen as $sp) {
                    $message .= '- '.($sp->pengguna?->nama ?? 'Siswa #'.$sp->id)."\n";
                }
            }

            $message .= "\n==================\n";
            $message .= "Cek absensi siswa:\n{$linkAbsensi}";

            $pesanWa = PesanWhatsapp::create([
                'kelas_id' => $kelas->id,
                'telepon_penerima' => $phone,
                'nama_penerima' => $waliKelas->nama,
                'tipe_pesan' => 'attendance_report',
                'isi_pesan' => $message,
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
