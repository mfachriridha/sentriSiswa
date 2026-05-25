<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\GeofenceValidator;
use Illuminate\Http\JsonResponse;
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

        $now = now();
        $currentTime = $now->format('H:i');
        $currentTimeLabel = $now->format('H:i');
        $canCheckIn = $currentTime >= $startTime && $currentTime <= $endTime;

        $geofenceData = Setting::get('attendance_geofence_data');
        $geofenceActive = false;
        if (is_string($geofenceData) && $geofenceData !== '') {
            $decoded = json_decode($geofenceData, true);
            if (is_array($decoded) && isset($decoded['coordinates']) && count($decoded['coordinates']) >= 3) {
                $geofenceActive = true;
            }
        }

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
            'currentYear',
            'geofenceActive',
            'currentTimeLabel'
        ));
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
        $lateToleranceMinutes = $this->lateToleranceMinutes($endTime);
        $currentTime = now()->format('H:i');

        if ($currentTime < $startTime || $currentTime > $endTime) {
            return redirect()->route('siswa.absensi')->with('error', 'Waktu absen sudah lewat atau belum dimulai.');
        }

        $rules = [
            'selfie' => ['required', 'image', 'mimes:jpeg,jpg,webp', 'max:300'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric'],
        ];

        $polygon = $this->attendancePolygon();

        if ($polygon !== null) {
            $rules['latitude'] = ['required', 'numeric', 'between:-90,90'];
            $rules['longitude'] = ['required', 'numeric', 'between:-180,180'];
            $rules['accuracy'] = ['required', 'numeric'];
        }

        $validated = $request->validate($rules, [
            'selfie.required' => 'Selfie wajib diambil.',
            'selfie.image' => 'File harus berupa gambar.',
            'selfie.mimes' => 'Format foto harus JPEG atau WebP.',
            'selfie.max' => 'Ukuran foto maksimal 300 KB.',
            'latitude.required' => 'Lokasi GPS wajib diaktifkan untuk absen.',
            'latitude.numeric' => 'Data GPS tidak valid.',
            'longitude.required' => 'Lokasi GPS wajib diaktifkan untuk absen.',
            'longitude.numeric' => 'Data GPS tidak valid.',
            'accuracy.required' => 'Akurasi GPS wajib tersedia.',
            'accuracy.numeric' => 'Data GPS tidak valid.',
        ]);

        $latitude = null;
        $longitude = null;
        $accuracy = null;
        $distanceMeters = null;

        if ($polygon !== null) {
            $latitude = (float) $validated['latitude'];
            $longitude = (float) $validated['longitude'];
            $accuracy = (float) $validated['accuracy'];
            $locationCheck = $this->evaluateLocation($latitude, $longitude);

            if (! $locationCheck['allowed']) {
                return redirect()->route('siswa.absensi')->with('error', $locationCheck['message']);
            }

            $distanceMeters = $locationCheck['distance_meters'];
        }

        $lateThresholdMinutes = $this->minutesFromTime($endTime) - $lateToleranceMinutes;
        $status = ($this->minutesFromTime($currentTime) > $lateThresholdMinutes) ? 'terlambat' : 'hadir';
        $selfiePath = $request->file('selfie')->store('attendance-selfies/'.$profile->id, 'public');

        $profile->attendances()->create([
            'date' => $today,
            'status' => $status,
            'check_in_time' => $currentTime,
            'selfie_path' => $selfiePath,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $accuracy,
            'distance_meters' => $distanceMeters,
        ]);

        return redirect()->route('siswa.absensi')->with('success', $status === 'terlambat' ? 'Absen tercatat: Terlambat.' : 'Absen berhasil: Hadir.');
    }

    private function minutesFromTime(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));

        return ($hour * 60) + $minute;
    }

    private function lateToleranceMinutes(string $endTime): int
    {
        $lateToleranceMinutes = Setting::get('attendance_late_tolerance_minutes');

        if (is_numeric($lateToleranceMinutes)) {
            return (int) $lateToleranceMinutes;
        }

        return max(0, $this->minutesFromTime($endTime) - $this->minutesFromTime(Setting::get('attendance_late_time', '07:00')));
    }

    /**
     * @return list<array{lat: float, lng: float}>|null
     */
    private function attendancePolygon(): ?array
    {
        $geofenceData = Setting::get('attendance_geofence_data');

        if (! is_string($geofenceData) || $geofenceData === '') {
            return null;
        }

        $decoded = json_decode($geofenceData, true);

        if (! is_array($decoded) || ! isset($decoded['coordinates']) || ! is_array($decoded['coordinates']) || count($decoded['coordinates']) < 3) {
            return null;
        }

        return $decoded['coordinates'];
    }

    /**
     * @return array{allowed: bool, status: string, distance_meters: float|null, message: string}
     */
    private function evaluateLocation(float $latitude, float $longitude): array
    {
        $polygon = $this->attendancePolygon();

        if ($polygon === null) {
            return [
                'allowed' => true,
                'status' => 'inactive',
                'distance_meters' => null,
                'message' => 'Area absensi belum dikonfigurasi.',
            ];
        }

        $validator = new GeofenceValidator;

        if ($validator->isInsidePolygon($latitude, $longitude, $polygon)) {
            return [
                'allowed' => true,
                'status' => 'inside',
                'distance_meters' => 0,
                'message' => 'Lokasi Anda berada di dalam area absensi.',
            ];
        }

        $distanceMeters = $validator->distanceToPolygonEdge($latitude, $longitude, $polygon);
        $toleranceMeters = (int) Setting::get('attendance_tolerance_meters', '0');

        if ($distanceMeters <= $toleranceMeters) {
            return [
                'allowed' => true,
                'status' => 'tolerance',
                'distance_meters' => round($distanceMeters, 2),
                'message' => 'Lokasi Anda masih dalam toleransi akurasi GPS.',
            ];
        }

        return [
            'allowed' => false,
            'status' => 'outside',
            'distance_meters' => round($distanceMeters, 2),
            'message' => 'Lokasi Anda di luar area absensi dan tidak bisa absen.',
        ];
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
