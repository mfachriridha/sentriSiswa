<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\PesanWhatsapp;
use App\Services\FonnteService;
use App\Services\KmlParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function attendanceTime(): View
    {
        $startTime = Pengaturan::get('attendance_start_time', '06:30');
        $endTime = Pengaturan::get('attendance_end_time', '07:00');
        $lateToleranceMinutes = Pengaturan::get('attendance_late_tolerance_minutes');
        $updatedAt = Pengaturan::get('attendance_time_updated_at');

        if (! is_numeric($lateToleranceMinutes)) {
            $lateToleranceMinutes = max(0, $this->minutesFromTime($endTime) - $this->minutesFromTime(Pengaturan::get('attendance_late_time', '07:00')));
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

        Pengaturan::set('attendance_start_time', $startTime);
        Pengaturan::set('attendance_end_time', $endTime);
        Pengaturan::set('attendance_late_tolerance_minutes', (string) $lateToleranceMinutes);
        Pengaturan::set('attendance_late_time', $this->formatMinutesAsTime($this->minutesFromTime($endTime) - $lateToleranceMinutes));
        Pengaturan::set('attendance_time_updated_at', now()->toDateTimeString());

        return redirect()->route('admin.pengaturan.waktu-absen.index')->with('success', 'Konfigurasi waktu absen berhasil disimpan.');
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
        $geofenceData = Pengaturan::get('attendance_geofence_data');
        $toleranceMeters = Pengaturan::get('attendance_tolerance_meters', '0');

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

        Pengaturan::set('attendance_geofence_data', json_encode($result));

        if (! Pengaturan::get('attendance_tolerance_meters')) {
            Pengaturan::set('attendance_tolerance_meters', '0');
        }

        return redirect()->route('admin.pengaturan.lokasi-absen.index')->with('success', 'Area absensi berhasil diimpor.');
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

        Pengaturan::set('attendance_tolerance_meters', (string) $validated['tolerance_meters']);

        return redirect()->route('admin.pengaturan.lokasi-absen.index')->with('success', 'Toleransi jarak berhasil disimpan.');
    }

    public function attendanceLocationDelete(): RedirectResponse
    {
        Pengaturan::set('attendance_geofence_data', '');
        Pengaturan::set('attendance_tolerance_meters', '0');

        return redirect()->route('admin.pengaturan.lokasi-absen.index')->with('success', 'Lokasi absen berhasil dihapus.');
    }

    public function whatsapp(): View
    {
        return view('admin.pengaturan.whatsapp', [
            'config' => [
                'token' => Pengaturan::get('fonnte_token', ''),
            ],
        ]);
    }

    public function whatsappUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fonnte_token' => ['nullable', 'string', 'max:255'],
            'clear_fonnte_token' => ['nullable', 'boolean'],
        ], [
            'fonnte_token.max' => 'Token Fonnte tidak boleh lebih dari 255 karakter.',
        ]);

        if ($request->boolean('clear_fonnte_token')) {
            Pengaturan::set('fonnte_token', '');
        } elseif (filled($validated['fonnte_token'] ?? null) || blank(Pengaturan::get('fonnte_token', ''))) {
            Pengaturan::set('fonnte_token', $validated['fonnte_token'] ?? '');
        }

        return redirect()->route('admin.pengaturan.whatsapp.index')->with('success', 'Konfigurasi WhatsApp berhasil disimpan.');
    }

    public function whatsappTest(Request $request, FonnteService $whatsapp): JsonResponse
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

        $availableAt = now()->addSeconds(FonnteService::TEST_COOLDOWN_SECONDS)->timestamp;
        Cache::put($cooldownKey, $availableAt, FonnteService::TEST_COOLDOWN_SECONDS);

        $result = $whatsapp->send($normalizedPhone, $validated['message'], [
            'timeout' => 60,
            'connect_timeout' => 10,
            'retries' => 0,
        ]);

        PesanWhatsapp::create([
            'kelas_id' => null,
            'telepon_penerima' => $normalizedPhone,
            'nama_penerima' => 'Test (Pesan Uji)',
            'tipe_pesan' => 'test',
            'isi_pesan' => $validated['message'],
            'status' => $result['success'] ? 'sent' : 'failed',
            'respons' => json_encode($result),
            'dikirim_pada' => now(),
        ]);

        return response()->json([
            ...$result,
            'retry_after' => FonnteService::TEST_COOLDOWN_SECONDS,
        ], $result['success'] ? 200 : 422);
    }
}
