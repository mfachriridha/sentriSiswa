<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\TeacherClass;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummySeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@sentrisiswa.sch.id'],
            [
                'name' => 'Admin Kesiswaan',
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('admin123'),
            ]
        );

        // Kelas
        $ipa1 = SchoolClass::firstOrCreate(['name' => '10 IPA 1']);
        $ips1 = SchoolClass::firstOrCreate(['name' => '10 IPS 1']);

        // ========== GURU SUDAH TERDAFTAR (is_active=true) ==========
        $wk1 = User::firstOrCreate(
            ['email' => 'ahmad.dahlan@sentrisiswa.sch.id'],
            [
                'name' => 'Ahmad Dahlan, S.Pd',
                'nip' => '19850101 202012 1 001',
                'role' => 'wali_kelas',
                'is_active' => true,
                'password' => Hash::make('password123'),
            ]
        );
        TeacherClass::firstOrCreate(['user_id' => $wk1->id, 'school_class_id' => $ipa1->id, 'role' => 'wali_kelas']);

        $wk2 = User::firstOrCreate(
            ['email' => 'siti.aminah@sentrisiswa.sch.id'],
            [
                'name' => 'Siti Aminah, M.Pd',
                'nip' => '19900615 202122 2 001',
                'role' => 'wali_kelas',
                'is_active' => true,
                'password' => Hash::make('password123'),
            ]
        );
        TeacherClass::firstOrCreate(['user_id' => $wk2->id, 'school_class_id' => $ips1->id, 'role' => 'wali_kelas']);

        // BK sudah terdaftar
        $bk = User::firstOrCreate(
            ['email' => 'rudi.hartono@sentrisiswa.sch.id'],
            [
                'name' => 'Rudi Hartono, S.Psi',
                'nip' => '19880325 201903 1 001',
                'role' => 'bk',
                'is_active' => true,
                'password' => Hash::make('password123'),
            ]
        );
        foreach ([$ipa1->id, $ips1->id] as $classId) {
            TeacherClass::firstOrCreate(['user_id' => $bk->id, 'school_class_id' => $classId, 'role' => 'bk']);
        }

        // ========== GURU BELUM TERDAFTAR (is_active=false) ==========
        User::firstOrCreate(
            ['email' => 'heru.suyana@sentrisiswa.sch.id'],
            [
                'name' => 'Heru Suyana, S.Pd',
                'nip' => '19661016 200012 1 001',
                'role' => 'wali_kelas',
                'is_active' => false,
                'password' => Hash::make('password123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'ayu.dewi@sentrisiswa.sch.id'],
            [
                'name' => 'Ayu Dewi Lestari, S.Pd',
                'nip' => '19921126 202521 2 077',
                'role' => 'wali_kelas',
                'is_active' => false,
                'password' => Hash::make('password123'),
            ]
        );

        // ========== SISWA SUDAH TERDAFTAR (ada user account) ==========
        $studentsActive = [
            ['nis' => '1001', 'nisn' => '0001001', 'name' => 'Andi Pratama', 'gender' => 'Laki-laki', 'class_id' => $ipa1->id, 'email' => 'andi.pratama@sentrisiswa.sch.id'],
            ['nis' => '1002', 'nisn' => '0001002', 'name' => 'Budi Santoso', 'gender' => 'Laki-laki', 'class_id' => $ipa1->id, 'email' => 'budi.santoso@sentrisiswa.sch.id'],
            ['nis' => '2001', 'nisn' => '0002001', 'name' => 'Citra Dewi', 'gender' => 'Perempuan', 'class_id' => $ips1->id, 'email' => 'citra.dewi@sentrisiswa.sch.id'],
            ['nis' => '2002', 'nisn' => '0002002', 'name' => 'Dewi Lestari', 'gender' => 'Perempuan', 'class_id' => $ips1->id, 'email' => 'dewi.lestari@sentrisiswa.sch.id'],
        ];

        foreach ($studentsActive as $s) {
            $user = User::firstOrCreate(
                ['email' => $s['email']],
                ['name' => $s['name'], 'role' => 'siswa', 'is_active' => true, 'password' => Hash::make('password123')]
            );
            Student::firstOrCreate(
                ['nis' => $s['nis']],
                ['nisn' => $s['nisn'], 'name' => $s['name'], 'gender' => $s['gender'], 'school_class_id' => $s['class_id'], 'user_id' => $user->id, 'poin' => 100]
            );
        }

        // ========== SISWA BELUM TERDAFTAR (user_id=null) ==========
        $studentsInactive = [
            ['nis' => '3001', 'nisn' => '0003001', 'name' => 'Eka Putra', 'gender' => 'Laki-laki', 'class_id' => $ipa1->id],
            ['nis' => '3002', 'nisn' => '0003002', 'name' => 'Fitriani', 'gender' => 'Perempuan', 'class_id' => $ips1->id],
            ['nis' => '3003', 'nisn' => '0003003', 'name' => 'Galih Prakoso', 'gender' => 'Laki-laki', 'class_id' => $ipa1->id],
        ];

        foreach ($studentsInactive as $s) {
            Student::firstOrCreate(
                ['nis' => $s['nis']],
                ['nisn' => $s['nisn'], 'name' => $s['name'], 'gender' => $s['gender'], 'school_class_id' => $s['class_id'], 'user_id' => null, 'poin' => 100]
            );
        }

        // Seed violations
        $violations = [
            ['name' => 'Terlambat 1-10 menit', 'poin' => 5, 'category' => 'ringan'],
            ['name' => 'Seragam tidak lengkap', 'poin' => 10, 'category' => 'ringan'],
            ['name' => 'Tidak mengerjakan PR', 'poin' => 10, 'category' => 'ringan'],
            ['name' => 'Keluar kelas tanpa izin', 'poin' => 15, 'category' => 'sedang'],
            ['name' => 'Merokok di lingkungan sekolah', 'poin' => 30, 'category' => 'berat'],
            ['name' => 'Berkelahi', 'poin' => 50, 'category' => 'berat'],
        ];
        foreach ($violations as $v) {
            Violation::firstOrCreate(['name' => $v['name']], $v);
        }

        $this->command->info('Dummy data: admin, 3 guru aktif, 2 guru belum daftar, 4 siswa aktif, 3 siswa belum daftar.');
    }
}
