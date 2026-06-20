<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\KmlParser;
use App\Services\WhatsAppCloudApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function attendanceTime(): View
    {
        $startTime = Setting::get('attendance_start_time', '06:30');
        $endTime = Setting::get('attendance_end_time', '07:00');
        $lateToleranceMinutes = Setting::get('attendance_late_tolerance_minutes');
        $updatedAt = Setting::get('attendance_time_updated_at');

        if (! is_numeric($lateToleranceMinutes)) {
            $lateToleranceMinutes = max(0, $this->minutesFromTime($endTime) - $this->minutesFromTime(Setting::get('attendance_late_time', '07:00')));
        }

        return view('admin.pengaturan.attendance-time', [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'lateToleranceMinutes' => (int) $lateToleranceMinutes,
            'updatedAt' => $updatedAt,
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
        Setting::set('attendance_time_updated_at', now()->toDateTimeString());

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

        return view('admin.pengaturan.attendance-location', [
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
        return view('admin.pengaturan.whatsapp', [
            'config' => [
                'access_token' => Setting::get('whatsapp_cloud_access_token', ''),
                'phone_number_id' => Setting::get('whatsapp_cloud_phone_number_id', ''),
                'api_version' => Setting::get('whatsapp_cloud_api_version', 'v23.0'),
                'webhook_verify_token' => Setting::get('whatsapp_webhook_verify_token', ''),
            ],
        ]);
    }

    public function whatsappUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_cloud_access_token' => ['nullable', 'string', 'max:1000'],
            'whatsapp_cloud_phone_number_id' => ['nullable', 'string', 'max:100'],
            'whatsapp_cloud_api_version' => ['nullable', 'string', 'max:20', 'regex:/^v[0-9]+\.[0-9]+$/'],
            'whatsapp_webhook_verify_token' => ['nullable', 'string', 'min:16', 'max:255'],
            'clear_whatsapp_cloud_access_token' => ['nullable', 'boolean'],
            'clear_whatsapp_cloud_phone_number_id' => ['nullable', 'boolean'],
            'clear_whatsapp_webhook_verify_token' => ['nullable', 'boolean'],
        ], [
            'whatsapp_cloud_access_token.max' => 'Access Token tidak boleh lebih dari 1000 karakter.',
            'whatsapp_cloud_phone_number_id.max' => 'Phone Number ID tidak boleh lebih dari 100 karakter.',
            'whatsapp_cloud_api_version.regex' => 'Format Graph API Version harus seperti v23.0.',
            'whatsapp_webhook_verify_token.min' => 'Verify Token minimal 16 karakter.',
            'whatsapp_webhook_verify_token.max' => 'Verify Token tidak boleh lebih dari 255 karakter.',
        ]);

        if ($request->boolean('clear_whatsapp_cloud_access_token')) {
            Setting::set('whatsapp_cloud_access_token', '');
        } elseif (filled($validated['whatsapp_cloud_access_token'] ?? null) || blank(Setting::get('whatsapp_cloud_access_token', ''))) {
            Setting::set('whatsapp_cloud_access_token', $validated['whatsapp_cloud_access_token'] ?? '');
        }

        if ($request->boolean('clear_whatsapp_cloud_phone_number_id')) {
            Setting::set('whatsapp_cloud_phone_number_id', '');
        } elseif (filled($validated['whatsapp_cloud_phone_number_id'] ?? null) || blank(Setting::get('whatsapp_cloud_phone_number_id', ''))) {
            Setting::set('whatsapp_cloud_phone_number_id', $validated['whatsapp_cloud_phone_number_id'] ?? '');
        }

        Setting::set('whatsapp_cloud_api_version', $validated['whatsapp_cloud_api_version'] ?? Setting::get('whatsapp_cloud_api_version', 'v23.0'));

        if ($request->boolean('clear_whatsapp_webhook_verify_token')) {
            Setting::set('whatsapp_webhook_verify_token', '');
        } elseif (filled($validated['whatsapp_webhook_verify_token'] ?? null) || blank(Setting::get('whatsapp_webhook_verify_token', ''))) {
            Setting::set('whatsapp_webhook_verify_token', $validated['whatsapp_webhook_verify_token'] ?? '');
        }

        return redirect()->route('admin.settings.whatsapp.index')->with('success', 'Konfigurasi WhatsApp berhasil disimpan.');
    }

    public function whatsappTest(Request $request, WhatsAppCloudApiService $whatsapp): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'message' => ['required', 'string', 'max:500'],
        ], [
            'phone.required' => 'Nomor HP wajib diisi.',
            'message.required' => 'Pesan wajib diisi.',
            'message.max' => 'Pesan maksimal 500 karakter.',
        ]);

        $normalizedPhone = $whatsapp->normalizePhone($validated['phone']);
        $cooldownKey = 'whatsapp:test:cooldown:'.sha1($normalizedPhone);
        $availableAt = Cache::get($cooldownKey);

        if (is_numeric($availableAt) && now()->timestamp < (int) $availableAt) {
            return response()->json([
                'success' => false,
                'error' => 'Nomor ini baru saja dipakai untuk test. Tunggu sebelum mengirim ulang.',
                'retry_after' => max(1, (int) $availableAt - now()->timestamp),
            ], 429);
        }

        $availableAt = now()->addSeconds(WhatsAppCloudApiService::TEST_COOLDOWN_SECONDS)->timestamp;
        Cache::put($cooldownKey, $availableAt, WhatsAppCloudApiService::TEST_COOLDOWN_SECONDS);

        $result = $whatsapp->send($normalizedPhone, $validated['message'], [
            'timeout' => 60,
            'connect_timeout' => 10,
            'retries' => 0,
        ]);

        return response()->json([
            ...$result,
            'retry_after' => WhatsAppCloudApiService::TEST_COOLDOWN_SECONDS,
        ], $result['success'] ? 200 : 422);
    }
}
