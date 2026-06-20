<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LokasiAbsenRequest;
use App\Http\Requests\Admin\ToleransiLokasiRequest;
use App\Http\Requests\Admin\WaktuAbsenRequest;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PengaturanController extends Controller
{
    public function waktuAbsen(): Response
    {
        return Inertia::render('admin/pengaturan/WaktuAbsen', [
            'waktuMulai' => Pengaturan::ambil('waktu_mulai', '06:30'),
            'waktuSelesai' => Pengaturan::ambil('waktu_selesai', '07:00'),
            'toleransiTerlambat' => (int) Pengaturan::ambil('toleransi_terlambat', '15'),
        ]);
    }

    public function waktuAbsenSimpan(WaktuAbsenRequest $request): RedirectResponse
    {
        $waktuMulai = $this->formatWaktu($request->jam_mulai, $request->menit_mulai);
        $waktuSelesai = $this->formatWaktu($request->jam_selesai, $request->menit_selesai);
        $toleransiTerlambat = (int) $request->toleransi_terlambat;

        $durasiAbsenMenit = $this->menitDariWaktu($waktuSelesai) - $this->menitDariWaktu($waktuMulai);

        if ($waktuSelesai <= $waktuMulai) {
            return back()->withErrors(['jam_selesai' => 'Jam selesai harus setelah jam mulai.'])->withInput();
        }

        if ($toleransiTerlambat > $durasiAbsenMenit) {
            return back()->withErrors(['toleransi_terlambat' => 'Toleransi terlambat tidak boleh lebih besar dari durasi absen.'])->withInput();
        }

        $waktuTerlambat = $this->formatMenitSebagaiWaktu($this->menitDariWaktu($waktuSelesai) - $toleransiTerlambat);

        Pengaturan::simpan('waktu_mulai', $waktuMulai);
        Pengaturan::simpan('waktu_selesai', $waktuSelesai);
        Pengaturan::simpan('toleransi_terlambat', (string) $toleransiTerlambat);
        Pengaturan::simpan('waktu_terlambat', $waktuTerlambat);

        return redirect()->route('admin.pengaturan.waktu-absen')->with('toast', ['type' => 'success', 'message' => 'Konfigurasi waktu absen berhasil disimpan.']);
    }

    private function formatWaktu(string $jam, string $menit): string
    {
        return sprintf('%02d:%02d', (int) $jam, (int) $menit);
    }

    private function menitDariWaktu(string $waktu): int
    {
        [$jam, $menit] = array_map('intval', explode(':', $waktu));

        return ($jam * 60) + $menit;
    }

    private function formatMenitSebagaiWaktu(int $menit): string
    {
        $menit %= 1440;

        return sprintf('%02d:%02d', intdiv($menit, 60), $menit % 60);
    }

    public function lokasiAbsen(): Response
    {
        $geofenceData = Pengaturan::ambil('geofence_data');
        $toleransiMeter = (int) Pengaturan::ambil('toleransi_meter', '0');

        if (is_string($geofenceData) && $geofenceData !== '') {
            $decoded = json_decode($geofenceData, true);
            $geofenceData = is_array($decoded) ? $decoded : null;
        } else {
            $geofenceData = null;
        }

        return Inertia::render('admin/pengaturan/LokasiAbsen', [
            'geofenceData' => $geofenceData,
            'toleransiMeter' => $toleransiMeter,
        ]);
    }

    public function lokasiAbsenSimpan(LokasiAbsenRequest $request): RedirectResponse
    {
        $parser = new \App\Services\ParserKml;
        $result = $parser->parseFile($request->file('kml_file')->getPathname());

        if (isset($result['error'])) {
            return back()->withErrors(['kml_file' => $result['error']])->withInput();
        }

        Pengaturan::simpan('geofence_data', json_encode($result));

        if (! Pengaturan::ambil('toleransi_meter')) {
            Pengaturan::simpan('toleransi_meter', '0');
        }

        return redirect()->route('admin.pengaturan.lokasi-absen')->with('toast', ['type' => 'success', 'message' => 'Area absensi berhasil diimpor.']);
    }

    public function lokasiAbsenToleransi(ToleransiLokasiRequest $request): RedirectResponse
    {
        Pengaturan::simpan('toleransi_meter', (string) $request->toleransi_meter);

        return redirect()->route('admin.pengaturan.lokasi-absen')->with('toast', ['type' => 'success', 'message' => 'Toleransi jarak berhasil disimpan.']);
    }

    public function lokasiAbsenHapus(): RedirectResponse
    {
        Pengaturan::simpan('geofence_data', '');
        Pengaturan::simpan('toleransi_meter', '0');

        return redirect()->route('admin.pengaturan.lokasi-absen')->with('toast', ['type' => 'success', 'message' => 'Lokasi absen berhasil dihapus.']);
    }
}
