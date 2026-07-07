<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\PengajuanPoin;
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
        $this->createPengajuanPoinRecords($classes);
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
            $tingkat = (string) $tingkat;
            $guru = Pengguna::create([
                'nama' => $nama,
                'email' => "wali{$tingkat}@sentrisiswa.test",
                'password' => Hash::make('password'),
                'peran' => 'wali_kelas',
                'status' => 'registered',
            ]);

            $profil = $guru->profilGuru()->create([
                'nip' => sprintf('197%02d0000000000', $nip++),
                'telepon' => sprintf('000000%05d', $nip),
                'tipe_guru' => 'wali_kelas',
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
                'telepon' => "00000{$tingkat}000000",
                'tipe_guru' => 'bk',
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
            'telepon' => '0000099000000',
            'tipe_guru' => 'kesiswaan',
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

        $streets = ['Jalan Melati Raya', 'Jalan Kenanga Indah', 'Jalan Cempaka Putih', 'Jalan Mawar Asri', 'Jalan Anggrek Timur', 'Jalan Flamboyan'];

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
                    'telepon' => "0000{$nis}",
                    'alamat' => $street.', Kelurahan Sentri',
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
        // 45 hari kalender ke belakang menjamin bulan berjalan (dan bulan
        // sebelumnya) selalu keisi penuh, gak peduli tanggal berapa seeder
        // ini dijalankan.
        $dates = collect(range(0, 45))
            ->map(fn (int $d) => today()->subDays($d))
            ->filter(fn ($date) => $date->isWeekday())
            ->sortBy(fn ($date) => $date->toDateString())
            ->values();

        $patterns = [
            // Rajin: nyaris selalu hadir, sesekali terlambat.
            ['hadir', 'hadir', 'hadir', 'hadir', 'terlambat', 'hadir', 'hadir'],
            // Sering terlambat.
            ['terlambat', 'hadir', 'terlambat', 'hadir', 'terlambat', 'hadir', 'hadir'],
            // Rawan: campuran izin/sakit/alpha, alpha cukup sering biar kena ambang batas (>=3).
            ['izin', 'sakit', 'alpha', 'hadir', 'alpha', 'izin', 'alpha', 'sakit', 'hadir', 'terlambat'],
        ];

        $today = today()->toDateString();

        foreach ($students as $idx => $student) {
            $pattern = $patterns[$idx % count($patterns)];

            foreach ($dates as $date) {
                $dayIdx = $date->dayOfYear;
                $status = $pattern[$dayIdx % count($pattern)];

                // Hari ini disamaratakan lintas pola biar dashboard/monitoring
                // "hari ini" kelihatan hidup: ada yang udah hadir, ada yang
                // masih belum absen.
                if ($date->toDateString() === $today) {
                    $status = match ($idx % 3) {
                        0 => 'hadir',
                        1 => 'terlambat',
                        default => 'belum_absen',
                    };
                }

                $selfiePath = null;

                if (in_array($status, ['hadir', 'terlambat'], true)) {
                    $selfiePath = $this->generateSelfiePhoto($student->nisn, $date->toDateString());
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
        $recorders = Pengguna::whereIn('peran', ['wali_kelas', 'bk', 'kesiswaan'])->pluck('id');
        $students = array_values($students);

        // Sengaja beragam: nyentuh ke-4 kategori pelanggaran, ke-3 status,
        // dan beberapa siswa dibiarin bersih buat kontras. Dua entri
        // pending/rejected cuma buat demo tampilan status filter/badge -
        // di alur sekarang kesiswaan langsung approved, tapi data lama
        // dengan status itu tetap valid untuk ditampilkan (lihat CLAUDE.md).
        $plan = [
            ['student' => 0, 'nama' => 'Penampilan tidak rapi', 'status' => 'approved'],
            ['student' => 2, 'nama' => 'Anggota tubuh bertato', 'status' => 'approved'],
            ['student' => 2, 'nama' => 'Salah Kostum', 'status' => 'approved'],
            ['student' => 3, 'nama' => 'Ber make up', 'status' => 'pending'],
            ['student' => 5, 'nama' => 'Mesum di sekolah', 'status' => 'approved'],
            ['student' => 6, 'nama' => 'Terlibat Narkoba', 'status' => 'approved'],
            ['student' => 7, 'nama' => 'Melakukan penghinaan', 'status' => 'rejected'],
        ];

        foreach ($plan as $i => $item) {
            $student = $students[$item['student']] ?? null;
            $jenis = JenisPelanggaran::where('nama', $item['nama'])->first();

            if (! $student || ! $jenis) {
                continue;
            }

            $status = $item['status'];

            PelanggaranSiswa::create([
                'profil_siswa_id' => $student->nisn,
                'jenis_pelanggaran_id' => $jenis->id,
                'dicatat_oleh_id' => $recorders[$i % $recorders->count()],
                'tanggal_pelanggaran' => today()->subDays(($i + 1) * 3)->toDateString(),
                'nama_pelanggaran' => $jenis->nama,
                'kategori_pelanggaran' => $jenis->kategori,
                'pengurangan_poin' => $jenis->pengurangan_poin,
                'catatan' => 'Data demo pelanggaran.',
                'status' => $status,
                'disetujui_oleh_id' => $status === 'pending' ? null : $recorders->last(),
                'disetujui_pada' => $status === 'pending' ? null : now(),
                'alasan_penolakan' => $status === 'rejected' ? 'Belum ada bukti pendukung, mohon lengkapi laporan.' : null,
            ]);
        }
    }

    /** @param array<int, Kelas> $classes */
    private function createPengajuanPoinRecords(array $classes): void
    {
        $kesiswaan = Pengguna::where('peran', 'kesiswaan')->first();

        // 1 pengajuan per kelas, cycling lewat status pending/approved/rejected
        // biar kesiswaan (antrean persetujuan) dan wali kelas (riwayat sendiri)
        // sama-sama punya data buat dilihat.
        $plan = [
            ['status' => 'pending', 'alasan' => 'Aktif membantu perpustakaan sekolah selama seminggu.'],
            ['status' => 'approved', 'alasan' => 'Juara 1 lomba debat tingkat kota, mengharumkan nama sekolah.', 'jumlah_poin' => 15],
            ['status' => 'rejected', 'alasan' => 'Menolong teman yang jatuh saat upacara.', 'alasan_penolakan' => 'Belum ada bukti pendukung, mohon lengkapi laporan.'],
        ];

        foreach (array_values($classes) as $idx => $kelas) {
            $waliKelas = $kelas->waliKelas;
            $student = $kelas->siswa()->first();

            if (! $waliKelas || ! $student) {
                continue;
            }

            $item = $plan[$idx % count($plan)];
            $isPending = $item['status'] === 'pending';

            PengajuanPoin::create([
                'profil_siswa_id' => $student->nisn,
                'diajukan_oleh_id' => $waliKelas->id,
                'alasan' => $item['alasan'],
                'status' => $item['status'],
                'jumlah_poin' => $item['jumlah_poin'] ?? null,
                'disetujui_oleh_id' => $isPending ? null : $kesiswaan?->id,
                'disetujui_pada' => $isPending ? null : now(),
                'alasan_penolakan' => $item['alasan_penolakan'] ?? null,
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
