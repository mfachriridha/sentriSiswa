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
        }

        if ($user->isCounselor()) {
            $grade = $user->teacherProfile?->grade;
            $studentIds = StudentProfile::whereHas('class', fn ($query) => $query->where('grade', $grade))->pluck('id');

            $summary['bk'] = [
                'grade' => $grade,
                'students' => $studentIds->count(),
                'pending' => StudentViolation::whereIn('student_profile_id', $studentIds)->where('status', 'pending')->count(),
                'approved' => StudentViolation::whereIn('student_profile_id', $studentIds)->approved()->count(),
            ];
        }

        if ($user->isStudentAffairs()) {
            $summary['kesiswaan'] = [
                'classes' => SchoolClass::count(),
                'students' => StudentProfile::count(),
                'pending' => StudentViolation::where('status', 'pending')->count(),
                'approved' => StudentViolation::approved()->count(),
            ];
        }

        return view('guru.dashboard', compact('summary'));
    }
}
