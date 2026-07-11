<?php

namespace App\Http\Controllers\Guru;

use App\Exports\RekapAbsensiExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\AttendanceRecapFilterRequest;
use App\Models\Absensi;
use App\Models\Pengaturan;
use App\Models\ProfilSiswa;
use App\Services\AbsenceWarningService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class RekapAbsensiController extends Controller
{
    public function index(AttendanceRecapFilterRequest $request): View
    {
        $class = Auth::user()->kelasWali;

        if (! $class) {
            return view('wali-kelas.absensi.empty');
        }

        [$startDate, $endDate] = $this->dateRange($request);
        $validated = $request->validated();
        $filterStudents = $this->classStudents($class->id);
        $students = $this->classStudents($class->id, $validated['profil_siswa_id'] ?? null);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));
        $statusFilter = $validated['status'] ?? '';
        $students = $this->paginateStudents($this->filterStudentsByStatus($students, $stats, $statusFilter), $request);
        $selectedStudent = $validated['profil_siswa_id'] ?? '';
        $selectedMonth = $validated['month'] ?? '';

        return view('wali-kelas.absensi.index', compact('class', 'students', 'filterStudents', 'stats', 'startDate', 'endDate', 'statusFilter', 'selectedStudent', 'selectedMonth'));
    }

    public function exportExcel(AttendanceRecapFilterRequest $request): BinaryFileResponse
    {
        $class = Auth::user()->kelasWali;

        if (! $class) {
            abort(403);
        }

        [$startDate, $endDate] = $this->dateRange($request);
        $validated = $request->validated();
        $students = $this->classStudents($class->id, $validated['profil_siswa_id'] ?? null);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));
        $students = $this->filterStudentsByStatus($students, $stats, $validated['status'] ?? '');

        $rows = $students->map(function (ProfilSiswa $student) use ($stats): array {
            $stat = $stats[$student->nisn];
            $flagged = $stat['alpha'] >= AbsenceWarningService::Threshold;

            return [
                $student->nis ?? '-',
                $student->pengguna->nama,
                $stat['hadir'],
                $stat['terlambat'],
                $stat['izin'],
                $stat['sakit'],
                $stat['alpha'],
                $stat['percentage'].'%',
                $flagged ? 'Perlu tindak lanjut' : '-',
            ];
        })->values()->all();

        return Excel::download(
            new RekapAbsensiExport($rows),
            "rekap-absensi-{$class->nama}-{$startDate}-sampai-{$endDate}.xlsx",
        );
    }

    public function exportPdf(AttendanceRecapFilterRequest $request): Response
    {
        $class = Auth::user()->kelasWali;

        if (! $class) {
            abort(403);
        }

        [$startDate, $endDate] = $this->dateRange($request);
        $validated = $request->validated();
        $students = $this->classStudents($class->id, $validated['profil_siswa_id'] ?? null);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));
        $students = $this->filterStudentsByStatus($students, $stats, $validated['status'] ?? '');

        $pdf = Pdf::loadView('exports.attendance-recap-pdf', [
            'title' => 'Rekap Absensi',
            'className' => $class->nama,
            'students' => $students,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("rekap-absensi-{$class->nama}-{$startDate}-sampai-{$endDate}.pdf");
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
            $validated['mulai'] ?? now()->startOfMonth()->toDateString(),
            $validated['selesai'] ?? now()->toDateString(),
        ];
    }

    /**
     * @return Collection<int, ProfilSiswa>
     */
    private function classStudents(int $classId, ?string $studentId = null): Collection
    {
        return ProfilSiswa::query()
            ->where('kelas_id', $classId)
            ->when($studentId, fn ($query) => $query->where('profil_siswa.nisn', $studentId))
            ->join('pengguna', 'profil_siswa.pengguna_id', '=', 'pengguna.id')
            ->where('pengguna.status', 'registered')
            ->with('pengguna')
            ->orderBy('pengguna.nama')
            ->select('profil_siswa.*')
            ->get();
    }

    /**
     * @param  Collection<int, ProfilSiswa>  $students
     * @return Collection<int, Collection<int, Absensi>>
     */
    private function attendancesByStudent(Collection $students, string $startDate, string $endDate): Collection
    {
        return Absensi::query()
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->whereIn('profil_siswa_id', $students->pluck('nisn'))
            ->get()
            ->filter(fn (Absensi $absensi): bool => Pengaturan::hariAbsenAktif($absensi->tanggal))
            ->groupBy('profil_siswa_id');
    }

    /**
     * @param  Collection<int, ProfilSiswa>  $students
     * @param  Collection<int, Collection<int, Absensi>>  $attendances
     * @return array<int, array{hadir: int, terlambat: int, izin: int, sakit: int, alpha: int, percentage: float}>
     */
    private function calculateStats(Collection $students, Collection $attendances): array
    {
        $stats = [];

        foreach ($students as $student) {
            $studentAttendances = $attendances->get($student->nisn, collect());
            $finalAttendances = $studentAttendances->whereIn('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha']);
            $finalAttendanceCount = $finalAttendances->count();

            $stats[$student->nisn] = [
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
            ->filter(fn (ProfilSiswa $student): bool => ($stats[$student->nisn][$status] ?? 0) > 0)
            ->values();
    }

    /**
     * Stats dihitung di level Collection (bukan query), jadi paginasinya manual.
     * Ekspor sengaja gak lewat sini - file ekspor harus berisi semua baris.
     *
     * @param  Collection<int, ProfilSiswa>  $students
     * @return LengthAwarePaginator<int, ProfilSiswa>
     */
    private function paginateStudents(Collection $students, AttendanceRecapFilterRequest $request): LengthAwarePaginator
    {
        $perPage = 25;
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $students->forPage($page, $perPage)->values(),
            $students->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );
    }
}
