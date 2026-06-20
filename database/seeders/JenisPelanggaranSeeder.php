<?php

namespace Database\Seeders;

use App\Models\JenisPelanggaran;
use Illuminate\Database\Seeder;

class JenisPelanggaranSeeder extends Seeder
{
    public function run(): void
    {
        $jenis = [
            [JenisPelanggaran::KATEGORI_RINGAN, 'Tidak Hadir', 5],
            [JenisPelanggaran::KATEGORI_RINGAN, 'Penampilan tidak rapi', 5],
            [JenisPelanggaran::KATEGORI_RINGAN, 'Ber make up', 5],
            [JenisPelanggaran::KATEGORI_RINGAN, 'Atribut tidak lengkap', 5],
            [JenisPelanggaran::KATEGORI_RINGAN, 'Memakai Sandal di sekolah', 10],
            [JenisPelanggaran::KATEGORI_RINGAN, 'Mencontek', 10],
            [JenisPelanggaran::KATEGORI_RINGAN, 'Salah Kostum', 15],
            [JenisPelanggaran::KATEGORI_RINGAN, 'Memakai perhiasan', 15],
            [JenisPelanggaran::KATEGORI_RINGAN, 'Bicara tidak sopan', 15],
            [JenisPelanggaran::KATEGORI_RINGAN, 'Di kantin jam pelajaran', 15],
            [JenisPelanggaran::KATEGORI_RINGAN, 'Berpakaian tidak standar', 25],
            [JenisPelanggaran::KATEGORI_RINGAN, 'Mendapat SP 1', 25],
            [JenisPelanggaran::KATEGORI_SEDANG, 'Bolos dari sekolah', 30],
            [JenisPelanggaran::KATEGORI_SEDANG, 'Melakukan penghinaan', 40],
            [JenisPelanggaran::KATEGORI_SEDANG, 'Anggota tubuh bertato', 40],
            [JenisPelanggaran::KATEGORI_SEDANG, 'Mencoret sarana sekolah', 40],
            [JenisPelanggaran::KATEGORI_SEDANG, 'Merusak sarana sekolah', 40],
            [JenisPelanggaran::KATEGORI_SEDANG, 'Mewarnai rambut', 40],
            [JenisPelanggaran::KATEGORI_SEDANG, 'Mencuri di sekolah', 45],
            [JenisPelanggaran::KATEGORI_SEDANG, 'Memalsukan dokumen', 50],
            [JenisPelanggaran::KATEGORI_SEDANG, 'Berlaku Rasis di sekolah', 50],
            [JenisPelanggaran::KATEGORI_SEDANG, 'Merokok di sekolah dan luar dengan seragam sekolah', 50],
            [JenisPelanggaran::KATEGORI_SEDANG, 'Mendapat SP ke 2', 50],
            [JenisPelanggaran::KATEGORI_BERAT, 'Bersekongkol melakukan kejahatan', 60],
            [JenisPelanggaran::KATEGORI_BERAT, 'Mesum di sekolah', 75],
            [JenisPelanggaran::KATEGORI_BERAT, 'Melakukan pelecehan', 75],
            [JenisPelanggaran::KATEGORI_BERAT, 'Membawa Sajam', 75],
            [JenisPelanggaran::KATEGORI_BERAT, 'Melakukan Bulli', 75],
            [JenisPelanggaran::KATEGORI_BERAT, 'Terlibat Uang Palsu', 75],
            [JenisPelanggaran::KATEGORI_BERAT, 'Melakukan pemalakan', 75],
            [JenisPelanggaran::KATEGORI_AMAT_BERAT, 'Terlibat balapan liar', 85],
            [JenisPelanggaran::KATEGORI_AMAT_BERAT, 'Terlibat Gangster', 100],
            [JenisPelanggaran::KATEGORI_AMAT_BERAT, 'Berkelahi di sekolah dan di luar', 100],
            [JenisPelanggaran::KATEGORI_AMAT_BERAT, 'Terlibat Narkoba', 100],
            [JenisPelanggaran::KATEGORI_AMAT_BERAT, 'Terlibat Pornografi', 100],
            [JenisPelanggaran::KATEGORI_AMAT_BERAT, 'Terlibat Tawuran', 100],
            [JenisPelanggaran::KATEGORI_AMAT_BERAT, 'Mendapat SP ke 3', 100],
        ];

        JenisPelanggaran::upsert(
            collect($jenis)->map(fn (array $item): array => [
                'kategori' => $item[0],
                'nama' => $item[1],
                'poin' => $item[2],
                'deskripsi' => null,
                'aktif' => true,
            ])->all(),
            uniqueBy: ['nama'],
            update: ['kategori', 'poin', 'deskripsi', 'aktif'],
        );
    }
}
