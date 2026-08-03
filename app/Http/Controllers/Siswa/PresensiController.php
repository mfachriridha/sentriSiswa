<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Services\PemeriksaLokasiAbsensi;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PresensiController extends Controller
{
    public function index(): View
    {
        $student = Auth::user();
        $profile = $student->profilSiswa;

        $today = now()->toDateString();
        $todayAttendance = $profile?->presensi()->whereDate('tanggal', $today)->first();

        $startTime = Pengaturan::get('attendance_start_time', '06:30');
        $endTime = Pengaturan::get('attendance_end_time', '07:00');

        $allowDesktop = (bool) Pengaturan::get('attendance_allow_desktop', false);

        $now = now();
        $currentTime = $now->format('H:i');
        $currentTimeLabel = $now->format('H:i');
        $isWeekday = Pengaturan::hariAbsenAktif($now);
        $isHadirWindow = ($isWeekday && $currentTime >= $startTime && $currentTime <= $endTime) || $allowDesktop;
        $isFinalStatus = $todayAttendance && in_array($todayAttendance->status, ['hadir', 'izin', 'sakit', 'dispensasi'], true);
        $canCheckIn = ($isWeekday || $allowDesktop) && ! $isFinalStatus;
        $activeDaysLabel = Pengaturan::labelHariAbsen();

        $geofenceData = Pengaturan::get('attendance_geofence_data');
        $geofenceActive = false;
        if (is_string($geofenceData) && $geofenceData !== '') {
            $decoded = json_decode($geofenceData, true);
            if (is_array($decoded) && isset($decoded['coordinates']) && count($decoded['coordinates']) >= 3) {
                $geofenceActive = true;
            }
        }

        $currentMonth = $now->month;
        $currentYear = $now->year;
        $monthAttendances = $profile?->presensi()
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->get() ?? collect();

        $stats = [
            'hadir' => $monthAttendances->where('status', 'hadir')->count(),
            'izin' => $monthAttendances->where('status', 'izin')->count(),
            'sakit' => $monthAttendances->where('status', 'sakit')->count(),
            'dispensasi' => $monthAttendances->where('status', 'dispensasi')->count(),
            'alpha' => $monthAttendances->where('status', 'alpha')->count(),
        ];

        $allowDesktop = (bool) Pengaturan::get('attendance_allow_desktop', false);

        return view('siswa.presensi.index', compact(
            'todayAttendance',
            'startTime',
            'endTime',
            'canCheckIn',
            'isHadirWindow',
            'stats',
            'currentMonth',
            'currentYear',
            'geofenceActive',
            'currentTimeLabel',
            'isWeekday',
            'activeDaysLabel',
            'allowDesktop',
        ));
    }

    public function statusHariIni(): JsonResponse
    {
        $profile = Auth::user()->profilSiswa;
        $absensi = $profile?->presensi()->whereDate('tanggal', now()->toDateString())->first();

        return response()->json([
            'sudah_absen' => $absensi && in_array($absensi->status, ['hadir', 'izin', 'sakit', 'dispensasi'], true),
            'status' => $absensi?->status ?? 'belum_absen',
        ]);
    }

    public function checkLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric'],
        ]);

        return response()->json($this->evaluateLocation(
            (float) $validated['latitude'],
            (float) $validated['longitude']
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $student = Auth::user();
        $profile = $student->profilSiswa;

        if (! $profile) {
            return redirect()->route('siswa.absensi')->with('error', 'Profil siswa tidak ditemukan.');
        }

        if (! Pengaturan::hariAbsenAktif()) {
            return redirect()->route('siswa.absensi')->with('error', 'Absensi hanya tersedia pada hari '.Pengaturan::labelHariAbsen().'.');
        }

        $today = now()->toDateString();
        $attendance = $profile->presensi()->whereDate('tanggal', $today)->first();

        if (! $attendance) {
            try {
                $attendance = $profile->presensi()->create([
                    'tanggal' => $today,
                    'status' => 'belum_absen',
                ]);
            } catch (QueryException $e) {
                $attendance = $profile->presensi()->whereDate('tanggal', $today)->first();

                if (! $attendance) {
                    throw $e;
                }
            }
        }

        if (in_array($attendance->status, ['hadir', 'izin', 'sakit', 'dispensasi'], strict: true)) {
            return redirect()->route('siswa.absensi')->with('error', 'Anda sudah absen hari ini.');
        }

        $startTime = Pengaturan::get('attendance_start_time', '06:30');
        $endTime = Pengaturan::get('attendance_end_time', '07:00');
        $currentTime = now()->format('H:i');
        $allowDesktop = (bool) Pengaturan::get('attendance_allow_desktop', false);

        $statusSubmitted = $request->input('status');
        if ($statusSubmitted === 'hadir' && ! $allowDesktop) {
            if ($currentTime < $startTime || $currentTime > $endTime) {
                return redirect()->route('siswa.absensi')->with('error', 'Waktu absen sudah lewat atau belum dimulai.');
            }
        }

        $rules = [
            'status' => ['required', Rule::in(['hadir', 'sakit', 'izin', 'dispensasi'])],
            'selfie' => ['required', 'image', 'mimes:jpeg,jpg,webp', 'max:1024'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric'],
        ];

        $polygon = $this->attendancePolygon();

        if ($polygon !== null && $statusSubmitted === 'hadir') {
            $rules['latitude'] = ['required', 'numeric', 'between:-90,90'];
            $rules['longitude'] = ['required', 'numeric', 'between:-180,180'];
            $rules['accuracy'] = ['required', 'numeric'];
        }

        $validated = $request->validate($rules, [
            'status.required' => 'Status kehadiran wajib dipilih.',
            'status.in' => 'Status kehadiran tidak valid.',
            'selfie.required' => 'Selfie/Foto bukti wajib diambil.',
            'selfie.image' => 'File harus berupa gambar.',
            'selfie.mimes' => 'Format foto harus JPEG atau WebP.',
            'selfie.max' => 'Ukuran foto maksimal 1 MB.',
            'latitude.required' => 'Lokasi GPS wajib diaktifkan untuk presensi Hadir.',
            'latitude.numeric' => 'Data GPS tidak valid.',
            'longitude.required' => 'Lokasi GPS wajib diaktifkan untuk presensi Hadir.',
            'longitude.numeric' => 'Data GPS tidak valid.',
            'accuracy.required' => 'Akurasi GPS wajib tersedia.',
            'accuracy.numeric' => 'Data GPS tidak valid.',
        ]);

        $latitude = null;
        $longitude = null;
        $accuracy = null;
        $distanceMeters = null;

        if ($polygon !== null && isset($validated['latitude'], $validated['longitude'], $validated['accuracy']) && $validated['latitude'] !== null) {
            $latitude = (float) $validated['latitude'];
            $longitude = (float) $validated['longitude'];
            $accuracy = (float) $validated['accuracy'];
            $locationCheck = $this->evaluateLocation($latitude, $longitude);

            if ($validated['status'] === 'hadir' && ! $locationCheck['allowed']) {
                return redirect()->route('siswa.absensi')->with('error', $locationCheck['message']);
            }

            $distanceMeters = $locationCheck['distance_meters'];
        }

        $selfiePath = $request->file('selfie')->store('attendance-selfies/'.$profile->nisn, 'public');

        $attendance->update([
            'status' => $validated['status'],
            'waktu_masuk' => $currentTime,
            'path_selfie' => $selfiePath,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'akurasi' => $accuracy,
            'jarak_meter' => $distanceMeters,
        ]);

        $statusLabels = [
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'dispensasi' => 'Dispensasi',
        ];

        return redirect()->route('siswa.absensi')->with('success', 'Absen berhasil: '.$statusLabels[$validated['status']].'.');
    }

    private function attendancePolygon(): ?array
    {
        return app(PemeriksaLokasiAbsensi::class)->poligon();
    }

    private function evaluateLocation(float $latitude, float $longitude): array
    {
        return app(PemeriksaLokasiAbsensi::class)->periksa($latitude, $longitude);
    }

    public function riwayat(Request $request): View
    {
        $student = Auth::user();
        $profile = $student->profilSiswa;
        $selectedMonth = $request->query('month', now()->format('Y-m'));

        if (! is_string($selectedMonth) || ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $selectedMonth)) {
            $selectedMonth = now()->format('Y-m');
        }

        $month = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();

        $attendances = $profile?->presensi()
            ->whereDate('tanggal', '>=', $month->toDateString())
            ->whereDate('tanggal', '<=', $month->copy()->endOfMonth()->toDateString())
            ->latest('tanggal')
            ->get() ?? collect();

        $monthLabel = $month->translatedFormat('F Y');

        $monthOptions = collect(range(0, 11))
            ->map(fn (int $back): Carbon => now()->startOfMonth()->subMonths($back))
            ->push($month)
            ->unique(fn (Carbon $date): string => $date->format('Y-m'))
            ->sortByDesc(fn (Carbon $date): string => $date->format('Y-m'))
            ->mapWithKeys(fn (Carbon $date): array => [$date->format('Y-m') => $date->translatedFormat('F Y')])
            ->all();

        return view('siswa.presensi.riwayat', compact('attendances', 'monthLabel', 'selectedMonth', 'monthOptions'));
    }
}
