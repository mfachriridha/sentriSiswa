<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $student = Auth::user()->loadMissing('profilSiswa.absensi', 'profilSiswa.pelanggaranSiswa', 'profilSiswa.kelas');
        $profile = $student->profilSiswa;
        $points = $profile?->poin ?? 100;

        [$startDate, $endDate] = Pengaturan::rentangTanggalPeriodeAktif();

        $allAttendances = $profile?->absensi ?? collect();
        $periodAttendances = $allAttendances->filter(function ($absensi) use ($startDate, $endDate) {
            $tanggal = is_string($absensi->tanggal) ? $absensi->tanggal : $absensi->tanggal?->toDateString();

            return $tanggal >= $startDate->toDateString() && $tanggal <= $endDate->toDateString();
        });

        $maxAlpha = Pengaturan::batasMaksimalAlpha();
        $alphaCount = $periodAttendances->where('status', 'alpha')->count();
        $statusAlpha = Pengaturan::statusPeringatanAlpha($alphaCount);

        $stats = [
            'points' => $points,
            'hadir' => $periodAttendances->where('status', 'hadir')->count(),
            'izin_sakit' => $periodAttendances->whereIn('status', ['izin', 'sakit', 'dispensasi'])->count(),
            'alpha' => $alphaCount,
            'max_alpha' => $maxAlpha,
            'sisa_alpha' => max(0, $maxAlpha - $alphaCount),
            'status_alpha' => $statusAlpha,
            'tahun_ajaran' => Pengaturan::tahunAjaran(),
            'mode_periode' => Pengaturan::modePeriode(),
            'semester' => Pengaturan::semester(),
        ];

        $identitas = [
            'kelas' => $profile?->kelas?->nama,
            'nisn' => $profile?->nisn,
            'nis' => $profile?->nis,
        ];

        return view('siswa.dashboard', compact('stats', 'identitas'));
    }
}
