<?php

namespace App\Console\Commands;

use App\Models\Pengaturan;
use Illuminate\Console\Command;

class AllowDesktopAttendanceCommand extends Command
{
    /**
     * Nama dan tanda tangan perintah console.
     *
     * @var string
     */
    protected $signature = 'absensi:allow-desktop
                            {--disable : Matikan izin presensi dari laptop/desktop dan kembalikan ke mode normal}';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Izinkan presensi Hadir menggunakan Laptop / PC Desktop untuk kebutuhan testing (DEV mode)';

    /**
     * Eksekusi perintah console.
     */
    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Command ini tidak boleh dijalankan di production!');

            return self::FAILURE;
        }

        if ($this->option('disable')) {
            Pengaturan::set('attendance_allow_desktop', '0');

            $this->info('🔒 Presensi Hadir dari Laptop / Desktop / PC DITUTUP.');
            $this->line('<fg=gray>   Presensi Hadir kembali mewajibkan peranti mobile (HP / Smartphone).</>');

            return self::SUCCESS;
        }

        // Aktifkan mode desktop
        Pengaturan::set('attendance_allow_desktop', '1');

        // Pasang polygon dummy jika geofence belum di-set agar presensi Hadir tidak terblokir lokasi saat testing
        $geofenceData = Pengaturan::get('attendance_geofence_data');
        $dummySet = false;
        if (! is_string($geofenceData) || blank($geofenceData)) {
            $dummyPolygon = [
                'coordinates' => [
                    ['lat' => -10.0, 'lng' => 95.0],
                    ['lat' => -10.0, 'lng' => 141.0],
                    ['lat' => 6.0, 'lng' => 141.0],
                    ['lat' => 6.0, 'lng' => 95.0],
                ],
            ];
            Pengaturan::set('attendance_geofence_data', json_encode($dummyPolygon));
            $dummySet = true;
        }

        $this->info('✅ Presensi Hadir dari Laptop / PC Desktop SEKARANG DIIZINKAN (Mode Testing).');
        $this->line('<fg=gray>   - Batasan HP/Smartphone: NONAKTIF</>');

        if ($dummySet) {
            $this->line('<fg=gray>   - Area Lokasi GPS: Polygon dummy wilayah Indonesia terpasang otomatis</>');
        }

        $this->newLine();
        $this->line('Untuk mengembalikan ke mode normal: <fg=yellow>php artisan absensi:allow-desktop --disable</>');

        return self::SUCCESS;
    }
}
