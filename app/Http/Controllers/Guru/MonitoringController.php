<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\ProfilSiswa;
use App\Services\AbsenceWarningService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function index(Request $request, AbsenceWarningService $absenceWarning): View
    {
        $search = $request->get('search', '');
        $filterClass = $request->get('kelas_id', '');

        $query = ProfilSiswa::with(['pengguna', 'kelas'])
            ->withSum(['pelanggaranSiswa' => fn ($query) => $query->approved()], 'pengurangan_poin')
            ->with(['absensi' => fn ($query) => $query->whereDate('tanggal', today())])
            ->withCount(['absensi as total_attendances'])
            ->withCount(['absensi as present_attendances' => fn ($query) => $query->whereIn('status', ['hadir', 'terlambat'])]);

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereHas('pengguna', fn ($userQuery) => $userQuery->where('nama', 'like', "%{$search}%"))
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($filterClass) {
            $query->where('kelas_id', $filterClass);
        }

        $students = $query->paginate(25)->appends([
            'search' => $search,
            'kelas_id' => $filterClass,
        ]);

        $classes = Kelas::orderBy('tingkat')->orderBy('nama')->get();
        $routePrefix = 'kesiswaan.monitoring';
        $title = 'Monitoring Siswa';
        $description = 'Pantau kehadiran dan pelanggaran siswa secara keseluruhan.';
        $alphaWarnings = $absenceWarning->alphaCountsForStudentIds($students->getCollection()->pluck('nisn'));
        $warningThreshold = AbsenceWarningService::Threshold;

        return view('kesiswaan.monitoring.index', compact('students', 'classes', 'search', 'filterClass', 'routePrefix', 'title', 'description', 'alphaWarnings', 'warningThreshold'));
    }

    public function show(ProfilSiswa $monitoring, AbsenceWarningService $absenceWarning): View
    {
        // Load relasi yang diperlukan untuk detail
        $monitoring->load([
            'pengguna',
            'kelas',
            'biodata',
            'pelanggaranSiswa' => fn ($q) => $q->approved()->latest('tanggal_pelanggaran')->with(['dicatatOleh', 'jenisPelanggaran']),
            'absensi' => fn ($q) => $q->latest('tanggal')->take(30),
        ]);

        return view('kesiswaan.monitoring.show', [
            'student' => $monitoring,
            'backRoute' => route('kesiswaan.monitoring.index'),
            'createViolationRoute' => route('kesiswaan.pelanggaran-siswa.create', ['profil_siswa_id' => $monitoring->nisn]),
            'alphaWarningCount' => $absenceWarning->alphaCountForStudentId($monitoring->nisn),
            'warningThreshold' => AbsenceWarningService::Threshold,
        ]);
    }
}
