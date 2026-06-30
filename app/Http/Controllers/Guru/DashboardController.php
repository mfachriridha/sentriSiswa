<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
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
            $profileIds = $user->kelasWali->siswa()->pluck('id');
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
                'belum_absen' => $notSubmitted,
                'warnings' => $warningCount,
            ];
            $summary['homeroom_chart'] = [
                ['label' => 'Hadir', 'value' => $todayAttendances->where('status', 'hadir')->count(), 'variant' => 'success'],
                ['label' => 'Terlambat', 'value' => $todayAttendances->where('status', 'terlambat')->count(), 'variant' => 'warning'],
                ['label' => 'Izin/Sakit', 'value' => $todayAttendances->whereIn('status', ['izin', 'sakit'])->count(), 'variant' => 'info'],
                ['label' => 'Alpha', 'value' => $todayAttendances->where('status', 'alpha')->count(), 'variant' => 'error'],
                ['label' => 'Belum Absen', 'value' => $notSubmitted, 'variant' => 'neutral'],
            ];
        }

        if ($user->isBk()) {
            $grade = $user->profilGuru?->tingkat;
            $studentIds = ProfilSiswa::whereHas('kelas', fn ($query) => $query->where('tingkat', $grade))->pluck('id');
            $violations = PelanggaranSiswa::whereIn('profil_siswa_id', $studentIds)->get();
            $warningCount = $absenceWarning->alphaCountsForStudentIds($studentIds)
                ->filter(fn (int $count): bool => $absenceWarning->hasWarning($count))
                ->count();

            $summary['bk'] = [
                'grade' => $grade,
                'students' => $studentIds->count(),
                'pending' => $violations->where('status', 'pending')->count(),
                'approved' => $violations->where('status', 'approved')->count(),
                'warnings' => $warningCount,
            ];
            $summary['bk_chart'] = [
                ['label' => 'Pending', 'value' => $violations->where('status', 'pending')->count(), 'variant' => 'warning'],
                ['label' => 'Disetujui', 'value' => $violations->where('status', 'approved')->count(), 'variant' => 'success'],
                ['label' => 'Ditolak', 'value' => $violations->where('status', 'rejected')->count(), 'variant' => 'error'],
            ];
        }

        if ($user->isKesiswaan()) {
            $violations = PelanggaranSiswa::all();
            $studentIds = ProfilSiswa::query()->pluck('id');
            $warningCount = $absenceWarning->alphaCountsForStudentIds($studentIds)
                ->filter(fn (int $count): bool => $absenceWarning->hasWarning($count))
                ->count();

            $summary['kesiswaan'] = [
                'classes' => Kelas::count(),
                'students' => ProfilSiswa::count(),
                'pending' => $violations->where('status', 'pending')->count(),
                'approved' => $violations->where('status', 'approved')->count(),
                'warnings' => $warningCount,
            ];
            $summary['kesiswaan_chart'] = [
                ['label' => 'Pending', 'value' => $violations->where('status', 'pending')->count(), 'variant' => 'warning'],
                ['label' => 'Disetujui', 'value' => $violations->where('status', 'approved')->count(), 'variant' => 'success'],
                ['label' => 'Ditolak', 'value' => $violations->where('status', 'rejected')->count(), 'variant' => 'error'],
            ];
        }

        return view('guru.dashboard', compact('summary'));
    }
}
