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
    public function academicPeriod(): View
    {
        return view('admin.pengaturan.academic-period', [
            'academicYear' => Pengaturan::tahunAjaran(),
            'periodMode' => Pengaturan::modePeriode(),
            'semesterPeriod' => Pengaturan::semester(),
            'startDate' => Pengaturan::get('period_start_date', '2025-07-01'),
            'endDate' => Pengaturan::get('period_end_date', '2026-06-30'),
            'maxAlphaLimit' => Pengaturan::batasMaksimalAlpha(),
            'thresholds' => Pengaturan::ambangPeringatanAlpha(),
            'updatedAt' => Pengaturan::get('academic_period_updated_at'),
        ]);
    }

    public function academicPeriodUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year' => ['required', 'string', 'max:20', 'regex:/^\d{4}[\/\-]\d{4}$/'],
            'period_mode' => ['required', Rule::in(['tahun_ajaran', 'semester'])],
            'semester_period' => ['required', Rule::in(['ganjil', 'genap'])],
            'period_start_date' => ['required', 'date'],
            'period_end_date' => ['required', 'date', 'after_or_equal:period_start_date'],
            'max_alpha_limit' => ['required', 'integer', 'min:1', 'max:100'],
            'alpha_sp1_threshold' => ['required', 'integer', 'min:1', 'max:100'],
            'alpha_sp2_threshold' => ['required', 'integer', 'min:1', 'max:100'],
            'alpha_wakasis_threshold' => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'academic_year.required' => 'Tahun pelajaran wajib diisi.',
            'academic_year.regex' => 'Format Tahun Pelajaran harus berupa YYYY/YYYY atau YYYY-YYYY (contoh: 2025/2026).',
            'period_mode.required' => 'Mode periode wajib dipilih.',
            'semester_period.required' => 'Semester wajib dipilih.',
            'period_start_date.required' => 'Tanggal mulai periode wajib diisi.',
            'period_end_date.required' => 'Tanggal selesai periode wajib diisi.',
            'period_end_date.after_or_equal' => 'Tanggal selesai harus pada atau setelah tanggal mulai.',
            'max_alpha_limit.required' => 'Batas maksimal alpha wajib diisi.',
            'max_alpha_limit.min' => 'Batas maksimal alpha minimal 1.',
        ]);

        if ($validated['alpha_sp1_threshold'] > $validated['alpha_sp2_threshold'] || $validated['alpha_sp2_threshold'] > $validated['alpha_wakasis_threshold']) {
            return back()->withErrors([
                'alpha_sp1_threshold' => 'Urutan ambang batas harus logis: SP1 <= SP2 <= Wakasis.',
            ])->withInput();
        }

        Pengaturan::set('academic_year', $validated['academic_year']);
        Pengaturan::set('period_mode', $validated['period_mode']);
        Pengaturan::set('semester_period', $validated['semester_period']);
        Pengaturan::set('period_start_date', $validated['period_start_date']);
        Pengaturan::set('period_end_date', $validated['period_end_date']);
        Pengaturan::set('max_alpha_limit', (string) $validated['max_alpha_limit']);
        Pengaturan::set('alpha_sp1_threshold', (string) $validated['alpha_sp1_threshold']);
        Pengaturan::set('alpha_sp2_threshold', (string) $validated['alpha_sp2_threshold']);
        Pengaturan::set('alpha_wakasis_threshold', (string) $validated['alpha_wakasis_threshold']);
        Pengaturan::set('academic_period_updated_at', now()->toDateTimeString());

        return redirect()->route('admin.pengaturan.periode-absen.index')->with('success', 'Konfigurasi periode dan batas alpha berhasil disimpan.');
    }

    public function attendanceTime(): View
    {
        $startTime = Pengaturan::get('attendance_start_time', '06:30');
        $endTime = Pengaturan::get('attendance_end_time', '07:00');
        $updatedAt = Pengaturan::get('attendance_time_updated_at');

        return view('admin.pengaturan.attendance-time', [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'updatedAt' => $updatedAt,
            'activeDays' => Pengaturan::hariAbsen(),
            'dayNames' => Pengaturan::namaHari(),
            'activeDaysLabel' => Pengaturan::labelHariAbsen(),
        ]);
    }

    public function attendanceTimeUpdate(Request $request): RedirectResponse
    {
        $hours = array_map(fn (int $hour): string => sprintf('%02d', $hour), range(0, 23));
        $minutes = array_map(fn (int $minute): string => sprintf('%02d', $minute), range(0, 59));

        $validated = $request->validate([
            'attendance_start_hour' => ['required', Rule::in($hours)],
            'attendance_start_minute' => ['required', Rule::in($minutes)],
            'attendance_end_hour' => ['required', Rule::in($hours)],
            'attendance_end_minute' => ['required', Rule::in($minutes)],
            'attendance_active_days' => ['required', 'array', 'min:1'],
            'attendance_active_days.*' => ['integer', 'between:1,7'],
        ], [
            'attendance_start_hour.required' => 'Jam dan menit wajib diisi.',
            'attendance_start_minute.required' => 'Jam dan menit wajib diisi.',
            'attendance_end_hour.required' => 'Jam dan menit wajib diisi.',
            'attendance_end_minute.required' => 'Jam dan menit wajib diisi.',
            'attendance_active_days.required' => 'Pilih minimal satu hari aktif absensi.',
            'attendance_active_days.min' => 'Pilih minimal satu hari aktif absensi.',
            '*.in' => 'Pilihan jam atau menit tidak valid.',
        ]);

        $startTime = $this->formatAttendanceTime($validated['attendance_start_hour'], $validated['attendance_start_minute']);
        $endTime = $this->formatAttendanceTime($validated['attendance_end_hour'], $validated['attendance_end_minute']);

        if ($endTime <= $startTime) {
            return back()->withErrors([
                'attendance_end_hour' => 'Jam selesai harus setelah jam mulai.',
            ])->withInput();
        }

        $activeDays = collect($validated['attendance_active_days'])
            ->map(fn ($day): int => (int) $day)
            ->unique()
            ->sort()
            ->values();

        Pengaturan::set('attendance_start_time', $startTime);
        Pengaturan::set('attendance_end_time', $endTime);
        Pengaturan::set('attendance_active_days', $activeDays->implode(','));
        Pengaturan::set('attendance_time_updated_at', now()->toDateTimeString());

        return redirect()->route('admin.pengaturan.waktu-absen.index')->with('success', 'Konfigurasi waktu absen berhasil disimpan.');
    }

    private function formatAttendanceTime(int|string $hour, int|string $minute): string
    {
        return sprintf('%02d:%02d', (int) $hour, (int) $minute);
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

    public function whatsapp(FonnteService $whatsapp): View
    {
        $token = Pengaturan::get('fonnte_token', '');

        return view('admin.pengaturan.whatsapp', [
            'config' => ['token' => $token],
            // Kondisi perangkat ditanyakan langsung ke Fonnte: token yang benar
            // pun tidak menjamin pesan terkirim kalau perangkatnya terputus,
            // kuotanya habis, atau masa aktifnya lewat.
            'perangkat' => filled($token) ? $whatsapp->deviceProfile() : null,
        ]);
    }

    public function whatsappUpdate(Request $request, FonnteService $whatsapp): RedirectResponse
    {
        $validated = $request->validate([
            'fonnte_token' => ['nullable', 'string', 'max:255'],
            'clear_fonnte_token' => ['nullable', 'boolean'],
        ], [
            'fonnte_token.max' => 'Token Fonnte tidak boleh lebih dari 255 karakter.',
        ]);

        if ($request->boolean('clear_fonnte_token')) {
            Pengaturan::set('fonnte_token', '');

            return redirect()->route('admin.pengaturan.whatsapp.index')->with('success', 'Token Fonnte berhasil dihapus.');
        }

        $tokenBaru = $validated['fonnte_token'] ?? '';

        if (blank($tokenBaru) && filled(Pengaturan::get('fonnte_token', ''))) {
            return redirect()->route('admin.pengaturan.whatsapp.index')->with('success', 'Konfigurasi WhatsApp berhasil disimpan.');
        }

        // Token tidak punya pola yang bisa diperiksa sendiri, jadi diuji langsung
        // ke Fonnte. Token ngawur yang lolos tersimpan baru ketahuan salah saat
        // laporan absensi gagal terkirim - jauh setelah admin meninggalkan
        // halaman ini.
        $hasil = $whatsapp->deviceProfile($tokenBaru);

        if ($hasil['success']) {
            Pengaturan::set('fonnte_token', $tokenBaru);

            return redirect()->route('admin.pengaturan.whatsapp.index')
                ->with('success', 'Token Fonnte berhasil disimpan dan sudah diverifikasi.');
        }

        // Gangguan jaringan bukan salah tokennya, jadi admin tidak dikunci karena
        // itu: tokennya tetap disimpan, tapi diberi tahu bahwa belum terverifikasi.
        if (self::gagalKarenaJaringan($hasil['error'] ?? '')) {
            Pengaturan::set('fonnte_token', $tokenBaru);

            return redirect()->route('admin.pengaturan.whatsapp.index')
                ->with('warning', 'Token disimpan, tapi belum bisa diverifikasi: '.$hasil['error'].' Cek lagi nanti lewat halaman ini.');
        }

        return redirect()->route('admin.pengaturan.whatsapp.index')
            ->withErrors(['fonnte_token' => 'Token ditolak Fonnte: '.$hasil['error'].' Token lama tetap dipakai.']);
    }

    /** Bedakan "token memang salah" dari "Fonnte-nya yang tidak bisa dihubungi". */
    private static function gagalKarenaJaringan(string $pesan): bool
    {
        return str_contains($pesan, 'timeout')
            || str_contains($pesan, 'tidak bisa dijangkau')
            || str_contains($pesan, 'kesalahan saat menghubungi')
            || str_contains($pesan, 'respons yang tidak valid');
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

        if (! $whatsapp->isValidPhoneFormat($normalizedPhone)) {
            return response()->json([
                'success' => false,
                'error' => 'Nomor HP tidak valid: '.$normalizedPhone,
            ], 422);
        }

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
