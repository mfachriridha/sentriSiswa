<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengaturan;
use App\Models\Presensi;
use App\Models\ProfilSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BkMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $grade = Auth::user()->profilGuru?->tingkat;
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
            }])
            ->whereHas('kelas', fn ($query) => $query->where('tingkat', $grade));

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

        $students = $query->paginate(25)->withQueryString();
        $classes = Kelas::where('tingkat', $grade)->orderBy('nama')->get();
        $maxAlpha = Pengaturan::batasMaksimalAlpha();
        $routePrefix = 'bk.monitoring';
        $title = 'Monitoring BK';
        $description = 'Pantau presensi dan pelanggaran siswa tingkat '.$grade.'.';

        return view('kesiswaan.monitoring.index', compact('students', 'classes', 'search', 'filterClass', 'routePrefix', 'title', 'description', 'maxAlpha'));
    }

    public function show(ProfilSiswa $monitoring): View
    {
        $grade = Auth::user()->profilGuru?->tingkat;

        abort_unless($monitoring->kelas?->tingkat === $grade, 403);

        [$startDate, $endDate] = Pengaturan::rentangTanggalPeriodeAktif();

        $monitoring->load([
            'pengguna',
            'kelas',
            'pelanggaranSiswa' => fn ($query) => $query->disetujui()->latest('tanggal_pelanggaran')->with(['dicatatOleh', 'jenisPelanggaran']),
            'pengajuanPoin' => fn ($query) => $query->disetujui()->latest()->with('diajukanOleh'),
            'absensi' => fn ($query) => $query->latest('tanggal')->take(30),
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
            'backRoute' => route('bk.monitoring.index'),
            'createViolationRoute' => null,
        ]);
    }
}
