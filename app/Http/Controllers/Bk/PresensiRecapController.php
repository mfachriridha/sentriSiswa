<?php

namespace App\Http\Controllers\Bk;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\AttendanceRecapFilterRequest;
use App\Models\Kelas;
use App\Models\Pengaturan;
use App\Models\Presensi;
use App\Models\ProfilSiswa;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PresensiRecapController extends Controller
{
    public function index(AttendanceRecapFilterRequest $request): View
    {
        $bkGrade = Auth::user()->loadMissing('profilGuru')->profilGuru?->tingkat;

        if (! $bkGrade) {
            return view('bk.laporan.empty');
        }

        $classes = Kelas::where('tingkat', $bkGrade)->orderBy('nama')->get();
        [$startDate, $endDate] = $this->dateRange($request);
        $validated = $request->validated();
        $statusFilter = $validated['status'] ?? '';
        $selectedStudent = $validated['profil_siswa_id'] ?? '';
        $selectedMonth = $validated['month'] ?? '';

        [$classIds, $selectedClassId] = $this->resolveClassIds($request, $classes);

        $filterStudents = $this->gradeStudents($classIds);
        $students = $this->gradeStudents($classIds, $validated['profil_siswa_id'] ?? null);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));
        $students = $this->paginateStudents($this->filterStudentsByStatus($students, $stats, $statusFilter), $request);

        return view('bk.laporan.index', compact(
            'classes', 'students', 'filterStudents', 'stats',
            'startDate', 'endDate', 'statusFilter', 'selectedStudent',
            'selectedMonth', 'selectedClassId', 'bkGrade'
        ));
    }

    public function exportExcel(AttendanceRecapFilterRequest $request): BinaryFileResponse|RedirectResponse
    {
        $bkGrade = Auth::user()->loadMissing('profilGuru')->profilGuru?->tingkat;

        if (! $bkGrade) {
            abort(403);
        }

        $classes = Kelas::where('tingkat', $bkGrade)->orderBy('nama')->get();
        [$startDate, $endDate] = $this->dateRange($request);
        $validated = $request->validated();
        [$classIds] = $this->resolveClassIds($request, $classes);

        $students = $this->gradeStudents($classIds, $validated['profil_siswa_id'] ?? null);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));
        $students = $this->filterStudentsByStatus($students, $stats, $validated['status'] ?? '');

        $rows = $students->map(function (ProfilSiswa $student) use ($stats): array {
            $stat = $stats[$student->nisn];

            return [
                $student->nis ?? '-',
                $student->pengguna->nama,
                $student->kelas?->nama ?? '-',
                $stat['hadir'],
                $stat['izin'],
                $stat['sakit'],
                $stat['dispensasi'],
                $stat['alpha'],
            ];
        })->values()->all();

        if ($rows === []) {
            return redirect()
                ->route('bk.laporan.index', $request->query())
                ->with('error', 'Tidak ada siswa yang cocok dengan penyaring ini, jadi tidak ada yang bisa diekspor.');
        }

        return Excel::download(
            new ArrayExport(['NIS', 'Nama', 'Kelas', 'Hadir', 'Izin', 'Sakit', 'Alpha'], $rows),
            "rekap-absensi-tingkat-{$bkGrade}-{$startDate}-sampai-{$endDate}.xlsx",
        );
    }

    public function cetak(AttendanceRecapFilterRequest $request): View
    {
        $bkGrade = Auth::user()->loadMissing('profilGuru')->profilGuru?->tingkat;

        if (! $bkGrade) {
            abort(403);
        }

        $classes = Kelas::where('tingkat', $bkGrade)->orderBy('nama')->get();
        [$startDate, $endDate] = $this->dateRange($request);
        $validated = $request->validated();
        [$classIds] = $this->resolveClassIds($request, $classes);

        $students = $this->gradeStudents($classIds, $validated['profil_siswa_id'] ?? null);
        $stats = $this->calculateStats($students, $this->attendancesByStudent($students, $startDate, $endDate));
        $students = $this->filterStudentsByStatus($students, $stats, $validated['status'] ?? '');

        return view('cetak.rekap-absensi', [
            'judul' => "Rekap Absensi Tingkat {$bkGrade}",
            'subjudul' => "Tingkat {$bkGrade}",
            'tampilkanKelas' => true,
            'students' => $students,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    private function resolveClassIds(AttendanceRecapFilterRequest $request, Collection $classes): array
    {
        $ownClassIds = $classes->pluck('id')->toArray();
        $selectedClassId = $request->query('kelas_id', '');

        if (filled($selectedClassId) && in_array((int) $selectedClassId, $ownClassIds, true)) {
            return [[(int) $selectedClassId], $selectedClassId];
        }

        return [$ownClassIds, ''];
    }

    private function dateRange(AttendanceRecapFilterRequest $request): array
    {
        $validated = $request->validated();

        if (! empty($validated['month'])) {
            $start = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth()->toDateString();
            $end = Carbon::createFromFormat('Y-m', $validated['month'])->endOfMonth()->toDateString();

            return [$start, $end];
        }

        [$startPeriode, $endPeriode] = Pengaturan::rentangTanggalPeriodeAktif();

        return [
            $validated['mulai'] ?? $startPeriode->toDateString(),
            $validated['selesai'] ?? $endPeriode->toDateString(),
        ];
    }

    private function gradeStudents(array $classIds, ?string $studentId = null): Collection
    {
        return ProfilSiswa::query()
            ->whereIn('kelas_id', $classIds)
            ->when($studentId, fn ($query) => $query->where('profil_siswa.nisn', $studentId))
            ->join('pengguna', 'profil_siswa.pengguna_id', '=', 'pengguna.id')
            ->where('pengguna.status', 'registered')
            ->with(['pengguna', 'kelas'])
            ->orderBy('profil_siswa.kelas_id')
            ->orderBy('pengguna.nama')
            ->select('profil_siswa.*')
            ->get();
    }

    private function attendancesByStudent(Collection $students, string $startDate, string $endDate): Collection
    {
        return Presensi::query()
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->whereIn('profil_siswa_id', $students->pluck('nisn'))
            ->get()
            ->groupBy('profil_siswa_id');
    }

    private function calculateStats(Collection $students, Collection $attendances): array
    {
        $stats = [];

        foreach ($students as $student) {
            $studentAttendances = $attendances->get($student->nisn, collect());
            $finalAttendances = $studentAttendances->whereIn('status', ['hadir', 'izin', 'sakit', 'dispensasi', 'alpha']);

            $stats[$student->nisn] = [
                'hadir' => $finalAttendances->where('status', 'hadir')->count(),
                'izin' => $finalAttendances->where('status', 'izin')->count(),
                'sakit' => $finalAttendances->where('status', 'sakit')->count(),
                'dispensasi' => $finalAttendances->where('status', 'dispensasi')->count(),
                'alpha' => $finalAttendances->where('status', 'alpha')->count(),
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
