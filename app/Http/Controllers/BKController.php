<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentViolation;
use App\Models\TeacherClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BKController extends Controller
{
    public function dashboard()
    {
        $classes = TeacherClass::where('user_id', Auth::id())
            ->where('role', 'bk')
            ->with('schoolClass')
            ->get();

        $today = now()->toDateString();

        $classStats = $classes->map(function ($tc) use ($today) {
            $studentIds = Student::where('school_class_id', $tc->school_class_id)->pluck('id');
            $hadir = Attendance::whereIn('student_id', $studentIds)->where('date', $today)->where('status', 'hadir')->count();
            $total = $studentIds->count();

            return [
                'class_id' => $tc->school_class_id,
                'name' => $tc->schoolClass->name,
                'hadir' => $hadir,
                'total' => $total,
            ];
        });

        return view('bk.dashboard', compact('classStats'));
    }

    public function laporan(Request $request)
    {
        $bkClasses = TeacherClass::where('user_id', Auth::id())
            ->where('role', 'bk')
            ->with('schoolClass')
            ->get();

        $classId = $request->input('class_id');
        $selectedClass = null;
        $rows = collect();

        if ($classId) {
            $selectedClass = $bkClasses->firstWhere('school_class_id', $classId);

            if ($selectedClass) {
                $students = Student::where('school_class_id', $classId)->orderBy('name')->get();

                $start = $request->input('start') ? now()->parse($request->input('start'))->startOfDay() : now()->startOfWeek();
                $end = $request->input('end') ? now()->parse($request->input('end'))->endOfDay() : now()->endOfDay();

                $studentIds = $students->pluck('id');

                $attendances = Attendance::whereIn('student_id', $studentIds)
                    ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                    ->get()
                    ->groupBy('student_id');

                $violations = StudentViolation::whereIn('student_id', $studentIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->with('violation')
                    ->get()
                    ->groupBy('student_id');

                $rows = $students->map(function ($student) use ($attendances, $violations) {
                    $attGroup = $attendances->get($student->id, collect());
                    $vioGroup = $violations->get($student->id, collect());
                    $poinDipotong = $vioGroup->sum(fn ($v) => $v->violation->poin);

                    return [
                        'nis' => $student->nis,
                        'name' => $student->name,
                        'hadir' => $attGroup->where('status', 'hadir')->count(),
                        'izin' => $attGroup->where('status', 'izin')->count(),
                        'sakit' => $attGroup->where('status', 'sakit')->count(),
                        'alfa' => $attGroup->where('status', 'alfa')->count(),
                        'total' => $attGroup->count(),
                        'poin' => $student->poin,
                        'poin_dipotong' => $poinDipotong,
                    ];
                });
            }

            return view('bk.laporan', compact('bkClasses', 'rows', 'start', 'end', 'classId', 'selectedClass'));
        }

        $start = now()->startOfWeek();
        $end = now()->endOfDay();

        return view('bk.laporan', compact('bkClasses', 'rows', 'start', 'end', 'classId', 'selectedClass'));
    }
}
