<?php

namespace App\Http\Controllers\Bk;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\AttendanceRecapFilterRequest;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class AbsensiRecapController extends Controller
{
    public function index(AttendanceRecapFilterRequest $request): View
    {
        $bkGrade = Auth::user()->loadMissing('teacherProfile')->teacherProfile?->grade;

        if (! $bkGrade) {
            return view('bk.laporan.empty');
        }

        $classes = SchoolClass::where('grade', $bkGrade)->orderBy('name')->get();
        [$startDate, $endDate] = $this->dateRange($request);
        $validated = $request->validated();
        $selectedClassId = $request->query('class_id', '');
        $statusFilter = $validated['status'] ?? '';
        $selectedStudent = $validated['student_id'] ?? '';
        $selectedMonth = $validated['month'] ?? '';

        $classIds = filled($selectedClassId)
            ? [(int) $selectedClassId]
            : $classes->pluck('id')->toArray();

        $filterStudents = $this->gradeStudents($classIds);
        $students = $this->gradeStudents($classIds, $validated['student_id'] ?? null);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));
        $students = $this->filterStudentsByStatus($students, $stats, $statusFilter);

        return view('bk.laporan.index', compact(
            'classes', 'students', 'filterStudents', 'stats',
            'startDate', 'endDate', 'statusFilter', 'selectedStudent',
            'selectedMonth', 'selectedClassId', 'bkGrade'
        ));
    }

    public function exportExcel(AttendanceRecapFilterRequest $request): BinaryFileResponse
    {
        $bkGrade = Auth::user()->loadMissing('teacherProfile')->teacherProfile?->grade;

        if (! $bkGrade) {
            abort(403);
        }

        $classes = SchoolClass::where('grade', $bkGrade)->orderBy('name')->get();
        [$startDate, $endDate] = $this->dateRange($request);
        $validated = $request->validated();
        $selectedClassId = $request->query('class_id', '');
        $classIds = filled($selectedClassId) ? [(int) $selectedClassId] : $classes->pluck('id')->toArray();

        $students = $this->gradeStudents($classIds, $validated['student_id'] ?? null);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));
        $students = $this->filterStudentsByStatus($students, $stats, $validated['status'] ?? '');

        $rows = $students->map(function (StudentProfile $student) use ($stats): array {
            $stat = $stats[$student->id];

            return [
                $student->nis ?? '-',
                $student->user->name,
                $student->class?->name ?? '-',
                $stat['hadir'],
                $stat['terlambat'],
                $stat['izin'],
                $stat['sakit'],
                $stat['alpha'],
                $stat['percentage'].'%',
            ];
        })->values()->all();

        return Excel::download(
            new ArrayExport(['NIS', 'Nama', 'Kelas', 'Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpha', 'Kehadiran'], $rows),
            "rekap-absensi-tingkat-{$bkGrade}-{$startDate}-sampai-{$endDate}.xlsx",
        );
    }

    public function exportPdf(AttendanceRecapFilterRequest $request): Response
    {
        $bkGrade = Auth::user()->loadMissing('teacherProfile')->teacherProfile?->grade;

        if (! $bkGrade) {
            abort(403);
        }

        $classes = SchoolClass::where('grade', $bkGrade)->orderBy('name')->get();
        [$startDate, $endDate] = $this->dateRange($request);
        $validated = $request->validated();
        $selectedClassId = $request->query('class_id', '');
        $classIds = filled($selectedClassId) ? [(int) $selectedClassId] : $classes->pluck('id')->toArray();

        $students = $this->gradeStudents($classIds, $validated['student_id'] ?? null);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));
        $students = $this->filterStudentsByStatus($students, $stats, $validated['status'] ?? '');

        $pdf = Pdf::loadView('exports.attendance-recap-pdf', [
            'title' => "Rekap Absensi Tingkat {$bkGrade}",
            'className' => "Tingkat {$bkGrade}",
            'students' => $students,
            'stats' => $stats,
            'chartRows' => $this->attendanceChartRows($stats),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("rekap-absensi-tingkat-{$bkGrade}-{$startDate}-sampai-{$endDate}.pdf");
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
     * @param  list<int>  $classIds
     * @return Collection<int, StudentProfile>
     */
    private function gradeStudents(array $classIds, int|string|null $studentId = null): Collection
    {
        return StudentProfile::query()
            ->whereIn('class_id', $classIds)
            ->when($studentId, fn ($query) => $query->where('student_profiles.id', $studentId))
            ->join('users', 'student_profiles.user_id', '=', 'users.id')
            ->with(['user', 'class'])
            ->orderBy('student_profiles.class_id')
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
