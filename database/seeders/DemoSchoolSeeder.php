<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\BiodataSiswa;
use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use App\Models\TataTertib;
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

    /** @return array<string, Pengguna> keyed by tingkat e.g. "10" */
    private function createHomeroomTeachers(): array
    {
        $teacherData = [
            '10' => 'Raka Pradipta',
            '11' => 'Dimas Mahendra',
            '12' => 'Hendra Kusuma',
        ];

        $teachers = [];
        $nip = 1;

        foreach ($teacherData as $tingkat => $nama) {
            $guru = Pengguna::create([
                'nama' => $nama,
                'email' => "wali{$tingkat}@sentrisiswa.test",
                'password' => Hash::make('password'),
                'peran' => 'wali_kelas',
                'status' => 'registered',
            ]);

            $profil = $guru->profilGuru()->create([
                'nip' => sprintf('197%02d0000000000', $nip++),
                'telepon' => sprintf('628121%05d', $nip),
                'tipe_guru' => 'homeroom',
                'tingkat' => $tingkat,
            ]);

            $photoPath = $this->generateTeacherPhoto($guru->id, $nama);
            $profil->update(['foto' => $photoPath]);

            $teachers[$tingkat] = $guru;
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

        foreach ($counselors as $tingkat => $nama) {
            $tingkat = (string) $tingkat;
            $guru = Pengguna::create([
                'nama' => $nama,
                'email' => "bk{$tingkat}@sentrisiswa.test",
                'password' => Hash::make('password'),
                'peran' => 'bk',
                'status' => 'registered',
            ]);

            $profil = $guru->profilGuru()->create([
                'nip' => "19{$tingkat}9000000000",
                'telepon' => "62813{$tingkat}000000",
                'tipe_guru' => 'counselor',
                'tingkat' => $tingkat,
            ]);

            $photoPath = $this->generateTeacherPhoto($guru->id, $nama);
            $profil->update(['foto' => $photoPath]);
        }
    }

    private function createStudentAffairs(): void
    {
        $guru = Pengguna::create([
            'nama' => 'Hendra Saputra',
            'email' => 'kesiswaan@sentrisiswa.test',
            'password' => Hash::make('password'),
            'peran' => 'kesiswaan',
            'status' => 'registered',
        ]);

        $profil = $guru->profilGuru()->create([
            'nip' => '19990000000000',
            'telepon' => '6281399000000',
            'tipe_guru' => 'student_affairs',
        ]);

        $photoPath = $this->generateTeacherPhoto($guru->id, $guru->nama);
        $profil->update(['foto' => $photoPath]);
    }

    /**
     * @param  array<string, Pengguna>  $homerooms
     * @return array<int, Kelas>
     */
    private function createClasses(array $homerooms): array
    {
        $classConfig = [
            ['10', 'IPA'],
            ['11', 'IPS'],
            ['12', 'IPA'],
        ];

        $classes = [];

        foreach ($classConfig as [$tingkat, $jurusan]) {
            $classes[] = Kelas::create([
                'nama' => "{$tingkat} {$jurusan}",
                'tingkat' => $tingkat,
                'wali_kelas_id' => $homerooms[$tingkat]->id,
            ]);
        }

        return $classes;
    }

    /**
     * @param  array<int, Kelas>  $classes
     * @return array<int, ProfilSiswa>
     */
    private function createStudents(array $classes): array
    {
        $students = [];
        $sequence = 1;

        $names = [
            // Kelas 10 IPA
            'Aditya Pratama', 'Aisyah Nurhaliza', 'Akbar Maulana',
            // Kelas 11 IPS
            'Fitri Lestari', 'Galang Prasetyo', 'Gilang Ramadhan',
            // Kelas 12 IPA
            'Intan Pratiwi', 'Iqbal Firmansyah', 'Jihan Azzahra',
        ];

        $birthPlaces = ['Jakarta', 'Bandung', 'Bogor', 'Depok', 'Bekasi', 'Tangerang'];
        $streets = ['Jalan Melati Raya', 'Jalan Kenanga Indah', 'Jalan Cempaka Putih', 'Jalan Mawar Asri', 'Jalan Anggrek Timur', 'Jalan Flamboyan'];
        $occupations = ['Karyawan', 'Wiraswasta', 'Guru', 'Perawat', 'Pedagang', 'Pegawai Negeri'];

        $nameIndex = 0;

        foreach ($classes as $kelas) {
            for ($slot = 1; $slot <= 3; $slot++) {
                $nis = sprintf('%05d', $sequence);
                $nama = $names[$nameIndex] ?? "Siswa {$sequence}";
                $street = $streets[($sequence - 1) % count($streets)];

                $pengguna = Pengguna::create([
                    'nama' => $nama,
                    'email' => "siswa{$nis}@sentrisiswa.test",
                    'password' => Hash::make('password'),
                    'peran' => 'siswa',
                    'status' => 'registered',
                ]);

                $profil = ProfilSiswa::create([
                    'pengguna_id' => $pengguna->id,
                    'nisn' => sprintf('00%08d', $sequence),
                    'nis' => $nis,
                    'kelas_id' => $kelas->id,
                    'telepon' => "0812{$nis}",
                    'alamat' => $street.', Kelurahan Sentri',
                ]);

                BiodataSiswa::create([
                    'profil_siswa_id' => $profil->nisn,
                    'tempat_lahir' => $birthPlaces[($sequence - 1) % count($birthPlaces)],
                    'tanggal_lahir' => now()->subYears(16)->subDays($sequence)->toDateString(),
                    'jenis_kelamin' => $slot % 2 === 0 ? 'P' : 'L',
                    'agama' => 'Islam',
                    'status_keluarga' => 'Kandung',
                    'anak_ke' => $slot,
                    'asal_sekolah' => 'SMP Nusantara',
                    'tanggal_masuk' => now()->subYear()->startOfMonth()->toDateString(),
                    'nama_ayah' => "Bapak dari {$nama}",
                    'pekerjaan_ayah' => $occupations[($sequence - 1) % count($occupations)],
                    'nama_ibu' => "Ibu dari {$nama}",
                    'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                    'alamat_ortu' => $street.', Kelurahan Sentri',
                    'telepon_ortu' => "0821{$nis}",
                    'nama_wali' => "Wali dari {$nama}",
                    'pekerjaan_wali' => $occupations[$sequence % count($occupations)],
                    'alamat_wali' => $street.', Kelurahan Sentri',
                    'telepon_wali' => "0831{$nis}",
                ]);

                $photoPath = $this->generateStudentPhoto($nis, $nama);
                $profil->update(['foto' => $photoPath]);

                $students[] = $profil;
                $sequence++;
                $nameIndex++;
            }
        }

        return $students;
    }

    /** @param array<int, ProfilSiswa> $students */
    private function createAttendanceRecords(array $students): void
    {
        $dates = collect(range(0, 20))
            ->map(fn (int $d) => today()->subDays($d))
            ->filter(fn ($date) => $date->isWeekday())
            ->take(10)
            ->values();

        $patterns = [
            ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'terlambat', 'hadir', 'hadir'],
            ['terlambat', 'hadir', 'terlambat', 'hadir', 'terlambat', 'hadir', 'terlambat', 'hadir', 'hadir', 'terlambat'],
            ['izin', 'sakit', 'hadir', 'alpha', 'izin', 'hadir', 'sakit', 'hadir', 'izin', 'belum_absen'],
        ];

        foreach ($students as $idx => $student) {
            $pattern = $patterns[$idx % count($patterns)];
            foreach ($dates as $dayIdx => $date) {
                $status = $pattern[$dayIdx] ?? 'hadir';
                $selfiePath = null;

                if (in_array($status, ['hadir', 'terlambat'])) {
                    $selfiePath = $this->generateSelfiePhoto($student->nisn, $date->format('Y-m-d'));
                }

                Absensi::create([
                    'profil_siswa_id' => $student->nisn,
                    'tanggal' => $date->toDateString(),
                    'status' => $status,
                    'waktu_masuk' => match ($status) {
                        'hadir' => '06:45:00',
                        'terlambat' => '07:'.sprintf('%02d', 10 + ($idx % 15)).':00',
                        default => null,
                    },
                    'path_selfie' => $selfiePath,
                ]);
            }
        }
    }

    /** @param array<int, ProfilSiswa> $students */
    private function createViolationRecords(array $students): void
    {
        $violationTypes = JenisPelanggaran::orderBy('pengurangan_poin')->get();
        $recorders = Pengguna::whereIn('peran', ['wali_kelas', 'bk', 'kesiswaan'])->pluck('id');

        foreach (array_values($students) as $index => $student) {
            if ($index % 3 !== 0) {
                continue;
            }

            $jenis = $violationTypes[$index % $violationTypes->count()];
            $status = match ($index % 9) {
                0 => 'pending',
                3 => 'rejected',
                default => 'approved',
            };

            PelanggaranSiswa::create([
                'profil_siswa_id' => $student->nisn,
                'jenis_pelanggaran_id' => $jenis->id,
                'dicatat_oleh_id' => $recorders[$index % $recorders->count()],
                'tanggal_pelanggaran' => today()->subDays($index % 20)->toDateString(),
                'nama_pelanggaran' => $jenis->nama,
                'kategori_pelanggaran' => $jenis->kategori,
                'pengurangan_poin' => $jenis->pengurangan_poin,
                'catatan' => 'Data demo pelanggaran.',
                'status' => $status,
                'disetujui_oleh_id' => $status === 'pending' ? null : $recorders->last(),
                'disetujui_pada' => $status === 'pending' ? null : now(),
                'alasan_penolakan' => $status === 'rejected' ? 'Data demo ditolak.' : null,
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

        $light = imagecolorallocatealpha($img, 255, 255, 255, 80);
        imagefilledellipse($img, (int) ($width / 2), (int) ($height / 2), (int) ($width * 0.5), (int) ($height * 0.5), $light);

        imagejpeg($img, $absPath, 85);
        imagedestroy($img);
    }

    private function generateTeacherPhoto(int $userId, string $nama): string
    {
        $path = "photos/teachers/{$userId}.jpg";
        [$r, $g, $b] = self::$palette[ord($nama[0]) % count(self::$palette)];
        $this->generateImage(200, 200, $r, $g, $b, storage_path("app/public/{$path}"));

        return $path;
    }

    private function generateStudentPhoto(string $nis, string $nama): string
    {
        $path = "photos/students/{$nis}.jpg";
        [$r, $g, $b] = self::$palette[ord($nama[0]) % count(self::$palette)];
        $this->generateImage(300, 400, $r, $g, $b, storage_path("app/public/{$path}"));

        return $path;
    }

    private function generateSelfiePhoto(string $profileId, string $date): string
    {
        $path = "attendance-selfies/{$profileId}/{$date}-demo.jpg";
        $colorIdx = ((int) $profileId + (int) str_replace('-', '', $date)) % count(self::$palette);
        [$r, $g, $b] = self::$palette[$colorIdx];
        $this->generateImage(300, 400, $r, $g, $b, storage_path("app/public/{$path}"));

        return $path;
    }

    private function createSchoolRule(): void
    {
        $path = 'school-rules/tata-tertib-demo.pdf';
        Storage::disk('public')->put($path, "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF");

        TataTertib::create([
            'judul' => 'Tata Tertib Sekolah Demo',
            'path_file' => $path,
            'dipublikasikan' => true,
            'diunggah_oleh_id' => Pengguna::where('peran', 'kesiswaan')->value('id'),
        ]);
    }
}
