<?php

namespace App\Http\Controllers\Guru;

use App\Exports\AttendanceRecapExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\AttendanceRecapFilterRequest;
use App\Models\Attendance;
use App\Models\StudentProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class AttendanceRecapController extends Controller
{
    public function index(AttendanceRecapFilterRequest $request): View
    {
        $class = Auth::user()->homeroomClass;

        if (! $class) {
            return view('wali-kelas.absensi.empty');
        }

        [$startDate, $endDate] = $this->dateRange($request);
        $validated = $request->validated();
        $filterStudents = $this->classStudents($class->id);
        $students = $this->classStudents($class->id, $validated['student_id'] ?? null);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));
        $statusFilter = $validated['status'] ?? '';
        $students = $this->filterStudentsByStatus($students, $stats, $statusFilter);
        $selectedStudent = $validated['student_id'] ?? '';
        $selectedMonth = $validated['month'] ?? '';

        return view('wali-kelas.absensi.index', compact('class', 'students', 'filterStudents', 'stats', 'startDate', 'endDate', 'statusFilter', 'selectedStudent', 'selectedMonth'));
    }

    public function exportExcel(AttendanceRecapFilterRequest $request): BinaryFileResponse
    {
        $class = Auth::user()->homeroomClass;

        if (! $class) {
            abort(403);
        }

        [$startDate, $endDate] = $this->dateRange($request);
        $validated = $request->validated();
        $students = $this->classStudents($class->id, $validated['student_id'] ?? null);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));
        $students = $this->filterStudentsByStatus($students, $stats, $validated['status'] ?? '');

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

    public function exportPdf(AttendanceRecapFilterRequest $request): Response
    {
        $class = Auth::user()->homeroomClass;

        if (! $class) {
            abort(403);
        }

        [$startDate, $endDate] = $this->dateRange($request);
        $validated = $request->validated();
        $students = $this->classStudents($class->id, $validated['student_id'] ?? null);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));
        $students = $this->filterStudentsByStatus($students, $stats, $validated['status'] ?? '');

        $pdf = Pdf::loadView('exports.attendance-recap-pdf', [
            'title' => 'Rekap Absensi',
            'className' => $class->name,
            'students' => $students,
            'stats' => $stats,
            'chartRows' => $this->attendanceChartRows($stats),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("rekap-absensi-{$class->name}-{$startDate}-sampai-{$endDate}.pdf");
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function dateRange(AttendanceRecapFilterRequest $request): array
    {
        $validated = $request->validated();

        if (! empty($validated['month'])) {
            $start = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth()->toDateString();
            $end = Carbon::createFromFormat('Y-m', $validated['month'])->endOfMonth()->toDateString();

            return [$start, $end];
        }

        return [
            $validated['start_date'] ?? now()->startOfMonth()->toDateString(),
            $validated['end_date'] ?? now()->toDateString(),
        ];
    }

    /**
     * @return Collection<int, StudentProfile>
     */
    private function classStudents(int $classId, int|string|null $studentId = null): Collection
    {
        return StudentProfile::query()
            ->where('class_id', $classId)
            ->when($studentId, fn ($query) => $query->where('student_profiles.id', $studentId))
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

    private function filterStudentsByStatus(Collection $students, array $stats, ?string $status): Collection
    {
        if (blank($status)) {
            return $students;
        }

        return $students
            ->filter(fn (StudentProfile $student): bool => ($stats[$student->id][$status] ?? 0) > 0)
            ->values();
    }

    /**
     * @param  array<int, array{hadir: int, terlambat: int, izin: int, sakit: int, alpha: int, percentage: float}>  $stats
     * @return list<array{label: string, value: int}>
     */
    private function attendanceChartRows(array $stats): array
    {
        return [
            ['label' => 'Hadir', 'value' => collect($stats)->sum('hadir')],
            ['label' => 'Terlambat', 'value' => collect($stats)->sum('terlambat')],
            ['label' => 'Izin', 'value' => collect($stats)->sum('izin')],
            ['label' => 'Sakit', 'value' => collect($stats)->sum('sakit')],
            ['label' => 'Alpha', 'value' => collect($stats)->sum('alpha')],
        ];
    }
}
