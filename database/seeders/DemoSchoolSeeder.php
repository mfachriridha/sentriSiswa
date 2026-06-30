<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\SchoolRule;
use App\Models\StudentBiodata;
use App\Models\StudentProfile;
use App\Models\StudentViolation;
use App\Models\User;
use App\Models\ViolationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DemoSchoolSeeder extends Seeder
{
    private static array $palette = [
        [52, 152, 219],   // blue
        [46, 204, 113],   // green
        [155, 89, 182],   // purple
        [231, 76, 60],    // red
        [241, 196, 15],   // yellow
        [26, 188, 156],   // teal
    ];

    public function run(): void
    {
        $homerooms = $this->createHomeroomTeachers();
        $this->createCounselors();
        $this->createStudentAffairs();

        $classes = $this->createClasses($homerooms);
        $students = $this->createStudents($classes);

        $this->createAttendanceRecords($students);
        $this->createViolationRecords($students);
        $this->createSchoolRule();
    }

    /** @return array<string, User> keyed by slug e.g. "10.ipa1" */
    private function createHomeroomTeachers(): array
    {
        $teacherData = [
            '10.ipa1' => ['Raka Pradipta', '10'],
            '10.ipa2' => ['Nadia Lestari', '10'],
            '10.ips1' => ['Bagas Wiratama', '10'],
            '10.ips2' => ['Maya Permatasari', '10'],
            '11.ipa1' => ['Dimas Mahendra', '11'],
            '11.ipa2' => ['Sinta Rahmawati', '11'],
            '11.ips1' => ['Fajar Nugroho', '11'],
            '11.ips2' => ['Dewi Anjani', '11'],
            '12.ipa1' => ['Hendra Kusuma', '12'],
            '12.ipa2' => ['Lina Wulandari', '12'],
            '12.ips1' => ['Agus Santoso', '12'],
            '12.ips2' => ['Rina Marlina', '12'],
        ];

        $teachers = [];
        $nip = 1;

        foreach ($teacherData as $slug => [$name, $grade]) {
            $emailSlug = str_replace('.', '-', $slug);
            $teacher = User::create([
                'name' => $name,
                'email' => "wali.{$emailSlug}@sentrisiswa.test",
                'password' => Hash::make('password'),
                'role' => 'wali_kelas',
                'status' => 'registered',
            ]);

            $profile = $teacher->teacherProfile()->create([
                'nip' => sprintf('197%02d0000000000', $nip++),
                'phone' => sprintf('628121%05d', $nip),
                'teacher_type' => 'homeroom',
                'grade' => $grade,
            ]);

            $photoPath = $this->generateTeacherPhoto($teacher->id, $name);
            $profile->update(['photo' => $photoPath]);

            $teachers[$slug] = $teacher;
        }

        return $teachers;
    }

    private function createCounselors(): void
    {
        $counselors = [
            '10' => 'Arif Nugroho',
            '11' => 'Ratna Wulandari',
            '12' => 'Yusuf Firmansyah',
        ];

        foreach ($counselors as $grade => $name) {
            $grade = (string) $grade;
            $teacher = User::create([
                'name' => $name,
                'email' => "bk{$grade}@sentrisiswa.test",
                'password' => Hash::make('password'),
                'role' => 'bk',
                'status' => 'registered',
            ]);

            $profile = $teacher->teacherProfile()->create([
                'nip' => "19{$grade}9000000000",
                'phone' => "62813{$grade}000000",
                'teacher_type' => 'counselor',
                'grade' => $grade,
            ]);

            $photoPath = $this->generateTeacherPhoto($teacher->id, $name);
            $profile->update(['photo' => $photoPath]);
        }
    }

    private function createStudentAffairs(): void
    {
        $teacher = User::create([
            'name' => 'Hendra Saputra',
            'email' => 'kesiswaan@sentrisiswa.test',
            'password' => Hash::make('password'),
            'role' => 'kesiswaan',
            'status' => 'registered',
        ]);

        $profile = $teacher->teacherProfile()->create([
            'nip' => '19990000000000',
            'phone' => '6281399000000',
            'teacher_type' => 'student_affairs',
        ]);

        $photoPath = $this->generateTeacherPhoto($teacher->id, $teacher->name);
        $profile->update(['photo' => $photoPath]);
    }

    /**
     * @param  array<string, User>  $homerooms
     * @return array<int, SchoolClass>
     */
    private function createClasses(array $homerooms): array
    {
        $classConfig = [
            ['10', 'X', 'IPA', '1', '10.ipa1'],
            ['10', 'X', 'IPA', '2', '10.ipa2'],
            ['10', 'X', 'IPS', '1', '10.ips1'],
            ['10', 'X', 'IPS', '2', '10.ips2'],
            ['11', 'XI', 'IPA', '1', '11.ipa1'],
            ['11', 'XI', 'IPA', '2', '11.ipa2'],
            ['11', 'XI', 'IPS', '1', '11.ips1'],
            ['11', 'XI', 'IPS', '2', '11.ips2'],
            ['12', 'XII', 'IPA', '1', '12.ipa1'],
            ['12', 'XII', 'IPA', '2', '12.ipa2'],
            ['12', 'XII', 'IPS', '1', '12.ips1'],
            ['12', 'XII', 'IPS', '2', '12.ips2'],
        ];

        $classes = [];

        foreach ($classConfig as [$grade, $roman, $jurusan, $number, $slug]) {
            $classes[] = SchoolClass::create([
                'name' => "{$roman} {$jurusan} {$number}",
                'grade' => $grade,
                'homeroom_teacher_id' => $homerooms[$slug]->id,
            ]);
        }

        return $classes;
    }

    /**
     * @param  array<int, SchoolClass>  $classes
     * @return array<int, StudentProfile>
     */
    private function createStudents(array $classes): array
    {
        $students = [];
        $sequence = 1;

        $names = [
            // Kelas 10 IPA 1 (3 siswa)
            'Aditya Pratama', 'Aisyah Nurhaliza', 'Akbar Maulana',
            // Kelas 10 IPA 2
            'Amelia Putri', 'Ananda Rizky', 'Andika Saputra',
            // Kelas 10 IPS 1
            'Aulia Rahmadani', 'Bagus Setiawan', 'Bintang Ramadhan',
            // Kelas 10 IPS 2
            'Cahya Maharani', 'Citra Anggraini', 'Daffa Fadillah',
            // Kelas 11 IPA 1
            'Dewi Kartika', 'Dina Maharani', 'Eka Purnama',
            // Kelas 11 IPA 2
            'Elisa Febriani', 'Fajar Hidayat', 'Farhan Maulana',
            // Kelas 11 IPS 1
            'Fitri Lestari', 'Galang Prasetyo', 'Gilang Ramadhan',
            // Kelas 11 IPS 2
            'Hana Safitri', 'Hanif Nugraha', 'Indah Permatasari',
            // Kelas 12 IPA 1
            'Intan Pratiwi', 'Iqbal Firmansyah', 'Jihan Azzahra',
            // Kelas 12 IPA 2
            'Kania Maharani', 'Kevin Saputra', 'Kurnia Sari',
            // Kelas 12 IPS 1
            'Laila Rahma', 'Lukman Hakim', 'Maya Salsabila',
            // Kelas 12 IPS 2
            'Miftah Fauzan', 'Nabila Khairunnisa', 'Nadia Amalia',
        ];

        $birthPlaces = ['Jakarta', 'Bandung', 'Bogor', 'Depok', 'Bekasi', 'Tangerang'];
        $streets = ['Jalan Melati Raya', 'Jalan Kenanga Indah', 'Jalan Cempaka Putih', 'Jalan Mawar Asri', 'Jalan Anggrek Timur', 'Jalan Flamboyan'];
        $occupations = ['Karyawan', 'Wiraswasta', 'Guru', 'Perawat', 'Pedagang', 'Pegawai Negeri'];

        $nameIndex = 0;

        foreach ($classes as $class) {
            for ($slot = 1; $slot <= 3; $slot++) {
                $nis = sprintf('%05d', $sequence);
                $name = $names[$nameIndex] ?? "Siswa {$sequence}";
                $street = $streets[($sequence - 1) % count($streets)];
                // Siswa ke-3 di tiap kelas = unregistered (belum buat akun)
                $isUnregistered = $slot === 3;

                $user = User::create([
                    'name' => $name,
                    'email' => $isUnregistered ? null : "siswa{$nis}@sentrisiswa.test",
                    'password' => Hash::make('password'),
                    'role' => 'siswa',
                    'status' => $isUnregistered ? 'unregistered' : 'registered',
                ]);

                $profile = StudentProfile::create([
                    'user_id' => $user->id,
                    'nisn' => sprintf('00%08d', $sequence),
                    'nis' => $nis,
                    'class_id' => $class->id,
                    'phone' => "0812{$nis}",
                    'address' => $street . ', Kelurahan Sentri',
                ]);

                StudentBiodata::create([
                    'student_profile_id' => $profile->id,
                    'place_of_birth' => $birthPlaces[($sequence - 1) % count($birthPlaces)],
                    'date_of_birth' => now()->subYears(16)->subDays($sequence)->toDateString(),
                    'gender' => $slot % 2 === 0 ? 'P' : 'L',
                    'religion' => 'Islam',
                    'family_status' => 'Kandung',
                    'child_number' => $slot,
                    'school_of_origin' => 'SMP Nusantara',
                    'admission_date' => now()->subYear()->startOfMonth()->toDateString(),
                    'father_name' => "Bapak dari {$name}",
                    'father_occupation' => $occupations[($sequence - 1) % count($occupations)],
                    'mother_name' => "Ibu dari {$name}",
                    'mother_occupation' => 'Ibu Rumah Tangga',
                    'parent_address' => $street . ', Kelurahan Sentri',
                    'parent_phone' => "0821{$nis}",
                    'guardian_name' => "Wali dari {$name}",
                    'guardian_occupation' => $occupations[$sequence % count($occupations)],
                    'guardian_address' => $street . ', Kelurahan Sentri',
                    'guardian_phone' => "0831{$nis}",
                ]);

                $photoPath = $this->generateStudentPhoto($nis, $name);
                $profile->update(['photo' => $photoPath]);

                $students[] = $profile;
                $sequence++;
                $nameIndex++;
            }
        }

        return $students;
    }

    /** @param array<int, StudentProfile> $students */
    private function createAttendanceRecords(array $students): void
    {
        $dates = collect(range(0, 20))
            ->map(fn (int $d) => today()->subDays($d))
            ->filter(fn ($date) => $date->isWeekday())
            ->take(10)
            ->values();

        // Variasi status: tiap siswa punya pola berbeda
        $patterns = [
            // Rajin hadir
            ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'terlambat', 'hadir', 'hadir'],
            // Sering terlambat
            ['terlambat', 'hadir', 'terlambat', 'hadir', 'terlambat', 'hadir', 'terlambat', 'hadir', 'hadir', 'terlambat'],
            // Sering absen/izin
            ['izin', 'sakit', 'hadir', 'alpha', 'izin', 'hadir', 'sakit', 'hadir', 'izin', 'belum_absen'],
        ];

        foreach ($students as $idx => $student) {
            $pattern = $patterns[$idx % count($patterns)];
            foreach ($dates as $dayIdx => $date) {
                $status = $pattern[$dayIdx] ?? 'hadir';
                $selfiePath = null;

                if (in_array($status, ['hadir', 'terlambat'])) {
                    $selfiePath = $this->generateSelfiePhoto($student->id, $date->format('Y-m-d'));
                }

                Attendance::create([
                    'student_profile_id' => $student->id,
                    'date' => $date->toDateString(),
                    'status' => $status,
                    'check_in_time' => match ($status) {
                        'hadir' => '06:45:00',
                        'terlambat' => '07:'.sprintf('%02d', 10 + ($idx % 15)).':00',
                        default => null,
                    },
                    'selfie_path' => $selfiePath,
                ]);
            }
        }
    }

    /** @param array<int, StudentProfile> $students */
    private function createViolationRecords(array $students): void
    {
        $violationTypes = ViolationType::orderBy('point_deduction')->get();
        $recorders = User::whereIn('role', ['wali_kelas', 'bk', 'kesiswaan'])->pluck('id');

        foreach (array_values($students) as $index => $student) {
            if ($index % 3 !== 0) {
                continue;
            }

            $violationType = $violationTypes[$index % $violationTypes->count()];
            $status = match ($index % 9) {
                0 => 'pending',
                3 => 'rejected',
                default => 'approved',
            };

            StudentViolation::create([
                'student_profile_id' => $student->id,
                'violation_type_id' => $violationType->id,
                'recorded_by_user_id' => $recorders[$index % $recorders->count()],
                'violation_date' => today()->subDays($index % 20)->toDateString(),
                'violation_name' => $violationType->name,
                'violation_category' => $violationType->category,
                'point_deduction' => $violationType->point_deduction,
                'notes' => 'Data demo pelanggaran.',
                'status' => $status,
                'approved_by_user_id' => $status === 'pending' ? null : $recorders->last(),
                'approved_at' => $status === 'pending' ? null : now(),
                'rejection_reason' => $status === 'rejected' ? 'Data demo ditolak.' : null,
            ]);
        }
    }

    private function generateImage(int $width, int $height, int $r, int $g, int $b, string $absPath): void
    {
        $dir = dirname($absPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $img = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($img, $r, $g, $b);
        imagefill($img, 0, 0, $bg);

        // Subtle lighter circle in center
        $light = imagecolorallocatealpha($img, 255, 255, 255, 80);
        imagefilledellipse($img, (int) ($width / 2), (int) ($height / 2), (int) ($width * 0.5), (int) ($height * 0.5), $light);

        imagejpeg($img, $absPath, 85);
        imagedestroy($img);
    }

    private function generateTeacherPhoto(int $userId, string $name): string
    {
        $path = "photos/teachers/{$userId}.jpg";
        [$r, $g, $b] = self::$palette[ord($name[0]) % count(self::$palette)];
        $this->generateImage(200, 200, $r, $g, $b, storage_path("app/public/{$path}"));

        return $path;
    }

    private function generateStudentPhoto(string $nis, string $name): string
    {
        $path = "photos/students/{$nis}.jpg";
        [$r, $g, $b] = self::$palette[ord($name[0]) % count(self::$palette)];
        $this->generateImage(300, 400, $r, $g, $b, storage_path("app/public/{$path}"));

        return $path;
    }

    private function generateSelfiePhoto(int $profileId, string $date): string
    {
        $path = "attendance-selfies/{$profileId}/{$date}-demo.jpg";
        $colorIdx = ($profileId + (int) str_replace('-', '', $date)) % count(self::$palette);
        [$r, $g, $b] = self::$palette[$colorIdx];
        $this->generateImage(300, 400, $r, $g, $b, storage_path("app/public/{$path}"));

        return $path;
    }

    private function createSchoolRule(): void
    {
        $path = 'school-rules/tata-tertib-demo.pdf';
        Storage::disk('public')->put($path, "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF");

        SchoolRule::create([
            'title' => 'Tata Tertib Sekolah Demo',
            'file_path' => $path,
            'is_published' => true,
            'uploaded_by_user_id' => User::where('role', 'kesiswaan')->value('id'),
        ]);
    }
}
