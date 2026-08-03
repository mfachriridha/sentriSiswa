<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\UpdateDailyAttendanceRequest;
use App\Models\Pengaturan;
use App\Models\Presensi;
use App\Models\ProfilSiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        $selectedDate = $request->query('tanggal', now()->toDateString());
        if (! is_string($selectedDate) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate) || $selectedDate < '2025-01-01' || $selectedDate > now()->toDateString()) {
            $selectedDate = now()->toDateString();
        }

        $isToday = ($selectedDate === now()->toDateString());
        $isWeekday = Pengaturan::hariAbsenAktif();
        $activeDaysLabel = Pengaturan::labelHariAbsen();
        $studentIds = $class->siswa()->pluck('profil_siswa.nisn');
        $attendances = Presensi::query()
            ->whereIn('profil_siswa_id', $studentIds)
            ->whereDate('tanggal', $selectedDate)
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

        [$startDate, $endDate] = Pengaturan::rentangTanggalPeriodeAktif();
        $periodAlphaCounts = Presensi::query()
            ->whereIn('profil_siswa_id', $studentIds)
            ->where('status', 'alpha')
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('profil_siswa_id, COUNT(*) as total_alpha')
            ->groupBy('profil_siswa_id')
            ->pluck('total_alpha', 'profil_siswa_id');

        $maxAlpha = Pengaturan::batasMaksimalAlpha();

        return view('wali-kelas.kelas-saya.index', compact(
            'class', 'students', 'attendances', 'stats', 'isWeekday', 'activeDaysLabel',
            'periodAlphaCounts', 'maxAlpha', 'selectedDate', 'isToday'
        ));
    }

    public function statusAbsensi(): JsonResponse
    {
        $class = Auth::user()->kelasWali;

        if (! $class) {
            return response()->json(['stats' => [], 'rows' => []]);
        }

        $today = now()->toDateString();
        $studentIds = $class->siswa()->pluck('profil_siswa.nisn');
        $attendances = Presensi::whereIn('profil_siswa_id', $studentIds)
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

        $targetDate = $request->input('tanggal') ?: $request->query('tanggal', now()->toDateString());
        if (! is_string($targetDate) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $targetDate) || $targetDate < '2025-01-01' || $targetDate > now()->toDateString()) {
            $targetDate = now()->toDateString();
        }

        $carbonDate = Carbon::parse($targetDate);
        if (! Pengaturan::hariAbsenAktif($carbonDate)) {
            return redirect()->route('wali-kelas.kelas-saya', ['tanggal' => $targetDate])
                ->with('error', 'Absensi hanya tersedia pada hari '.Pengaturan::labelHariAbsen().'.');
        }

        $attendance = Presensi::query()
            ->where('profil_siswa_id', $profilSiswa->nisn)
            ->whereDate('tanggal', $targetDate)
            ->first();

        if (! $attendance) {
            $attendance = new Presensi([
                'profil_siswa_id' => $profilSiswa->nisn,
                'tanggal' => $targetDate,
            ]);
        }

        $attendance->status = $request->validated('status');
        $attendance->save();

        $message = ($targetDate === now()->toDateString())
            ? 'Status absensi hari ini berhasil diperbarui.'
            : 'Status presensi tanggal '.$carbonDate->locale('id')->translatedFormat('d F Y').' berhasil diperbarui.';

        return redirect()->route('wali-kelas.kelas-saya', ['tanggal' => $targetDate])
            ->with('success', $message);
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

        [$startDate, $endDate] = Pengaturan::rentangTanggalPeriodeAktif();

        $startStr = $startDate->format('Y-m-d 00:00:00');
        $endStr = $endDate->format('Y-m-d 23:59:59');

        $periodAlphaCount = Presensi::where('profil_siswa_id', $profilSiswa->nisn)
            ->where('status', 'alpha')
            ->whereBetween('tanggal', [$startStr, $endStr])
            ->count();

        $maxAlpha = Pengaturan::batasMaksimalAlpha();
        $warningStatus = Pengaturan::statusPeringatanAlpha($periodAlphaCount);

        return view('kesiswaan.monitoring.show', [
            'student' => $profilSiswa,
            'periodAlphaCount' => $periodAlphaCount,
            'maxAlpha' => $maxAlpha,
            'warningStatus' => $warningStatus,
            'backRoute' => route('wali-kelas.kelas-saya'),
            'backLabel' => 'Kembali ke Kelas Saya',
            'createViolationRoute' => null,
        ]);
    }
}
