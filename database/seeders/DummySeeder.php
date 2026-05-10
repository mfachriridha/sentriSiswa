<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\TeacherClass;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummySeeder extends Seeder
{
    public function run(): void
    {
        $ipa1 = SchoolClass::firstOrCreate(['name' => '10 IPA 1']);
        $ips1 = SchoolClass::firstOrCreate(['name' => '10 IPS 1']);

        // 2 Wali Kelas
        $wk1 = User::firstOrCreate(
            ['email' => '198501012020121001@sentrisiswa.sch.id'],
            [
                'name' => 'Ahmad Dahlan, S.Pd',
                'nip' => '19850101 202012 1 001',
                'role' => 'wali_kelas',
                'is_active' => false,
                'password' => Hash::make('password123'),
            ]
        );

        TeacherClass::firstOrCreate([
            'user_id' => $wk1->id,
            'school_class_id' => $ipa1->id,
            'role' => 'wali_kelas',
        ]);

        $wk2 = User::firstOrCreate(
            ['email' => '19900615202122001@sentrisiswa.sch.id'],
            [
                'name' => 'Siti Aminah, M.Pd',
                'nip' => '19900615 202122 2 001',
                'role' => 'wali_kelas',
                'is_active' => false,
                'password' => Hash::make('password123'),
            ]
        );

        TeacherClass::firstOrCreate([
            'user_id' => $wk2->id,
            'school_class_id' => $ips1->id,
            'role' => 'wali_kelas',
        ]);

        // 1 BK
        $bk = User::firstOrCreate(
            ['email' => '198803252019031001@sentrisiswa.sch.id'],
            [
                'name' => 'Rudi Hartono, S.Psi',
                'nip' => '19880325 201903 1 001',
                'role' => 'bk',
                'is_active' => false,
                'password' => Hash::make('password123'),
            ]
        );

        foreach ([$ipa1->id, $ips1->id] as $classId) {
            TeacherClass::firstOrCreate([
                'user_id' => $bk->id,
                'school_class_id' => $classId,
                'role' => 'bk',
            ]);
        }

        // 4 Siswa (2 per kelas) with user accounts
        $students = [
            ['nis' => '1001', 'nisn' => '0001001', 'name' => 'Andi Pratama', 'gender' => 'Laki-laki', 'class_id' => $ipa1->id, 'email' => 'andi.pratama@sentrisiswa.sch.id'],
            ['nis' => '1002', 'nisn' => '0001002', 'name' => 'Budi Santoso', 'gender' => 'Laki-laki', 'class_id' => $ipa1->id, 'email' => 'budi.santoso@sentrisiswa.sch.id'],
            ['nis' => '2001', 'nisn' => '0002001', 'name' => 'Citra Dewi', 'gender' => 'Perempuan', 'class_id' => $ips1->id, 'email' => 'citra.dewi@sentrisiswa.sch.id'],
            ['nis' => '2002', 'nisn' => '0002002', 'name' => 'Dewi Lestari', 'gender' => 'Perempuan', 'class_id' => $ips1->id, 'email' => 'dewi.lestari@sentrisiswa.sch.id'],
        ];

        foreach ($students as $s) {
            $user = User::firstOrCreate(
                ['email' => $s['email']],
                [
                    'name' => $s['name'],
                    'role' => 'siswa',
                    'is_active' => true,
                    'password' => Hash::make('password123'),
                ]
            );

            $student = Student::firstOrCreate(
                ['nis' => $s['nis']],
                [
                    'nisn' => $s['nisn'],
                    'name' => $s['name'],
                    'gender' => $s['gender'],
                    'school_class_id' => $s['class_id'],
                    'user_id' => $user->id,
                    'poin' => 100,
                ]
            );
        }

        $this->command->info('Dummy data created: 2 wali kelas, 1 BK, 4 siswa, 2 kelas.');
    }
}
