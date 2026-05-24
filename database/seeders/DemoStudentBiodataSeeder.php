<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\StudentBiodata;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoStudentBiodataSeeder extends Seeder
{
    public function run(): void
    {
        $class = SchoolClass::first();

        $user = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti.demo@sentrisiswa.test',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $profile = StudentProfile::create([
            'user_id' => $user->id,
            'nisn' => '0012345678',
            'nis' => '20240001',
            'class_id' => $class->id,
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 45, Jakarta Selatan',
        ]);

        StudentBiodata::create([
            'student_profile_id' => $profile->id,
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '2008-05-15',
            'gender' => 'P',
            'religion' => 'Islam',
            'family_status' => 'Kandung',
            'child_number' => 2,
            'school_of_origin' => 'SMP Negeri 1 Jakarta',
            'admission_date' => '2024-07-15',
            'father_name' => 'Budi Santoso',
            'father_occupation' => 'Pegawai Negeri',
            'mother_name' => 'Sari Dewi',
            'mother_occupation' => 'Guru',
            'parent_address' => 'Jl. Merdeka No. 45, Jakarta Selatan',
            'parent_phone' => '081298765432',
            'guardian_name' => null,
            'guardian_occupation' => null,
            'guardian_address' => null,
            'guardian_phone' => null,
        ]);

        $this->command->info("Demo student created: {$user->name} (ID: {$user->id})");
    }
}
