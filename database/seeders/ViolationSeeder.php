<?php

namespace Database\Seeders;

use App\Models\Violation;
use Illuminate\Database\Seeder;

class ViolationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Ringan (5-25)
            ['Penampilan tidak rapi', 5, 'ringan'],
            ['Ber make up', 5, 'ringan'],
            ['Atribut tidak lengkap', 5, 'ringan'],
            ['Memakai sandal di sekolah', 10, 'ringan'],
            ['Mencontek', 10, 'ringan'],
            ['Salah kostum', 15, 'ringan'],
            ['Memakai perhiasan', 15, 'ringan'],
            ['Bicara tidak sopan', 15, 'ringan'],
            ['Di kantin jam pelajaran', 15, 'ringan'],
            ['Berpakaian tidak standar', 25, 'ringan'],
            ['Mendapat SP 1', 25, 'ringan'],
            // Sedang (26-50)
            ['Bolos dari sekolah', 30, 'sedang'],
            ['Melakukan penghinaan', 40, 'sedang'],
            ['Anggota tubuh bertato', 40, 'sedang'],
            ['Mencoret sarana sekolah', 40, 'sedang'],
            ['Merusak sarana sekolah', 40, 'sedang'],
            ['Mewarnai rambut', 40, 'sedang'],
            ['Mencuri di sekolah', 45, 'sedang'],
            ['Memalsukan dokumen', 50, 'sedang'],
            ['Berlaku rasis di sekolah', 50, 'sedang'],
            ['Merokok di sekolah/dengan seragam', 50, 'sedang'],
            ['Mendapat SP 2', 50, 'sedang'],
            // Berat (51-75)
            ['Bersekongkol melakukan kejahatan', 60, 'berat'],
            ['Mesum di sekolah', 75, 'berat'],
            ['Melakukan pelecehan', 75, 'berat'],
            ['Membawa sajam', 75, 'berat'],
            ['Melakukan bully', 75, 'berat'],
            ['Terlibat uang palsu', 75, 'berat'],
            ['Melakukan pemalakan', 75, 'berat'],
            // Amat Berat (76-100)
            ['Terlibat balapan liar', 85, 'amat_berat'],
            ['Terlibat gangster', 100, 'amat_berat'],
            ['Berkelahi di sekolah/di luar', 100, 'amat_berat'],
            ['Terlibat narkoba', 100, 'amat_berat'],
            ['Terlibat pornografi', 100, 'amat_berat'],
            ['Terlibat tawuran', 100, 'amat_berat'],
            ['Mendapat SP 3', 100, 'amat_berat'],
        ];

        foreach ($data as $v) {
            Violation::firstOrCreate(
                ['name' => $v[0], 'poin' => $v[1]],
                ['category' => $v[2]]
            );
        }
    }
}