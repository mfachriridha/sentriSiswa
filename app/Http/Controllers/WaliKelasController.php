<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentViolation;
use App\Models\TeacherClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaliKelasController extends Controller
{
    public function dashboard()
    {
        $teacherClass = $this->getTeacherClass();

        if (! $teacherClass) {
            return view('wali-kelas.dashboard', [
                'schoolClass' => null,
                'attendances' => collect(),
                'students' => collect(),
            ])->with('error', 'Anda belum di-assign ke kelas manapun.');
        }

        $today = now()->toDateString();

        $students = Student::where('school_class_id', $teacherClass->school_class_id)
            ->orderBy('name')
            ->get();

        $attendances = Attendance::whereIn('student_id', $students->pluck('id'))
            ->where('date', $today)
            ->get()
            ->keyBy('student_id');

        return view('wali-kelas.dashboard', compact('teacherClass', 'students', 'attendances'));
    }

    public function laporan(Request $request)
    {
        $teacherClass = $this->getTeacherClass();

        if (! $teacherClass) {
            return back()->with('error', 'Anda belum di-assign ke kelas manapun.');
        }

        $students = Student::where('school_class_id', $teacherClass->school_class_id)
            ->orderBy('name')
            ->get();

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

        $periods = [
            'Minggu Ini' => [now()->startOfWeek(), now()->endOfWeek()],
            'Bulan Ini' => [now()->startOfMonth(), now()->endOfMonth()],
            'Semester Ini' => [now()->startOfYear()->month >= 7 ? now()->create(now()->year, 7, 1) : now()->create(now()->year, 1, 1), now()],
        ];

        return view('wali-kelas.laporan', compact('teacherClass', 'rows', 'start', 'end', 'periods'));
    }

    protected function getTeacherClass(): ?TeacherClass
    {
        return TeacherClass::where('user_id', Auth::id())
            ->where('role', 'wali_kelas')
            ->with('schoolClass')
            ->first();
    }

    public function confirm(Attendance $attendance)
    {
        $attendance->update([
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Presensi dikonfirmasi. Notifikasi akan dikirim ke orang tua.']);
    }

    public function updateStatus(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alfa',
            'note' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $data['status'],
            'note' => $data['note'] ?? $attendance->note,
        ]);

        return response()->json(['success' => true, 'message' => 'Status presensi diubah.']);
    }
}
