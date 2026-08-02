<?php

namespace Database\Seeders;

use App\Models\KategoriPengajuanPoin;
use Illuminate\Database\Seeder;

class KategoriPengajuanPoinSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Lomba Eksternal
            ['nama' => 'Juara 1 Lomba Tingkat Internasional', 'grup' => 'Lomba Eksternal', 'poin' => 40, 'urutan' => 1],
            ['nama' => 'Juara 2 Lomba Tingkat Internasional', 'grup' => 'Lomba Eksternal', 'poin' => 30, 'urutan' => 2],
            ['nama' => 'Juara 3 Lomba Tingkat Internasional', 'grup' => 'Lomba Eksternal', 'poin' => 20, 'urutan' => 3],
            ['nama' => 'Juara 1 Lomba Tingkat Nasional', 'grup' => 'Lomba Eksternal', 'poin' => 20, 'urutan' => 4],
            ['nama' => 'Juara 2 Lomba Tingkat Nasional', 'grup' => 'Lomba Eksternal', 'poin' => 15, 'urutan' => 5],
            ['nama' => 'Juara 3 Lomba Tingkat Nasional', 'grup' => 'Lomba Eksternal', 'poin' => 10, 'urutan' => 6],
            ['nama' => 'Juara 1 Lomba Tingkat Provinsi', 'grup' => 'Lomba Eksternal', 'poin' => 15, 'urutan' => 7],
            ['nama' => 'Juara 2 Lomba Tingkat Provinsi', 'grup' => 'Lomba Eksternal', 'poin' => 12, 'urutan' => 8],
            ['nama' => 'Juara 3 Lomba Tingkat Provinsi', 'grup' => 'Lomba Eksternal', 'poin' => 10, 'urutan' => 9],
            ['nama' => 'Juara 1 Lomba Tingkat Kabupaten/Kota', 'grup' => 'Lomba Eksternal', 'poin' => 12, 'urutan' => 10],
            ['nama' => 'Juara 2 Lomba Tingkat Kabupaten/Kota', 'grup' => 'Lomba Eksternal', 'poin' => 10, 'urutan' => 11],
            ['nama' => 'Juara 3 Lomba Tingkat Kabupaten/Kota', 'grup' => 'Lomba Eksternal', 'poin' => 8, 'urutan' => 12],
            ['nama' => 'Juara 1 Lomba Tingkat Kecamatan', 'grup' => 'Lomba Eksternal', 'poin' => 10, 'urutan' => 13],
            ['nama' => 'Juara 2 Lomba Tingkat Kecamatan', 'grup' => 'Lomba Eksternal', 'poin' => 8, 'urutan' => 14],
            ['nama' => 'Juara 3 Lomba Tingkat Kecamatan', 'grup' => 'Lomba Eksternal', 'poin' => 5, 'urutan' => 15],

            // Prestasi Internal Sekolah
            ['nama' => 'Juara 1 di Sekolah', 'grup' => 'Prestasi Internal Sekolah', 'poin' => 15, 'urutan' => 16],
            ['nama' => 'Juara 2 di Sekolah', 'grup' => 'Prestasi Internal Sekolah', 'poin' => 12, 'urutan' => 17],
            ['nama' => 'Juara 3 di Sekolah', 'grup' => 'Prestasi Internal Sekolah', 'poin' => 10, 'urutan' => 18],
            ['nama' => 'Juara 1 di Kelas', 'grup' => 'Prestasi Internal Sekolah', 'poin' => 10, 'urutan' => 19],
            ['nama' => 'Juara 2 di Kelas', 'grup' => 'Prestasi Internal Sekolah', 'poin' => 8, 'urutan' => 20],
            ['nama' => 'Juara 3 di Kelas', 'grup' => 'Prestasi Internal Sekolah', 'poin' => 5, 'urutan' => 21],

            // Kontribusi & Kedisiplinan
            ['nama' => 'Pengurus OSIS / MPK / Ekstrakurikuler Aktif', 'grup' => 'Kontribusi & Kedisiplinan', 'poin' => 10, 'urutan' => 22],
            ['nama' => 'Petugas Upacara / Event Resmi Sekolah', 'grup' => 'Kontribusi & Kedisiplinan', 'poin' => 5, 'urutan' => 23],
            ['nama' => 'Kedisiplinan Presensi 100%', 'grup' => 'Kontribusi & Kedisiplinan', 'poin' => 5, 'urutan' => 24],
        ];

        foreach ($categories as $cat) {
            KategoriPengajuanPoin::updateOrCreate(
                ['nama' => $cat['nama']],
                $cat
            );
        }
    }
}
