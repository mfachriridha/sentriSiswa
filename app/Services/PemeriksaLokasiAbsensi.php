<?php

namespace App\Services;

use App\Models\Pengaturan;

/**
 * Memutuskan apakah sebuah koordinat boleh dipakai absen.
 *
 * Dipisah dari controller supaya keputusan yang sama bisa dipanggil dari tempat
 * lain - misalnya perintah pengukur latensi - tanpa menyalin ulang aturannya.
 */
class PemeriksaLokasiAbsensi
{
    public function __construct(private readonly GeofenceValidator $geofence = new GeofenceValidator) {}

    /**
     * @return array{allowed: bool, status: string, distance_meters: float|int|null, message: string}
     */
    public function periksa(float $latitude, float $longitude): array
    {
        $poligon = $this->poligon();

        if ($poligon === null) {
            return [
                'allowed' => true,
                'status' => 'inactive',
                'distance_meters' => null,
                'message' => 'Area absensi belum dikonfigurasi.',
            ];
        }

        if ($this->geofence->isInsidePolygon($latitude, $longitude, $poligon)) {
            return [
                'allowed' => true,
                'status' => 'inside',
                'distance_meters' => 0,
                'message' => 'Lokasi Anda berada di dalam area absensi.',
            ];
        }

        $jarakMeter = $this->geofence->distanceToPolygonEdge($latitude, $longitude, $poligon);
        $toleransiMeter = (int) Pengaturan::get('attendance_tolerance_meters', '0');

        if ($jarakMeter <= $toleransiMeter) {
            return [
                'allowed' => true,
                'status' => 'tolerance',
                'distance_meters' => round($jarakMeter, 2),
                'message' => 'Lokasi Anda masih dalam toleransi akurasi GPS.',
            ];
        }

        return [
            'allowed' => false,
            'status' => 'outside',
            'distance_meters' => round($jarakMeter, 2),
            'message' => 'Lokasi Anda di luar area absensi dan tidak bisa absen.',
        ];
    }

    /**
     * Titik-titik batas area absensi, atau null kalau areanya belum dipasang.
     *
     * @return list<array{lat: float, lng: float}>|null
     */
    public function poligon(): ?array
    {
        $data = Pengaturan::get('attendance_geofence_data');

        if (! is_string($data) || $data === '') {
            return null;
        }

        $terbaca = json_decode($data, true);

        if (! is_array($terbaca) || ! isset($terbaca['coordinates']) || ! is_array($terbaca['coordinates']) || count($terbaca['coordinates']) < 3) {
            return null;
        }

        return $terbaca['coordinates'];
    }
}
