<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceRecapController extends Controller
{
    public function index(Request $request): View
    {
        $class = Auth::user()->homeroomClass;

        if (! $class) {
            return view('guru.absensi.empty');
        }

        $mode = $request->get('mode', 'daily');
        $date = $request->get('date', now()->toDateString());
        $month = $request->get('month', now()->format('Y-m'));

        $students = $class->students()
            ->join('users', 'student_profiles.user_id', '=', 'users.id')
            ->with(['user', 'biodata'])
            ->orderBy('users.name')
            ->select('student_profiles.*')
            ->get();

        if ($mode === 'daily') {
            $attendances = Attendance::where('date', $date)
                ->whereIn('student_profile_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_profile_id');

            $stats = [
                'hadir' => $attendances->where('status', 'hadir')->count(),
                'terlambat' => $attendances->where('status', 'terlambat')->count(),
                'izin' => $attendances->where('status', 'izin')->count(),
                'sakit' => $attendances->where('status', 'sakit')->count(),
                'alpha' => $attendances->where('status', 'alpha')->count(),
                'belum_absen' => $students->count() - $attendances->count(),
            ];
        } else {
            $attendances = Attendance::whereMonth('date', substr($month, 5, 2))
                ->whereYear('date', substr($month, 0, 4))
                ->whereIn('student_profile_id', $students->pluck('id'))
                ->get()
                ->groupBy('student_profile_id');

            $stats = $this->calculateMonthlyStats($students, $attendances);
        }

        return view('guru.absensi.index', compact('class', 'students', 'attendances', 'stats', 'mode', 'date', 'month'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $class = Auth::user()->homeroomClass;

        if (! $class || $attendance->studentProfile->class_id !== $class->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:hadir,terlambat,izin,sakit,alpha'],
        ]);

        $attendance->update($validated);

        return redirect()->back()->with('success', 'Status absensi berhasil diperbarui.');
    }

    public function exportExcel(Request $request)
    {
        // TODO: Implement Excel export
        return redirect()->back()->with('info', 'Fitur export Excel akan segera tersedia.');
    }

    public function exportPdf(Request $request)
    {
        // TODO: Implement PDF export
        return redirect()->back()->with('info', 'Fitur export PDF akan segera tersedia.');
    }

    private function calculateMonthlyStats($students, $attendances): array
    {
        $totalDays = now()->daysInMonth;
        $stats = [];

        foreach ($students as $student) {
            $studentAttendances = $attendances->get($student->id, collect());

            $stats[$student->id] = [
                'hadir' => $studentAttendances->where('status', 'hadir')->count(),
                'terlambat' => $studentAttendances->where('status', 'terlambat')->count(),
                'izin' => $studentAttendances->where('status', 'izin')->count(),
                'sakit' => $studentAttendances->where('status', 'sakit')->count(),
                'alpha' => $studentAttendances->where('status', 'alpha')->count(),
                'percentage' => $totalDays > 0
                    ? round(($studentAttendances->whereIn('status', ['hadir', 'terlambat'])->count() / $totalDays) * 100, 1)
                    : 0,
            ];
        }

        return $stats;
    }
}
