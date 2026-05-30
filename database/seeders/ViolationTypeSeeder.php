<?php

namespace Database\Seeders;

use App\Models\ViolationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ViolationTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $violationTypes = [
            [ViolationType::CATEGORY_LIGHT, 'Penampilan tidak rapi', 5],
            [ViolationType::CATEGORY_LIGHT, 'Ber make up', 5],
            [ViolationType::CATEGORY_LIGHT, 'Atribut tidak lengkap', 5],
            [ViolationType::CATEGORY_LIGHT, 'Memakai Sandal di sekolah', 10],
            [ViolationType::CATEGORY_LIGHT, 'Mencontek', 10],
            [ViolationType::CATEGORY_LIGHT, 'Salah Kostum', 15],
            [ViolationType::CATEGORY_LIGHT, 'Memakai perhiasan', 15],
            [ViolationType::CATEGORY_LIGHT, 'Bicara tidak sopan', 15],
            [ViolationType::CATEGORY_LIGHT, 'Di kantin jam pelajaran', 15],
            [ViolationType::CATEGORY_LIGHT, 'Berpakaian tidak standar', 25],
            [ViolationType::CATEGORY_LIGHT, 'Mendapat SP 1', 25],
            [ViolationType::CATEGORY_MEDIUM, 'Bolos dari sekolah', 30],
            [ViolationType::CATEGORY_MEDIUM, 'Melakukan penghinaan', 40],
            [ViolationType::CATEGORY_MEDIUM, 'Anggota tubuh bertato', 40],
            [ViolationType::CATEGORY_MEDIUM, 'Mencoret sarana sekolah', 40],
            [ViolationType::CATEGORY_MEDIUM, 'Merusak sarana sekolah', 40],
            [ViolationType::CATEGORY_MEDIUM, 'Merwarna rambut', 40],
            [ViolationType::CATEGORY_MEDIUM, 'Mencuri di sekolah', 45],
            [ViolationType::CATEGORY_MEDIUM, 'Memalsukan dokumen', 50],
            [ViolationType::CATEGORY_MEDIUM, 'Berlaku Rasis di sekolah', 50],
            [ViolationType::CATEGORY_MEDIUM, 'Merokok di sekolah dan luar dengan seragam sekolah', 50],
            [ViolationType::CATEGORY_MEDIUM, 'Mendapat SP ke 2', 50],
            [ViolationType::CATEGORY_HEAVY, 'Bersekongkol melakukan kejahatan', 60],
            [ViolationType::CATEGORY_HEAVY, 'Mesum di sekolah', 75],
            [ViolationType::CATEGORY_HEAVY, 'Melakukan pelecehan', 75],
            [ViolationType::CATEGORY_HEAVY, 'Membawa Sajam', 75],
            [ViolationType::CATEGORY_HEAVY, 'Melakukan Bulli', 75],
            [ViolationType::CATEGORY_HEAVY, 'Terlibat Uang Palsu', 75],
            [ViolationType::CATEGORY_HEAVY, 'Melakukan pemalakan', 75],
            [ViolationType::CATEGORY_SEVERE, 'Terlibat balapan liar', 85],
            [ViolationType::CATEGORY_SEVERE, 'Terlibat Gangster', 100],
            [ViolationType::CATEGORY_SEVERE, 'Berkelahi di sekolah dan di luar', 100],
            [ViolationType::CATEGORY_SEVERE, 'Terlibat Narkoba', 100],
            [ViolationType::CATEGORY_SEVERE, 'Terlibat Pornografi', 100],
            [ViolationType::CATEGORY_SEVERE, 'Terlibat Tawuran', 100],
            [ViolationType::CATEGORY_SEVERE, 'Mendapat SP ke 3', 100],
        ];

        ViolationType::upsert(
            collect($violationTypes)->map(fn (array $violationType): array => [
                'category' => $violationType[0],
                'name' => $violationType[1],
                'point_deduction' => $violationType[2],
                'description' => null,
                'is_active' => true,
            ])->all(),
            uniqueBy: ['name'],
            update: ['category', 'point_deduction', 'description', 'is_active'],
        );
    }
}
