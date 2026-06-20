<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\StudentViolation;
use App\Services\AbsenceWarningService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(AbsenceWarningService $absenceWarning): View
    {
        $user = Auth::user()->loadMissing('teacherProfile', 'homeroomClass');
        $summary = [];

        if ($user->isWaliKelas() && $user->homeroomClass) {
            $profileIds = $user->homeroomClass->students()->pluck('id');
            $todayAttendances = Attendance::whereIn('student_profile_id', $profileIds)
                ->whereDate('date', today())
                ->get();
            $notSubmitted = max(0, $profileIds->count() - $todayAttendances->count())
                + $todayAttendances->where('status', 'belum_absen')->count();
            $warningCount = $absenceWarning->alphaCountsForStudentIds($profileIds)
                ->filter(fn (int $count): bool => $absenceWarning->hasWarning($count))
                ->count();

            $summary['homeroom'] = [
                'class_name' => $user->homeroomClass->name,
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
            $grade = $user->teacherProfile?->grade;
            $studentIds = StudentProfile::whereHas('class', fn ($query) => $query->where('grade', $grade))->pluck('id');
            $violations = StudentViolation::whereIn('student_profile_id', $studentIds)->get();
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
            $violations = StudentViolation::all();
            $studentIds = StudentProfile::query()->pluck('id');
            $warningCount = $absenceWarning->alphaCountsForStudentIds($studentIds)
                ->filter(fn (int $count): bool => $absenceWarning->hasWarning($count))
                ->count();

            $summary['kesiswaan'] = [
                'classes' => SchoolClass::count(),
                'students' => StudentProfile::count(),
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
