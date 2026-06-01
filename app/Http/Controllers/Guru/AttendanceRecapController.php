<?php

namespace App\Http\Controllers\Guru;

use App\Exports\AttendanceRecapExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\AttendanceRecapFilterRequest;
use App\Models\Attendance;
use App\Models\StudentProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttendanceRecapController extends Controller
{
    public function index(AttendanceRecapFilterRequest $request): View
    {
        $class = Auth::user()->homeroomClass;

        if (! $class) {
            return view('guru.absensi.empty');
        }

        [$startDate, $endDate] = $this->dateRange($request);
        $students = $this->classStudents($class->id);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));

        return view('guru.absensi.index', compact('class', 'students', 'stats', 'startDate', 'endDate'));
    }

    public function exportExcel(AttendanceRecapFilterRequest $request): BinaryFileResponse
    {
        $class = Auth::user()->homeroomClass;

        if (! $class) {
            abort(403);
        }

        [$startDate, $endDate] = $this->dateRange($request);
        $students = $this->classStudents($class->id);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));

        $rows = $students->map(function (StudentProfile $student) use ($stats): array {
            $stat = $stats[$student->id];

            return [
                $student->nis ?? '-',
                $student->user->name,
                $stat['hadir'],
                $stat['terlambat'],
                $stat['izin'],
                $stat['sakit'],
                $stat['alpha'],
                $stat['percentage'].'%',
            ];
        })->values()->all();

        return Excel::download(
            new AttendanceRecapExport($rows),
            "rekap-absensi-{$class->name}-{$startDate}-sampai-{$endDate}.xlsx",
        );
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function dateRange(AttendanceRecapFilterRequest $request): array
    {
        $validated = $request->validated();

        return [
            $validated['start_date'] ?? now()->startOfMonth()->toDateString(),
            $validated['end_date'] ?? now()->toDateString(),
        ];
    }

    /**
     * @return Collection<int, StudentProfile>
     */
    private function classStudents(int $classId): Collection
    {
        return StudentProfile::query()
            ->where('class_id', $classId)
            ->join('users', 'student_profiles.user_id', '=', 'users.id')
            ->with('user')
            ->orderBy('users.name')
            ->select('student_profiles.*')
            ->get();
    }

    /**
     * @param  Collection<int, StudentProfile>  $students
     * @return Collection<int, Collection<int, Attendance>>
     */
    private function attendancesByStudent(Collection $students, string $startDate, string $endDate): Collection
    {
        return Attendance::query()
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->whereIn('student_profile_id', $students->pluck('id'))
            ->get()
            ->filter(fn (Attendance $attendance): bool => $attendance->date->isWeekday())
            ->groupBy('student_profile_id');
    }

    /**
     * @param  Collection<int, StudentProfile>  $students
     * @param  Collection<int, Collection<int, Attendance>>  $attendances
     * @return array<int, array{hadir: int, terlambat: int, izin: int, sakit: int, alpha: int, percentage: float}>
     */
    private function calculateStats(Collection $students, Collection $attendances): array
    {
        $stats = [];

        foreach ($students as $student) {
            $studentAttendances = $attendances->get($student->id, collect());
            $finalAttendances = $studentAttendances->whereIn('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha']);
            $finalAttendanceCount = $finalAttendances->count();

            $stats[$student->id] = [
                'hadir' => $finalAttendances->where('status', 'hadir')->count(),
                'terlambat' => $finalAttendances->where('status', 'terlambat')->count(),
                'izin' => $finalAttendances->where('status', 'izin')->count(),
                'sakit' => $finalAttendances->where('status', 'sakit')->count(),
                'alpha' => $finalAttendances->where('status', 'alpha')->count(),
                'percentage' => $finalAttendanceCount > 0
                    ? round(($finalAttendances->whereIn('status', ['hadir', 'terlambat'])->count() / $finalAttendanceCount) * 100, 1)
                    : 0,
            ];
        }

        return $stats;
    }
}
