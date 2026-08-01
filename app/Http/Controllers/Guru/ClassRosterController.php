<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\UpdateDailyAttendanceRequest;
use App\Models\Absensi;
use App\Models\Pengaturan;
use App\Models\ProfilSiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClassRosterController extends Controller
{
    public function index(Request $request): View
    {
        $class = Auth::user()->kelasWali;

        if (! $class) {
            return view('wali-kelas.kelas-saya.empty');
        }

        $today = now()->toDateString();
        $isWeekday = Pengaturan::hariAbsenAktif();
        $activeDaysLabel = Pengaturan::labelHariAbsen();
        $studentIds = $class->siswa()->pluck('profil_siswa.nisn');
        $attendances = Absensi::query()
            ->whereIn('profil_siswa_id', $studentIds)
            ->whereDate('tanggal', $today)
            ->get()
            ->keyBy('profil_siswa_id');

        $students = $class->siswa()
            ->join('pengguna', 'profil_siswa.pengguna_id', '=', 'pengguna.id')
            ->where('pengguna.status', 'registered')
            ->with('pengguna')
            ->when($request->string('search')->toString(), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('pengguna.nama', 'like', "%{$search}%")
                        ->orWhere('profil_siswa.nis', 'like', "%{$search}%");
                });
            })
            ->orderBy('pengguna.nama')
            ->select('profil_siswa.*')
            ->get();

        $stats = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'dispensasi' => 0,
            'alpha' => 0,
            'belum_absen' => 0,
        ];

        foreach ($studentIds as $studentId) {
            $status = $attendances->get($studentId)?->status ?? 'belum_absen';
            $stats[$status]++;
        }

        return view('wali-kelas.kelas-saya.index', compact('class', 'students', 'attendances', 'stats', 'isWeekday', 'activeDaysLabel'));
    }

    public function statusAbsensi(): JsonResponse
    {
        $class = Auth::user()->kelasWali;

        if (! $class) {
            return response()->json(['stats' => [], 'rows' => []]);
        }

        $today = now()->toDateString();
        $studentIds = $class->siswa()->pluck('profil_siswa.nisn');
        $attendances = Absensi::whereIn('profil_siswa_id', $studentIds)
            ->whereDate('tanggal', $today)
            ->get()
            ->keyBy('profil_siswa_id');

        $stats = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'dispensasi' => 0, 'alpha' => 0, 'belum_absen' => 0];
        foreach ($studentIds as $id) {
            $status = $attendances->get($id)?->status ?? 'belum_absen';
            $stats[$status]++;
        }

        $rows = $class->siswa()->with('pengguna')->get()->map(fn ($sp) => [
            'id' => $sp->nisn,
            'status' => $attendances->get($sp->nisn)?->status ?? 'belum_absen',
            'check_in_time' => $attendances->get($sp->nisn)?->waktu_masuk,
        ]);

        return response()->json(compact('stats', 'rows'));
    }

    public function updateAttendance(UpdateDailyAttendanceRequest $request, ProfilSiswa $profilSiswa): RedirectResponse
    {
        $class = Auth::user()->kelasWali;

        if (! $class || $profilSiswa->kelas_id !== $class->id) {
            abort(403);
        }

        if (! Pengaturan::hariAbsenAktif()) {
            return redirect()->route('wali-kelas.kelas-saya')->with('error', 'Absensi hanya tersedia pada hari '.Pengaturan::labelHariAbsen().'.');
        }

        $attendance = Absensi::query()
            ->where('profil_siswa_id', $profilSiswa->nisn)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        if (! $attendance) {
            $attendance = new Absensi([
                'profil_siswa_id' => $profilSiswa->nisn,
                'tanggal' => now()->toDateString(),
            ]);
        }

        $attendance->status = $request->validated('status');
        $attendance->save();

        return redirect()->route('wali-kelas.kelas-saya')->with('success', 'Status absensi hari ini berhasil diperbarui.');
    }

    public function show(ProfilSiswa $profilSiswa): View
    {
        $class = Auth::user()->kelasWali;

        if (! $class || $profilSiswa->kelas_id !== $class->id) {
            abort(403);
        }

        $profilSiswa->load([
            'pengguna',
            'kelas',
            'pelanggaranSiswa' => fn ($query) => $query->disetujui()->latest('tanggal_pelanggaran')->with(['dicatatOleh', 'jenisPelanggaran']),
            'pengajuanPoin' => fn ($query) => $query->disetujui()->latest()->with('diajukanOleh'),
            'absensi' => fn ($query) => $query->latest('tanggal')->take(30),
        ]);

        return view('kesiswaan.monitoring.show', [
            'student' => $profilSiswa,
            'backRoute' => route('wali-kelas.kelas-saya'),
            'backLabel' => 'Kembali ke Kelas Saya',
            'createViolationRoute' => null,
        ]);
    }
}
