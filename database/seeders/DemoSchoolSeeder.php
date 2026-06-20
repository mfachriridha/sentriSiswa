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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DemoSchoolSeeder extends Seeder
{
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

    /**
     * @return array<int, User>
     */
    private function createHomeroomTeachers(): array
    {
        $teachers = [];
        $teacherNames = [
            'Raka Pradipta',
            'Nadia Lestari',
            'Bagas Wiratama',
            'Maya Permatasari',
            'Dimas Mahendra',
            'Sinta Rahmawati',
        ];
        $nameIndex = 0;

        foreach (['10', '11', '12'] as $grade) {
            for ($index = 1; $index <= 2; $index++) {
                $teacher = User::create([
                    'name' => $teacherNames[$nameIndex],
                    'email' => "wali{$grade}{$index}@sentrisiswa.test",
                    'password' => Hash::make('password'),
                    'role' => 'wali_kelas',
                    'status' => 'registered',
                ]);

                $teacher->teacherProfile()->create([
                    'nip' => "19{$grade}{$index}000000000",
                    'phone' => "62812{$grade}{$index}00000",
                ]);

                $teachers[] = $teacher;
                $nameIndex++;
            }
        }

        return $teachers;
    }

    private function createCounselors(): void
    {
        foreach (['10' => 'Arif Nugroho', '11' => 'Ratna Wulandari', '12' => 'Yusuf Firmansyah'] as $grade => $name) {
            $grade = (string) $grade;

            $teacher = User::create([
                'name' => $name,
                'email' => "bk{$grade}@sentrisiswa.test",
                'password' => Hash::make('password'),
                'role' => 'bk',
                'status' => 'registered',
            ]);

            $teacher->teacherProfile()->create([
                'nip' => "19{$grade}9000000000",
                'phone' => "62813{$grade}000000",
                'grade' => $grade,
            ]);
        }
    }

    private function createStudentAffairs(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA ignore_check_constraints = ON');
        }

        $teacher = User::create([
            'name' => 'Hendra Saputra',
            'email' => 'kesiswaan@sentrisiswa.test',
            'password' => Hash::make('password'),
            'role' => 'kesiswaan',
            'status' => 'registered',
        ]);

        $teacher->teacherProfile()->create([
            'nip' => '19990000000000',
            'phone' => '6281399000000',
        ]);

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA ignore_check_constraints = OFF');
        }
    }

    /**
     * @param  array<int, User>  $homerooms
     * @return array<int, SchoolClass>
     */
    private function createClasses(array $homerooms): array
    {
        $classes = [];
        $teacherIndex = 0;

        foreach (['10' => 'X', '11' => 'XI', '12' => 'XII'] as $grade => $roman) {
            for ($index = 1; $index <= 2; $index++) {
                $classes[] = SchoolClass::create([
                    'name' => "{$roman} RPL {$index}",
                    'grade' => (string) $grade,
                    'homeroom_teacher_id' => $homerooms[$teacherIndex]->id,
                ]);

                $teacherIndex++;
            }
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
        $studentNames = collect($this->studentNames())->shuffle()->values()->all();
        $birthPlaces = ['Jakarta', 'Bandung', 'Bogor', 'Depok', 'Bekasi', 'Tangerang'];
        $streets = ['Jalan Melati Raya', 'Jalan Kenanga Indah', 'Jalan Cempaka Putih', 'Jalan Mawar Asri', 'Jalan Anggrek Timur', 'Jalan Flamboyan'];
        $occupations = ['Karyawan', 'Wiraswasta', 'Guru', 'Perawat', 'Pedagang', 'Pegawai Negeri'];

        foreach ($classes as $class) {
            for ($index = 1; $index <= 3; $index++) {
                $isUnregistered = $index === 3;
                $nis = sprintf('%05d', $sequence);
                $studentName = $studentNames[$sequence - 1];
                $street = $streets[($sequence - 1) % count($streets)];

                $user = User::create([
                    'name' => $studentName,
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
                    'address' => $street.', Kelurahan Sentri',
                ]);

                StudentBiodata::create([
                    'student_profile_id' => $profile->id,
                    'place_of_birth' => $birthPlaces[($sequence - 1) % count($birthPlaces)],
                    'date_of_birth' => now()->subYears(16)->subDays($sequence)->toDateString(),
                    'gender' => $index % 2 === 0 ? 'P' : 'L',
                    'religion' => 'Islam',
                    'family_status' => 'Kandung',
                    'child_number' => $index,
                    'school_of_origin' => 'SMP Nusantara',
                    'admission_date' => now()->subYear()->startOfMonth()->toDateString(),
                    'father_name' => "Bapak {$studentName}",
                    'father_occupation' => $occupations[($sequence - 1) % count($occupations)],
                    'mother_name' => "Ibu {$studentName}",
                    'mother_occupation' => 'Ibu Rumah Tangga',
                    'parent_address' => $street.', Kelurahan Sentri',
                    'parent_phone' => "0821{$nis}",
                    'guardian_name' => "Wali {$studentName}",
                    'guardian_occupation' => $occupations[$sequence % count($occupations)],
                    'guardian_address' => $street.', Kelurahan Sentri',
                    'guardian_phone' => "0831{$nis}",
                ]);

                $students[] = $profile;
                $sequence++;
            }
        }

        return $students;
    }

    /**
     * @return list<string>
     */
    private function studentNames(): array
    {
        return [
            'Aditya Pratama',
            'Aisyah Nurhaliza',
            'Akbar Maulana',
            'Amelia Putri',
            'Ananda Rizky',
            'Andika Saputra',
            'Aulia Rahmadani',
            'Bagus Setiawan',
            'Bintang Ramadhan',
            'Cahya Maharani',
            'Citra Anggraini',
            'Daffa Fadillah',
            'Dewi Kartika',
            'Dina Maharani',
            'Eka Purnama',
            'Elisa Febriani',
            'Fajar Hidayat',
            'Farhan Maulana',
            'Fitri Lestari',
            'Galang Prasetyo',
            'Gilang Ramadhan',
            'Hana Safitri',
            'Hanif Nugraha',
            'Indah Permatasari',
            'Intan Pratiwi',
            'Iqbal Firmansyah',
            'Jihan Azzahra',
            'Kania Maharani',
            'Kevin Saputra',
            'Kurnia Sari',
            'Laila Rahma',
            'Lukman Hakim',
            'Maya Salsabila',
            'Miftah Fauzan',
            'Nabila Khairunnisa',
            'Nadia Amalia',
            'Naufal Akbar',
            'Novia Ramadhani',
            'Putra Mahendra',
            'Putri Azzahra',
            'Rafi Alfarizi',
            'Rahma Fitriana',
            'Rangga Pratama',
            'Reza Fahlevi',
            'Rizki Kurniawan',
            'Salsa Nabila',
            'Sari Wulandari',
            'Satria Wijaya',
            'Tasya Amelia',
            'Tegar Saputra',
            'Tiara Oktaviani',
            'Vina Lestari',
            'Wahyu Ramadhan',
            'Wulan Puspita',
            'Yogi Prasetyo',
            'Yulia Rahmawati',
            'Zahra Aulia',
            'Zaki Mubarak',
            'Zidan Ardiansyah',
            'Zulfikar Hakim',
        ];
    }

    /**
     * @param  array<int, StudentProfile>  $students
     */
    private function createAttendanceRecords(array $students): void
    {
        $dates = collect(range(0, 13))
            ->map(fn (int $daysAgo) => today()->subDays($daysAgo))
            ->filter(fn ($date): bool => $date->isWeekday())
            ->take(10)
            ->values();

        $statuses = ['hadir', 'hadir', 'hadir', 'terlambat', 'izin', 'sakit', 'alpha', 'belum_absen'];

        foreach ($students as $studentIndex => $student) {
            foreach ($dates as $dateIndex => $date) {
                $status = $statuses[($studentIndex + $dateIndex) % count($statuses)];

                Attendance::create([
                    'student_profile_id' => $student->id,
                    'date' => $date->toDateString(),
                    'status' => $status,
                    'check_in_time' => in_array($status, ['hadir', 'terlambat'], true)
                        ? ($status === 'hadir' ? '06:45:00' : '07:10:00')
                        : null,
                ]);
            }
        }
    }

    /**
     * @param  array<int, StudentProfile>  $students
     */
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
