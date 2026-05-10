<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@sentrisiswa.sch.id'],
            ['name' => 'Admin Kesiswaan', 'password' => 'admin123', 'role' => 'admin']
        );

        User::firstOrCreate(
            ['email' => 'siswa@sentrisiswa.sch.id'],
            ['name' => 'Ahmad Fauzi', 'password' => 'siswa123', 'role' => 'siswa']
        );

        $kelas = SchoolClass::firstOrCreate(['name' => 'XII IPA 1']);

        $student = Student::firstOrCreate(
            ['nis' => 'S001'],
            [
                'nisn' => '0087654321',
                'name' => 'Ahmad Fauzi',
                'gender' => 'Laki-laki',
                'school_class_id' => $kelas->id,
                'birth_place' => 'Tangerang',
                'birth_date' => '2008-08-17',
                'religion' => 'Islam',
                'family_status' => 'Anak Kandung',
                'birth_order' => '2',
                'address' => 'Jl. Merdeka No. 42, Kab. Tangerang',
            ]
        );

        // Orang tua
        StudentParent::firstOrCreate(
            ['student_id' => $student->id, 'type' => 'father'],
            ['name' => 'H. Supriyanto, S.Pd.', 'job' => 'Guru PNS', 'phone' => '081234567891']
        );
        StudentParent::firstOrCreate(
            ['student_id' => $student->id, 'type' => 'mother'],
            ['name' => 'Hj. Siti Rahmawati', 'job' => 'Ibu Rumah Tangga', 'phone' => '081398765432']
        );

        // Wali (kosong)
        Guardian::firstOrCreate(
            ['student_id' => $student->id],
            ['name' => null, 'job' => null, 'phone' => null, 'address' => null]
        );
    }
}