<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\PengajuanPoin;
use App\Models\Pengaturan;
use App\Models\ProfilSiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user()->loadMissing('profilGuru', 'kelasWali');
        $summary = [];
        [$startDate, $endDate] = Pengaturan::rentangTanggalPeriodeAktif();

        if ($user->isWaliKelas() && $user->kelasWali) {
            $profileIds = $user->kelasWali->siswa()
                ->whereHas('pengguna', fn ($query) => $query->where('status', 'registered'))
                ->pluck('nisn');
            $todayAttendances = Absensi::whereIn('profil_siswa_id', $profileIds)
                ->whereDate('tanggal', today())
                ->get();
            $notSubmitted = max(0, $profileIds->count() - $todayAttendances->count())
                + $todayAttendances->where('status', 'belum_absen')->count();

            $homeroomAlphaStudents = ProfilSiswa::with(['pengguna', 'kelas'])
                ->where('kelas_id', $user->kelasWali->id)
                ->whereHas('pengguna', fn ($q) => $q->where('status', 'registered'))
                ->withCount(['absensi as alpha_count' => fn ($q) => $q->where('status', 'alpha')->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])])
                ->having('alpha_count', '>=', 1)
                ->orderByDesc('alpha_count')
                ->get()
                ->map(function ($student) {
                    $student->status_alpha = Pengaturan::statusPeringatanAlpha($student->alpha_count);
                    $student->detail_url = route('wali-kelas.kelas-saya');

                    return $student;
                });

            $summary['homeroom'] = [
                'class_name' => $user->kelasWali->nama,
                'students' => $profileIds->count(),
                'hadir' => $todayAttendances->where('status', 'hadir')->count(),
                'izin_sakit' => $todayAttendances->whereIn('status', ['izin', 'sakit', 'dispensasi'])->count(),
                'alpha' => $todayAttendances->where('status', 'alpha')->count(),
                'belum_absen' => $notSubmitted,
                'alpha_students' => $homeroomAlphaStudents,
            ];
        }

        if ($user->isBk()) {
            $grade = $user->profilGuru?->tingkat;
            $studentIds = ProfilSiswa::whereHas('kelas', fn ($query) => $query->where('tingkat', $grade))
                ->whereHas('pengguna', fn ($query) => $query->where('status', 'registered'))
                ->pluck('nisn');

            $bkAlphaStudents = ProfilSiswa::with(['pengguna', 'kelas'])
                ->whereHas('kelas', fn ($q) => $q->where('tingkat', $grade))
                ->whereHas('pengguna', fn ($q) => $q->where('status', 'registered'))
                ->withCount(['absensi as alpha_count' => fn ($q) => $q->where('status', 'alpha')->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])])
                ->having('alpha_count', '>=', 1)
                ->orderByDesc('alpha_count')
                ->get()
                ->map(function ($student) {
                    $student->status_alpha = Pengaturan::statusPeringatanAlpha($student->alpha_count);
                    $student->detail_url = route('bk.monitoring.show', $student->nisn);

                    return $student;
                });

            $summary['bk'] = [
                'grade' => $grade,
                'students' => $studentIds->count(),
                'classes' => Kelas::where('tingkat', $grade)->count(),
                'pelanggaran' => PelanggaranSiswa::disetujui()
                    ->whereHas('profilSiswa.kelas', fn ($query) => $query->where('tingkat', $grade))
                    ->count(),
                'alpha_students' => $bkAlphaStudents,
            ];
        }

        if ($user->isKesiswaan()) {
            $studentIds = ProfilSiswa::whereHas('pengguna', fn ($query) => $query->where('status', 'registered'))->pluck('nisn');
            $thresholdSp1 = Pengaturan::ambangPeringatanAlpha()['sp1'];

            $alphaWarningCount = ProfilSiswa::whereHas('pengguna', fn ($q) => $q->where('status', 'registered'))
                ->whereHas('absensi', function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'alpha')
                        ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
                }, '>=', $thresholdSp1)
                ->count();

            $kesiswaanAlphaStudents = ProfilSiswa::with(['pengguna', 'kelas'])
                ->whereHas('pengguna', fn ($q) => $q->where('status', 'registered'))
                ->withCount(['absensi as alpha_count' => fn ($q) => $q->where('status', 'alpha')->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])])
                ->having('alpha_count', '>=', 1)
                ->orderByDesc('alpha_count')
                ->get()
                ->map(function ($student) {
                    $student->status_alpha = Pengaturan::statusPeringatanAlpha($student->alpha_count);
                    $student->detail_url = route('kesiswaan.monitoring.show', $student->nisn);

                    return $student;
                });

            $summary['kesiswaan'] = [
                'classes' => Kelas::count(),
                'students' => $studentIds->count(),
                'approved' => PelanggaranSiswa::disetujui()->count(),
                'pengajuan_poin_pending' => PengajuanPoin::where('status', 'pending')->count(),
                'alpha_warning' => $alphaWarningCount,
                'alpha_students' => $kesiswaanAlphaStudents,
            ];
        }

        return view('guru.dashboard', compact('summary'));
    }
}
