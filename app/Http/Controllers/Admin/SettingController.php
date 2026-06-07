<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\KmlParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function attendanceTime(): View
    {
        $startTime = Setting::get('attendance_start_time', '06:30');
        $endTime = Setting::get('attendance_end_time', '07:00');
        $lateToleranceMinutes = Setting::get('attendance_late_tolerance_minutes');

        if (! is_numeric($lateToleranceMinutes)) {
            $lateToleranceMinutes = max(0, $this->minutesFromTime($endTime) - $this->minutesFromTime(Setting::get('attendance_late_time', '07:00')));
        }

        return view('admin.settings.attendance-time', [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'lateToleranceMinutes' => (int) $lateToleranceMinutes,
        ]);
    }

    public function attendanceTimeUpdate(Request $request): RedirectResponse
    {
        $hours = array_map(fn (int $hour): string => sprintf('%02d', $hour), range(0, 23));
        $minutes = array_map(fn (int $minute): string => sprintf('%02d', $minute), range(0, 59));
        $lateToleranceOptions = array_map('strval', [0, 5, 10, 15, 20, 30, 45, 60, 90, 120]);

        $validated = $request->validate([
            'attendance_start_hour' => ['required', Rule::in($hours)],
            'attendance_start_minute' => ['required', Rule::in($minutes)],
            'attendance_end_hour' => ['required', Rule::in($hours)],
            'attendance_end_minute' => ['required', Rule::in($minutes)],
            'attendance_late_tolerance_minutes' => ['required', Rule::in($lateToleranceOptions)],
        ], [
            '*.required' => 'Jam dan menit wajib diisi.',
            '*.in' => 'Pilihan jam atau menit tidak valid.',
        ]);

        $startTime = $this->formatAttendanceTime($validated['attendance_start_hour'], $validated['attendance_start_minute']);
        $endTime = $this->formatAttendanceTime($validated['attendance_end_hour'], $validated['attendance_end_minute']);
        $lateToleranceMinutes = (int) $validated['attendance_late_tolerance_minutes'];
        $attendanceDurationMinutes = $this->minutesFromTime($endTime) - $this->minutesFromTime($startTime);

        if ($endTime <= $startTime) {
            return back()->withErrors([
                'attendance_end_hour' => 'Jam selesai harus setelah jam mulai.',
            ])->withInput();
        }

        if ($lateToleranceMinutes > $attendanceDurationMinutes) {
            return back()->withErrors([
                'attendance_late_tolerance_minutes' => 'Toleransi terlambat tidak boleh lebih besar dari durasi absen.',
            ])->withInput();
        }

        Setting::set('attendance_start_time', $startTime);
        Setting::set('attendance_end_time', $endTime);
        Setting::set('attendance_late_tolerance_minutes', (string) $lateToleranceMinutes);
        Setting::set('attendance_late_time', $this->formatMinutesAsTime($this->minutesFromTime($endTime) - $lateToleranceMinutes));

        return redirect()->route('admin.settings.attendance-time.index')->with('success', 'Konfigurasi waktu absen berhasil disimpan.');
    }

    private function formatAttendanceTime(int|string $hour, int|string $minute): string
    {
        return sprintf('%02d:%02d', (int) $hour, (int) $minute);
    }

    private function minutesFromTime(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));

        return ($hour * 60) + $minute;
    }

    private function formatMinutesAsTime(int $minutes): string
    {
        $minutes %= 1440;

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    public function attendanceLocation(): View
    {
        $geofenceData = Setting::get('attendance_geofence_data');
        $toleranceMeters = Setting::get('attendance_tolerance_meters', '0');

        if (is_string($geofenceData) && $geofenceData !== '') {
            $decoded = json_decode($geofenceData, true);
            if (is_array($decoded) && isset($decoded['coordinates'])) {
                $geofenceData = $decoded;
            } else {
                $geofenceData = null;
            }
        } else {
            $geofenceData = null;
        }

        return view('admin.settings.attendance-location', [
            'geofenceData' => $geofenceData,
            'toleranceMeters' => (int) $toleranceMeters,
        ]);
    }

    public function attendanceLocationUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kml_file' => ['required', 'file', 'mimetypes:text/xml,application/xml,application/vnd.google-earth.kml+xml', 'max:5120'],
        ], [
            'kml_file.required' => 'File KML wajib diunggah.',
            'kml_file.file' => 'File tidak valid.',
            'kml_file.mimetypes' => 'File harus berformat KML.',
            'kml_file.max' => 'File maksimal 5 MB.',
        ]);

        $parser = new KmlParser;
        $result = $parser->parseFile($request->file('kml_file')->getPathname());

        if (isset($result['error'])) {
            return back()->withErrors(['kml_file' => $result['error']])->withInput();
        }

        Setting::set('attendance_geofence_data', json_encode($result));

        if (! Setting::get('attendance_tolerance_meters')) {
            Setting::set('attendance_tolerance_meters', '0');
        }

        return redirect()->route('admin.settings.attendance-location.index')->with('success', 'Area absensi berhasil diimpor.');
    }

    public function attendanceLocationTolerance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tolerance_meters' => ['required', 'integer', 'min:0', 'max:500'],
        ], [
            'tolerance_meters.required' => 'Toleransi wajib diisi.',
            'tolerance_meters.integer' => 'Toleransi harus berupa angka.',
            'tolerance_meters.min' => 'Toleransi minimal 0 meter.',
            'tolerance_meters.max' => 'Toleransi maksimal 500 meter.',
        ]);

        Setting::set('attendance_tolerance_meters', (string) $validated['tolerance_meters']);

        return redirect()->route('admin.settings.attendance-location.index')->with('success', 'Toleransi jarak berhasil disimpan.');
    }

    public function attendanceLocationDelete(): RedirectResponse
    {
        Setting::set('attendance_geofence_data', '');
        Setting::set('attendance_tolerance_meters', '0');

        return redirect()->route('admin.settings.attendance-location.index')->with('success', 'Lokasi absen berhasil dihapus.');
    }

    public function whatsapp(): View
    {
        return view('admin.settings.whatsapp', [
            'config' => [
                'api_key' => Setting::get('wapisender_api_key', ''),
                'device_key' => Setting::get('wapisender_device_key', ''),
                'timeout_seconds' => Setting::get('wapisender_timeout_seconds', '60'),
                'delay_min_seconds' => Setting::get('wapisender_delay_min_seconds', '8'),
                'delay_max_seconds' => Setting::get('wapisender_delay_max_seconds', '15'),
                'is_priority' => Setting::get('wapisender_is_priority', '0'),
                'simulate_typing' => Setting::get('wapisender_simulate_typing', '0'),
            ],
        ]);
    }

    public function whatsappUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'wapisender_api_key' => ['nullable', 'string', 'max:255'],
            'wapisender_device_key' => ['nullable', 'string', 'max:100'],
            'wapisender_timeout_seconds' => ['required', 'integer', 'min:30', 'max:120'],
            'wapisender_delay_min_seconds' => ['required', 'integer', 'min:1', 'max:60'],
            'wapisender_delay_max_seconds' => ['required', 'integer', 'min:1', 'max:120'],
            'wapisender_is_priority' => ['nullable', 'boolean'],
            'wapisender_simulate_typing' => ['nullable', 'boolean'],
        ], [
            'wapisender_api_key.max' => 'API Key tidak boleh lebih dari 255 karakter.',
            'wapisender_device_key.max' => 'Device Key tidak boleh lebih dari 100 karakter.',
            'wapisender_timeout_seconds.required' => 'Timeout wajib diisi.',
            'wapisender_timeout_seconds.min' => 'Timeout minimal 30 detik.',
            'wapisender_timeout_seconds.max' => 'Timeout maksimal 120 detik.',
            'wapisender_delay_min_seconds.required' => 'Delay minimal wajib diisi.',
            'wapisender_delay_max_seconds.required' => 'Delay maksimal wajib diisi.',
        ]);

        if ((int) $validated['wapisender_delay_min_seconds'] > (int) $validated['wapisender_delay_max_seconds']) {
            return back()->withErrors([
                'wapisender_delay_max_seconds' => 'Delay maksimal harus lebih besar atau sama dengan delay minimal.',
            ])->withInput();
        }

        if (filled($validated['wapisender_api_key'] ?? null) || blank(Setting::get('wapisender_api_key', ''))) {
            Setting::set('wapisender_api_key', $validated['wapisender_api_key'] ?? '');
        }

        Setting::set('wapisender_device_key', $validated['wapisender_device_key'] ?? '');
        Setting::set('wapisender_timeout_seconds', (string) $validated['wapisender_timeout_seconds']);
        Setting::set('wapisender_delay_min_seconds', (string) $validated['wapisender_delay_min_seconds']);
        Setting::set('wapisender_delay_max_seconds', (string) $validated['wapisender_delay_max_seconds']);
        Setting::set('wapisender_is_priority', $request->boolean('wapisender_is_priority') ? '1' : '0');
        Setting::set('wapisender_simulate_typing', $request->boolean('wapisender_simulate_typing') ? '1' : '0');

        return redirect()->route('admin.settings.whatsapp.index')->with('success', 'Konfigurasi WhatsApp berhasil disimpan.');
    }

    public function whatsappTest(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'message' => ['required', 'string', 'max:500'],
        ], [
            'phone.required' => 'Nomor HP wajib diisi.',
            'message.required' => 'Pesan wajib diisi.',
            'message.max' => 'Pesan maksimal 500 karakter.',
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Test kirim WhatsApp belum diaktifkan. Konfigurasi Wapisender sudah bisa disimpan.',
        ], 409);
    }
}
