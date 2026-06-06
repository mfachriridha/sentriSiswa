<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\StudentViolation;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user()->loadMissing('teacherProfile', 'homeroomClass');
        $summary = [];

        if ($user->isHomeroom() && $user->homeroomClass) {
            $profileIds = $user->homeroomClass->students()->pluck('id');
            $todayAttendances = Attendance::whereIn('student_profile_id', $profileIds)
                ->whereDate('date', today())
                ->get();

            $summary['homeroom'] = [
                'class_name' => $user->homeroomClass->name,
                'students' => $profileIds->count(),
                'hadir' => $todayAttendances->where('status', 'hadir')->count(),
                'terlambat' => $todayAttendances->where('status', 'terlambat')->count(),
                'belum_absen' => $todayAttendances->where('status', 'belum_absen')->count(),
            ];
            $summary['homeroom_chart'] = [
                ['label' => 'Hadir', 'value' => $todayAttendances->where('status', 'hadir')->count(), 'variant' => 'success'],
                ['label' => 'Terlambat', 'value' => $todayAttendances->where('status', 'terlambat')->count(), 'variant' => 'warning'],
                ['label' => 'Izin/Sakit', 'value' => $todayAttendances->whereIn('status', ['izin', 'sakit'])->count(), 'variant' => 'info'],
                ['label' => 'Alpha', 'value' => $todayAttendances->where('status', 'alpha')->count(), 'variant' => 'error'],
                ['label' => 'Belum Absen', 'value' => $todayAttendances->where('status', 'belum_absen')->count(), 'variant' => 'neutral'],
            ];
        }

        if ($user->isCounselor()) {
            $grade = $user->teacherProfile?->grade;
            $studentIds = StudentProfile::whereHas('class', fn ($query) => $query->where('grade', $grade))->pluck('id');
            $violations = StudentViolation::whereIn('student_profile_id', $studentIds)->get();

            $summary['bk'] = [
                'grade' => $grade,
                'students' => $studentIds->count(),
                'pending' => $violations->where('status', 'pending')->count(),
                'approved' => $violations->where('status', 'approved')->count(),
            ];
            $summary['bk_chart'] = [
                ['label' => 'Pending', 'value' => $violations->where('status', 'pending')->count(), 'variant' => 'warning'],
                ['label' => 'Disetujui', 'value' => $violations->where('status', 'approved')->count(), 'variant' => 'success'],
                ['label' => 'Ditolak', 'value' => $violations->where('status', 'rejected')->count(), 'variant' => 'error'],
            ];
        }

        if ($user->isStudentAffairs()) {
            $violations = StudentViolation::all();

            $summary['kesiswaan'] = [
                'classes' => SchoolClass::count(),
                'students' => StudentProfile::count(),
                'pending' => $violations->where('status', 'pending')->count(),
                'approved' => $violations->where('status', 'approved')->count(),
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
