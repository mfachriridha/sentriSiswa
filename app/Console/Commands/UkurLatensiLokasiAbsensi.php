<?php

namespace App\Console\Commands;

use App\Services\PemeriksaLokasiAbsensi;
use Illuminate\Console\Command;

/**
 * Mengukur berapa lama sistem memverifikasi satu koordinat absensi.
 *
 * Yang diukur hanya kerja sistemnya: membaca area absensi, memeriksa titik ada
 * di dalam poligon atau tidak, lalu menghitung jarak ke batas terdekat. Waktu
 * ponsel mendapatkan sinyal GPS tidak ikut dihitung - itu urusan perangkat dan
 * satelit, bukan kecepatan sistem.
 *
 * Jalankan: php artisan absensi:ukur-latensi --ulangi=100
 */
class UkurLatensiLokasiAbsensi extends Command
{
    protected $signature = 'absensi:ukur-latensi
        {--ulangi=100 : Berapa kali tiap skenario diukur}';

    protected $description = 'Mengukur waktu verifikasi koordinat absensi (min, rata-rata, p95, maksimum)';

    public function handle(PemeriksaLokasiAbsensi $pemeriksa): int
    {
        $poligon = $pemeriksa->poligon();

        if ($poligon === null) {
            $this->error('Area absensi belum dikonfigurasi. Unggah dulu berkas KML di Pengaturan > Lokasi Absen.');
            $this->line('Tanpa area yang asli, angka pengukurannya tidak mewakili keadaan sebenarnya.');

            return self::FAILURE;
        }

        $ulangi = max(1, (int) $this->option('ulangi'));
        $skenario = $this->skenario($poligon);

        $this->info('Pengukuran waktu verifikasi koordinat absensi');
        $this->line('  Titik batas area : '.count($poligon).' titik');
        $this->line('  Pengulangan      : '.$ulangi.'x per skenario');
        $this->newLine();

        $baris = [];

        foreach ($skenario as $nama => [$lat, $lng]) {
            // Sekali jalan tanpa dicatat, supaya biaya pemanasan (autoload,
            // cache pengaturan) tidak terhitung sebagai latensi.
            $pemeriksa->periksa($lat, $lng);

            $waktu = [];

            for ($i = 0; $i < $ulangi; $i++) {
                $mulai = hrtime(true);
                $hasil = $pemeriksa->periksa($lat, $lng);
                $waktu[] = (hrtime(true) - $mulai) / 1_000_000;
            }

            sort($waktu);

            $baris[] = [
                $nama,
                $hasil['status'],
                $this->ms(min($waktu)),
                $this->ms(array_sum($waktu) / count($waktu)),
                $this->ms($this->persentil($waktu, 95)),
                $this->ms(max($waktu)),
            ];
        }

        $this->table(['Skenario', 'Hasil', 'Min', 'Rata-rata', 'p95', 'Maks'], $baris);
        $this->line('Satuan milidetik. p95 = 95% pengukuran lebih cepat dari angka ini.');
        $this->newLine();
        $this->line('Catatan: yang diukur waktu sistem memverifikasi koordinat, belum termasuk');
        $this->line('waktu jaringan dan waktu ponsel mendapatkan sinyal GPS.');

        return self::SUCCESS;
    }

    /**
     * Tiga keadaan yang benar-benar ditemui siswa, dihitung dari area absensi
     * yang sedang dipakai sekolah.
     *
     * @param  list<array{lat: float, lng: float}>  $poligon
     * @return array<string, array{0: float, 1: float}>
     */
    private function skenario(array $poligon): array
    {
        $tengahLat = array_sum(array_column($poligon, 'lat')) / count($poligon);
        $tengahLng = array_sum(array_column($poligon, 'lng')) / count($poligon);

        // Jarak titik terjauh dari tengah. Titik yang lebih jauh dari ini pasti
        // di luar area, bentuk poligonnya seperti apa pun - jadi skenario "di
        // luar" tidak bisa meleset jadi "di dalam" hanya karena areanya besar.
        $jari = 0.0;

        foreach ($poligon as $titik) {
            $jari = max($jari, hypot($titik['lat'] - $tengahLat, $titik['lng'] - $tengahLng));
        }

        return [
            // Di dalam: jalur tercepat, berhenti begitu ketahuan di dalam tanpa
            // perlu menghitung jarak ke sisi mana pun.
            'Di dalam area' => [$tengahLat, $tengahLng],
            // Tepat di luar: memaksa perhitungan jarak ke seluruh sisi poligon.
            'Tepat di luar batas' => [$tengahLat + $jari * 1.05, $tengahLng],
            // Jauh di luar: seperti siswa yang mencoba absen dari rumah.
            'Jauh di luar area' => [$tengahLat + $jari * 10, $tengahLng],
        ];
    }

    /** @param list<float> $terurut */
    private function persentil(array $terurut, int $persen): float
    {
        $posisi = (int) ceil(($persen / 100) * count($terurut)) - 1;

        return $terurut[max(0, $posisi)];
    }

    private function ms(float $nilai): string
    {
        return number_format($nilai, 3).' ms';
    }
}
