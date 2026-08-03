<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengaturan;
use App\Models\Presensi;
use App\Models\ProfilSiswa;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterClass = $request->get('kelas_id', '');
        [$startDate, $endDate] = Pengaturan::rentangTanggalPeriodeAktif();

        $query = ProfilSiswa::with(['pengguna', 'kelas'])
            ->whereHas('pengguna', fn ($query) => $query->where('status', 'registered'))
            ->withSum(['pelanggaranSiswa' => fn ($query) => $query->disetujui()], 'pengurangan_poin')
            ->withSum(['pengajuanPoin' => fn ($query) => $query->disetujui()], 'jumlah_poin')
            ->with(['absensi' => fn ($query) => $query->whereDate('tanggal', today())])
            ->withCount(['absensi as total_attendances'])
            ->withCount(['absensi as present_attendances' => fn ($query) => $query->where('status', 'hadir')])
            ->withCount(['absensi as period_alpha_count' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'alpha')
                    ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
            }]);

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
        $maxAlpha = Pengaturan::batasMaksimalAlpha();
        $routePrefix = 'kesiswaan.monitoring';
        $title = 'Monitoring Siswa';
        $description = 'Pantau kehadiran dan pelanggaran siswa secara keseluruhan.';

        return view('kesiswaan.monitoring.index', compact('students', 'classes', 'search', 'filterClass', 'routePrefix', 'title', 'description', 'maxAlpha'));
    }

    public function show(ProfilSiswa $monitoring): View
    {
        [$startDate, $endDate] = Pengaturan::rentangTanggalPeriodeAktif();

        // Load relasi yang diperlukan untuk detail
        $monitoring->load([
            'pengguna',
            'kelas',
            'pelanggaranSiswa' => fn ($q) => $q->disetujui()->latest('tanggal_pelanggaran')->with(['dicatatOleh', 'jenisPelanggaran']),
            'pengajuanPoin' => fn ($q) => $q->disetujui()->latest()->with('diajukanOleh'),
            'absensi' => fn ($q) => $q->latest('tanggal')->take(30),
        ]);

        $periodAlphaCount = Presensi::where('profil_siswa_id', $monitoring->nisn)
            ->where('status', 'alpha')
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->count();

        $maxAlpha = Pengaturan::batasMaksimalAlpha();
        $warningStatus = Pengaturan::statusPeringatanAlpha($periodAlphaCount);

        return view('kesiswaan.monitoring.show', [
            'student' => $monitoring,
            'periodAlphaCount' => $periodAlphaCount,
            'maxAlpha' => $maxAlpha,
            'warningStatus' => $warningStatus,
            'backRoute' => route('kesiswaan.monitoring.index'),
            'createViolationRoute' => route('kesiswaan.pelanggaran-siswa.create', ['profil_siswa_id' => $monitoring->nisn]),
        ]);
    }
}
