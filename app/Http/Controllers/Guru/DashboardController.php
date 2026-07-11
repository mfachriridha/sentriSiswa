<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\PengajuanPoin;
use App\Models\ProfilSiswa;
use App\Services\AbsenceWarningService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(AbsenceWarningService $absenceWarning): View
    {
        $user = Auth::user()->loadMissing('profilGuru', 'kelasWali');
        $summary = [];

        if ($user->isWaliKelas() && $user->kelasWali) {
            $profileIds = $user->kelasWali->siswa()
                ->whereHas('pengguna', fn ($query) => $query->where('status', 'registered'))
                ->pluck('nisn');
            $todayAttendances = Absensi::whereIn('profil_siswa_id', $profileIds)
                ->whereDate('tanggal', today())
                ->get();
            $notSubmitted = max(0, $profileIds->count() - $todayAttendances->count())
                + $todayAttendances->where('status', 'belum_absen')->count();
            $warningCount = $absenceWarning->alphaCountsForStudentIds($profileIds)
                ->filter(fn (int $count): bool => $absenceWarning->hasWarning($count))
                ->count();

            $summary['homeroom'] = [
                'class_name' => $user->kelasWali->nama,
                'students' => $profileIds->count(),
                'hadir' => $todayAttendances->where('status', 'hadir')->count(),
                'terlambat' => $todayAttendances->where('status', 'terlambat')->count(),
                'izin_sakit' => $todayAttendances->whereIn('status', ['izin', 'sakit'])->count(),
                'alpha' => $todayAttendances->where('status', 'alpha')->count(),
                'belum_absen' => $notSubmitted,
                'warnings' => $warningCount,
            ];
        }

        if ($user->isBk()) {
            $grade = $user->profilGuru?->tingkat;
            $studentIds = ProfilSiswa::whereHas('kelas', fn ($query) => $query->where('tingkat', $grade))
                ->whereHas('pengguna', fn ($query) => $query->where('status', 'registered'))
                ->pluck('nisn');
            $warningCount = $absenceWarning->alphaCountsForStudentIds($studentIds)
                ->filter(fn (int $count): bool => $absenceWarning->hasWarning($count))
                ->count();

            $summary['bk'] = [
                'grade' => $grade,
                'students' => $studentIds->count(),
                'classes' => Kelas::where('tingkat', $grade)->count(),
                'pelanggaran' => PelanggaranSiswa::disetujui()
                    ->whereHas('profilSiswa.kelas', fn ($query) => $query->where('tingkat', $grade))
                    ->count(),
                'warnings' => $warningCount,
            ];
        }

        if ($user->isKesiswaan()) {
            $studentIds = ProfilSiswa::whereHas('pengguna', fn ($query) => $query->where('status', 'registered'))->pluck('nisn');
            $warningCount = $absenceWarning->alphaCountsForStudentIds($studentIds)
                ->filter(fn (int $count): bool => $absenceWarning->hasWarning($count))
                ->count();

            $summary['kesiswaan'] = [
                'classes' => Kelas::count(),
                'students' => $studentIds->count(),
                'approved' => PelanggaranSiswa::disetujui()->count(),
                'pengajuan_poin_pending' => PengajuanPoin::where('status', 'pending')->count(),
                'warnings' => $warningCount,
            ];
        }

        return view('guru.dashboard', compact('summary'));
    }
}
