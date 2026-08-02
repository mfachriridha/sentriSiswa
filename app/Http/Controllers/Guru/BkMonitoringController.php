<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
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

        $query = ProfilSiswa::with(['pengguna', 'kelas'])
            ->whereHas('pengguna', fn ($query) => $query->where('status', 'registered'))
            ->withSum(['pelanggaranSiswa' => fn ($query) => $query->disetujui()], 'pengurangan_poin')
            ->withSum(['pengajuanPoin' => fn ($query) => $query->disetujui()], 'jumlah_poin')
            ->with(['absensi' => fn ($query) => $query->whereDate('tanggal', today())])
            ->withCount(['absensi as total_attendances'])
            ->withCount(['absensi as present_attendances' => fn ($query) => $query->where('status', 'hadir')])
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
        $routePrefix = 'bk.monitoring';
        $title = 'Monitoring BK';
        $description = 'Pantau presensi dan pelanggaran siswa tingkat '.$grade.'.';

        return view('kesiswaan.monitoring.index', compact('students', 'classes', 'search', 'filterClass', 'routePrefix', 'title', 'description'));
    }

    public function show(ProfilSiswa $monitoring): View
    {
        $grade = Auth::user()->profilGuru?->tingkat;

        abort_unless($monitoring->kelas?->tingkat === $grade, 403);

        $monitoring->load([
            'pengguna',
            'kelas',
            'pelanggaranSiswa' => fn ($query) => $query->disetujui()->latest('tanggal_pelanggaran')->with(['dicatatOleh', 'jenisPelanggaran']),
            'pengajuanPoin' => fn ($query) => $query->disetujui()->latest()->with('diajukanOleh'),
            'absensi' => fn ($query) => $query->latest('tanggal')->take(30),
        ]);

        return view('kesiswaan.monitoring.show', [
            'student' => $monitoring,
            'backRoute' => route('bk.monitoring.index'),
            'createViolationRoute' => null,
        ]);
    }
}
