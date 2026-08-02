<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\TokenAksesAbsensi;

class AttendanceReportMessageBuilder
{
    /**
     * Susun isi laporan absensi harian sebuah kelas dari kondisi absensi TERKINI.
     * Dipakai baik saat laporan pertama kali dibuat maupun saat dikirim ulang -
     * supaya "kirim ulang" tidak sekadar mengirim ulang teks basi.
     *
     * Return null kalau kelasnya tidak punya siswa sama sekali.
     *
     * @return array{telepon_penerima: ?string, nama_penerima: string, isi_pesan: string}|null
     */
    public function build(Kelas $kelas, string $tanggal): ?array
    {
        $kelas->loadMissing(['waliKelas.profilGuru', 'siswa.pengguna']);

        $waliKelas = $kelas->waliKelas;
        $namaWaliKelas = $waliKelas?->nama ?? '-';
        $studentProfiles = $kelas->siswa;
        $totalStudents = $studentProfiles->count();

        if ($totalStudents === 0) {
            return null;
        }

        $profileIds = $studentProfiles->pluck('nisn');
        $attendances = Absensi::whereIn('profil_siswa_id', $profileIds)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('profil_siswa_id');

        $sudahAbsen = $studentProfiles->filter(fn ($sp) => $attendances->has($sp->nisn)
            && in_array($attendances[$sp->nisn]->status, ['hadir', 'izin', 'sakit', 'dispensasi']));

        $belumAbsen = $studentProfiles->reject(fn ($sp) => $attendances->has($sp->nisn)
            && in_array($attendances[$sp->nisn]->status, ['hadir', 'izin', 'sakit', 'dispensasi']));

        $sudahCount = $sudahAbsen->count();
        $belumCount = $belumAbsen->count();

        // Token dipakai ulang kalau sudah ada buat kelas+tanggal ini, bukan
        // digenerate baru - biar link yang sudah dibagikan gak keburu invalid.
        $aksesToken = TokenAksesAbsensi::where('kelas_id', $kelas->id)
            ->where('tanggal', $tanggal)
            ->first() ?? TokenAksesAbsensi::buatAtauPerbarui($kelas->id, $tanggal);
        $linkAbsensi = route('absensi.publik', $aksesToken->token);

        $hari = now()->locale('id')->translatedFormat('l');
        $tanggalLabel = now()->locale('id')->translatedFormat('d F Y');
        $waktu = now()->format('H:i').' WIB';

        $message = "📋 *LAPORAN PRESENSI HARIAN*\n";
        $message .= "🏫 Kelas *{$kelas->nama}*\n";
        $message .= "👤 Wali Kelas: {$namaWaliKelas}\n";
        $message .= "📅 {$hari}, {$tanggalLabel} · {$waktu}\n";
        $message .= "\n";
        $message .= "━━━━━━━━━━━━━━━━\n";
        $message .= "✅ Sudah presensi : *{$sudahCount}* siswa\n";
        $message .= "❌ Belum presensi : *{$belumCount}* siswa\n";
        $message .= "📊 Total          : *{$totalStudents}* siswa\n";
        $message .= "━━━━━━━━━━━━━━━━\n";

        if ($belumCount === 0) {
            $message .= "\n🎉 Seluruh siswa telah presensi hari ini!\n";
        } else {
            $message .= "\n📌 *Belum presensi:*\n";
            foreach ($belumAbsen as $sp) {
                $message .= '• '.($sp->pengguna?->nama ?? 'Siswa NISN '.$sp->nisn)."\n";
            }
        }

        // Isinya hasil presensi, bukan formulir input.
        $message .= "\n🔗 Cek hasil presensi:\n{$linkAbsensi}\n";

        return [
            'telepon_penerima' => $waliKelas?->profilGuru?->telepon,
            'nama_penerima' => $namaWaliKelas,
            'isi_pesan' => $message,
        ];
    }
}
