<?php

namespace Database\Seeders;

use App\Models\JenisPelanggaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ViolationTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $violationTypes = [
            [JenisPelanggaran::CATEGORY_LIGHT, 'Penampilan tidak rapi', 5],
            [JenisPelanggaran::CATEGORY_LIGHT, 'Ber make up', 5],
            [JenisPelanggaran::CATEGORY_LIGHT, 'Atribut tidak lengkap', 5],
            [JenisPelanggaran::CATEGORY_LIGHT, 'Memakai Sandal di sekolah', 10],
            [JenisPelanggaran::CATEGORY_LIGHT, 'Mencontek', 10],
            [JenisPelanggaran::CATEGORY_LIGHT, 'Salah Kostum', 15],
            [JenisPelanggaran::CATEGORY_LIGHT, 'Memakai perhiasan', 15],
            [JenisPelanggaran::CATEGORY_LIGHT, 'Bicara tidak sopan', 15],
            [JenisPelanggaran::CATEGORY_LIGHT, 'Di kantin jam pelajaran', 15],
            [JenisPelanggaran::CATEGORY_LIGHT, 'Berpakaian tidak standar', 25],
            [JenisPelanggaran::CATEGORY_LIGHT, 'Mendapat SP 1', 25],
            [JenisPelanggaran::CATEGORY_MEDIUM, 'Bolos dari sekolah', 30],
            [JenisPelanggaran::CATEGORY_MEDIUM, 'Melakukan penghinaan', 40],
            [JenisPelanggaran::CATEGORY_MEDIUM, 'Anggota tubuh bertato', 40],
            [JenisPelanggaran::CATEGORY_MEDIUM, 'Mencoret sarana sekolah', 40],
            [JenisPelanggaran::CATEGORY_MEDIUM, 'Merusak sarana sekolah', 40],
            [JenisPelanggaran::CATEGORY_MEDIUM, 'Merwarna rambut', 40],
            [JenisPelanggaran::CATEGORY_MEDIUM, 'Mencuri di sekolah', 45],
            [JenisPelanggaran::CATEGORY_MEDIUM, 'Memalsukan dokumen', 50],
            [JenisPelanggaran::CATEGORY_MEDIUM, 'Berlaku Rasis di sekolah', 50],
            [JenisPelanggaran::CATEGORY_MEDIUM, 'Merokok di sekolah dan luar dengan seragam sekolah', 50],
            [JenisPelanggaran::CATEGORY_MEDIUM, 'Mendapat SP ke 2', 50],
            [JenisPelanggaran::CATEGORY_HEAVY, 'Bersekongkol melakukan kejahatan', 60],
            [JenisPelanggaran::CATEGORY_HEAVY, 'Mesum di sekolah', 75],
            [JenisPelanggaran::CATEGORY_HEAVY, 'Melakukan pelecehan', 75],
            [JenisPelanggaran::CATEGORY_HEAVY, 'Membawa Sajam', 75],
            [JenisPelanggaran::CATEGORY_HEAVY, 'Melakukan Bulli', 75],
            [JenisPelanggaran::CATEGORY_HEAVY, 'Terlibat Uang Palsu', 75],
            [JenisPelanggaran::CATEGORY_HEAVY, 'Melakukan pemalakan', 75],
            [JenisPelanggaran::CATEGORY_SEVERE, 'Terlibat balapan liar', 85],
            [JenisPelanggaran::CATEGORY_SEVERE, 'Terlibat Gangster', 100],
            [JenisPelanggaran::CATEGORY_SEVERE, 'Berkelahi di sekolah dan di luar', 100],
            [JenisPelanggaran::CATEGORY_SEVERE, 'Terlibat Narkoba', 100],
            [JenisPelanggaran::CATEGORY_SEVERE, 'Terlibat Pornografi', 100],
            [JenisPelanggaran::CATEGORY_SEVERE, 'Terlibat Tawuran', 100],
            [JenisPelanggaran::CATEGORY_SEVERE, 'Mendapat SP ke 3', 100],
        ];

        JenisPelanggaran::upsert(
            collect($violationTypes)->map(fn (array $jenis): array => [
                'kategori'         => $jenis[0],
                'nama'             => $jenis[1],
                'pengurangan_poin' => $jenis[2],
                'keterangan'       => null,
                'aktif'            => true,
            ])->all(),
            uniqueBy: ['nama'],
            update: ['kategori', 'pengurangan_poin', 'keterangan', 'aktif'],
        );
    }
}
