<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@sentrisiswa.sch.id'],
            [
                'name' => 'Admin Kesiswaan',
                'password' => 'admin123',
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'siswa@sentrisiswa.sch.id'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => 'siswa123',
                'role' => 'siswa',
            ]
        );

        // Dummy kelas & 1 siswa
        $kelas = Kelas::firstOrCreate(['name' => 'XII IPA 1']);

        Student::firstOrCreate(
            ['nis' => 'S001'],
            [
                'nisn' => '0087654321',
                'name' => 'Ahmad Fauzi',
                'gender' => 'Laki-laki',
                'kelas_id' => $kelas->id,
                'phone' => '081312345678',
                'parent_phone' => '081398765432',
                'birth_place' => 'Tangerang',
                'birth_date' => '2008-08-17',
                'religion' => 'Islam',
                'family_status' => 'Anak Kandung',
                'birth_order' => '2',
                'address' => 'Jl. Merdeka No. 42, Kab. Tangerang',
                'father_name' => 'H. Supriyanto, S.Pd.',
                'father_job' => 'Guru PNS',
                'father_phone' => '081234567891',
                'mother_name' => 'Hj. Siti Rahmawati',
                'mother_job' => 'Ibu Rumah Tangga',
                'mother_phone' => '081398765432',
            ]
        );
    }
}