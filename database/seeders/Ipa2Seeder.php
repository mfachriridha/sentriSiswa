<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Ipa2Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Wali Kelas
        $walasUser = Pengguna::updateOrCreate(
            ['email' => 'walasipa2@sentrisiswa.test'],
            [
                'nama' => 'Budi Santoso, S.Pd.',
                'password' => Hash::make('password123'),
                'peran' => 'wali_kelas',
                'status' => 'registered',
            ]
        );
        $walasUser->profilGuru()->updateOrCreate(
            ['pengguna_id' => $walasUser->id],
            [
                'nip' => '198501012010011001',
                'tipe_guru' => 'wali_kelas',
                'tingkat' => '10',
            ]
        );

        // 2. Buat / Dapatkan Kelas 10 IPA 2 & tautkan wali kelas
        $kelas = Kelas::updateOrCreate(
            ['nama' => '10 IPA 2'],
            [
                'tingkat' => '10',
                'wali_kelas_id' => $walasUser->id,
            ]
        );

        // 3. Akun Kesiswaan
        $kesiswaanUser = Pengguna::updateOrCreate(
            ['email' => 'kesiswaan@sentrisiswa.test'],
            [
                'nama' => 'Drs. H. Ahmad Dahlan',
                'password' => Hash::make('password123'),
                'peran' => 'kesiswaan',
                'status' => 'registered',
            ]
        );
        $kesiswaanUser->profilGuru()->updateOrCreate(
            ['pengguna_id' => $kesiswaanUser->id],
            [
                'nip' => '197505052000031002',
                'tipe_guru' => 'kesiswaan',
            ]
        );

        // 4. Akun BK Tingkat 10
        $bkUser = Pengguna::updateOrCreate(
            ['email' => 'bk10@sentrisiswa.test'],
            [
                'nama' => 'Siti Rahmawati, S.Psi.',
                'password' => Hash::make('password123'),
                'peran' => 'bk',
                'status' => 'registered',
            ]
        );
        $bkUser->profilGuru()->updateOrCreate(
            ['pengguna_id' => $bkUser->id],
            [
                'nip' => '198812122015022003',
                'tipe_guru' => 'bk',
                'tingkat' => '10',
            ]
        );

        // 5. Data 4 Siswa (Terdaftar, Nama Asli)
        $siswaList = [
            [
                'email' => 'siswa01@sentrisiswa.test',
                'nama' => 'Andi Pratama',
                'nisn' => '1000000001',
                'nis' => '10001',
                'jenis_kelamin' => 'L',
                'pengurangan_poin' => 0,
            ],
            [
                'email' => 'siswa02@sentrisiswa.test',
                'nama' => 'Bunga Lestari',
                'nisn' => '1000000002',
                'nis' => '10002',
                'jenis_kelamin' => 'P',
                'pengurangan_poin' => 5,
                'alasan_poin' => 'Terlambat Masuk Sekolah',
            ],
            [
                'email' => 'siswa03@sentrisiswa.test',
                'nama' => 'Citra Dewi',
                'nisn' => '1000000003',
                'nis' => '10003',
                'jenis_kelamin' => 'P',
                'pengurangan_poin' => 15,
                'alasan_poin' => 'Tidak Mengikuti Apel Pagi & Seragam Tidak Lengkap',
            ],
            [
                'email' => 'siswa04@sentrisiswa.test',
                'nama' => 'Doni Wijaya',
                'nisn' => '1000000004',
                'nis' => '10004',
                'jenis_kelamin' => 'L',
                'pengurangan_poin' => 25,
                'alasan_poin' => 'Keluar Lingkungan Sekolah Tanpa Izin',
            ],
        ];

        $profilSiswaModels = [];

        foreach ($siswaList as $sData) {
            $user = Pengguna::updateOrCreate(
                ['email' => $sData['email']],
                [
                    'nama' => $sData['nama'],
                    'password' => Hash::make('password123'),
                    'peran' => 'siswa',
                    'status' => 'registered',
                ]
            );

            $profil = ProfilSiswa::updateOrCreate(
                ['nisn' => $sData['nisn']],
                [
                    'pengguna_id' => $user->id,
                    'nis' => $sData['nis'],
                    'kelas_id' => $kelas->id,
                    'jenis_kelamin' => $sData['jenis_kelamin'],
                ]
            );

            $profilSiswaModels[$sData['nisn']] = $profil;

            // Jika ada pengurangan poin, catat pelanggaran terkonfirmasi agar sisa poin beragam
            if ($sData['pengurangan_poin'] > 0) {
                PelanggaranSiswa::updateOrCreate(
                    [
                        'profil_siswa_id' => $sData['nisn'],
                        'nama_pelanggaran' => $sData['alasan_poin'],
                    ],
                    [
                        'dicatat_oleh_id' => $kesiswaanUser->id,
                        'tanggal_pelanggaran' => Carbon::today()->subDays(5)->format('Y-m-d'),
                        'kategori_pelanggaran' => 'ringan',
                        'pengurangan_poin' => $sData['pengurangan_poin'],
                        'status' => 'approved',
                        'disetujui_oleh_id' => $kesiswaanUser->id,
                        'disetujui_pada' => Carbon::now(),
                    ]
                );
            }
        }

        // 6. Generate Absensi 15 Hari Terakhir (Senin - Jumat) dengan Variasi Status
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(20);

        $workDays = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            if (! $curr->isWeekend()) {
                $workDays[] = $curr->format('Y-m-d');
            }
            $curr->addDay();
        }

        $workDays = array_slice($workDays, -15);

        // Distribusi Status Kehadiran Per Siswa
        // Siswa 1 (Andi): 0 Alpha
        // Siswa 2 (Bunga): 1 Alpha
        // Siswa 3 (Citra): 2 Alpha
        // Siswa 4 (Doni): 3 Alpha
        $pattern = [
            '1000000001' => ['hadir', 'hadir', 'izin', 'hadir', 'hadir', 'dispensasi', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
            '1000000002' => ['hadir', 'hadir', 'sakit', 'hadir', 'alpha', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
            '1000000003' => ['hadir', 'alpha', 'hadir', 'izin', 'hadir', 'sakit', 'hadir', 'alpha', 'hadir', 'izin', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
            '1000000004' => ['alpha', 'hadir', 'sakit', 'hadir', 'alpha', 'dispensasi', 'hadir', 'alpha', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
        ];

        foreach ($workDays as $idx => $tanggalStr) {
            foreach ($profilSiswaModels as $nisn => $profil) {
                $statusArr = $pattern[$nisn] ?? ['hadir'];
                $status = $statusArr[$idx % count($statusArr)];

                Absensi::updateOrCreate(
                    [
                        'profil_siswa_id' => $nisn,
                        'tanggal' => $tanggalStr,
                    ],
                    [
                        'status' => $status,
                        'waktu_masuk' => $status === 'hadir' ? '06:45:00' : null,
                    ]
                );
            }
        }
    }
}
