<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(): View
    {
        $student = Auth::user();
        $profile = $student->studentProfile;

        $today = now()->toDateString();
        $todayAttendance = $profile?->attendances()->where('date', $today)->first();

        $startTime = Setting::get('attendance_start_time', '06:30');
        $endTime = Setting::get('attendance_end_time', '07:00');
        $lateTime = Setting::get('attendance_late_time', '07:00');

        $now = now();
        $currentTime = $now->format('H:i');
        $canCheckIn = $currentTime >= $startTime && $currentTime <= $endTime;

        $currentMonth = $now->month;
        $currentYear = $now->year;
        $monthAttendances = $profile?->attendances()
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get() ?? collect();

        $stats = [
            'hadir' => $monthAttendances->where('status', 'hadir')->count(),
            'terlambat' => $monthAttendances->where('status', 'terlambat')->count(),
            'izin' => $monthAttendances->where('status', 'izin')->count(),
            'sakit' => $monthAttendances->where('status', 'sakit')->count(),
            'alpha' => $monthAttendances->where('status', 'alpha')->count(),
        ];

        return view('siswa.absensi.index', compact(
            'todayAttendance',
            'startTime',
            'endTime',
            'canCheckIn',
            'stats',
            'currentMonth',
            'currentYear'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $student = Auth::user();
        $profile = $student->studentProfile;

        if (! $profile) {
            return redirect()->route('siswa.absensi')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $today = now()->toDateString();
        $existing = $profile->attendances()->where('date', $today)->first();

        if ($existing) {
            return redirect()->route('siswa.absensi')->with('error', 'Anda sudah absen hari ini.');
        }

        $startTime = Setting::get('attendance_start_time', '06:30');
        $endTime = Setting::get('attendance_end_time', '07:00');
        $lateTime = Setting::get('attendance_late_time', '07:00');
        $currentTime = now()->format('H:i');

        if ($currentTime < $startTime || $currentTime > $endTime) {
            return redirect()->route('siswa.absensi')->with('error', 'Waktu absen sudah lewat atau belum dimulai.');
        }

        $request->validate([
            'selfie' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $status = ($currentTime > $lateTime) ? 'terlambat' : 'hadir';
        $selfiePath = $request->file('selfie')->store('attendance-selfies/'.$profile->id, 'public');

        $profile->attendances()->create([
            'date' => $today,
            'status' => $status,
            'check_in_time' => $currentTime,
            'selfie_path' => $selfiePath,
        ]);

        return redirect()->route('siswa.absensi')->with('success', $status === 'terlambat' ? 'Absen tercatat: Terlambat.' : 'Absen berhasil: Hadir.');
    }

    public function riwayat(Request $request): View
    {
        $student = Auth::user();
        $profile = $student->studentProfile;
        $selectedMonth = $request->query('month', now()->format('Y-m'));

        if (! is_string($selectedMonth) || ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $selectedMonth)) {
            $selectedMonth = now()->format('Y-m');
        }

        $month = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();

        $attendances = $profile?->attendances()
            ->whereBetween('date', [
                $month->toDateString(),
                $month->copy()->endOfMonth()->toDateString(),
            ])
            ->latest('date')
            ->get() ?? collect();

        $monthLabel = $month->translatedFormat('F Y');

        return view('siswa.absensi.riwayat', compact('attendances', 'monthLabel', 'selectedMonth'));
    }
}
