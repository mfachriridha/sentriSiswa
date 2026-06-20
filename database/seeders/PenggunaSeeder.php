<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'nama' => 'Administrator',
            'email' => 'admin@sentrisiswa.test',
            'password' => Hash::make('password'),
            'peran' => User::PERAN_ADMIN,
            'status' => User::STATUS_TERDAFTAR,
        ]);

        $waliKelas = User::create([
            'nama' => 'Budi Wali Kelas',
            'email' => 'wali@sentrisiswa.test',
            'password' => Hash::make('password'),
            'peran' => User::PERAN_WALI_KELAS,
            'status' => User::STATUS_TERDAFTAR,
        ]);

        $bk = User::create([
            'nama' => 'Siti BK',
            'email' => 'bk@sentrisiswa.test',
            'password' => Hash::make('password'),
            'peran' => User::PERAN_BK,
            'status' => User::STATUS_TERDAFTAR,
        ]);

        $kesiswaan = User::create([
            'nama' => 'Andi Kesiswaan',
            'email' => 'kesiswaan@sentrisiswa.test',
            'password' => Hash::make('password'),
            'peran' => User::PERAN_KESISWAAN,
            'status' => User::STATUS_TERDAFTAR,
        ]);

        // Siswa belum terdaftar (harus daftar via NISN)
        $siswaUsers = [
            ['nama' => 'Ayu Lestari', 'email' => null],
            ['nama' => 'Bima Saputra', 'email' => null],
            ['nama' => 'Citra Dewi', 'email' => null],
            ['nama' => 'Dian Pratama', 'email' => null],
            ['nama' => 'Eka Putri', 'email' => null],
        ];

        foreach ($siswaUsers as $data) {
            User::create([
                'nama' => $data['nama'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'peran' => User::PERAN_SISWA,
                'status' => User::STATUS_BELUM_TERDAFTAR,
            ]);
        }
    }
}
