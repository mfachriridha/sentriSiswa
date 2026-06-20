<?php

namespace Database\Seeders;

use App\Models\BiodataSiswa;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::where('nama', 'X IPA 1')->first();

        $siswaUsers = User::where('peran', User::PERAN_SISWA)->get();

        $dataSiswa = [
            ['nisn' => '1234567890', 'nis' => '10001'],
            ['nisn' => '1234567891', 'nis' => '10002'],
            ['nisn' => '1234567892', 'nis' => '10003'],
            ['nisn' => '1234567893', 'nis' => '10004'],
            ['nisn' => '1234567894', 'nis' => '10005'],
        ];

        foreach ($siswaUsers as $index => $pengguna) {
            $data = $dataSiswa[$index] ?? ['nisn' => null, 'nis' => null];

            $siswa = Siswa::create([
                'pengguna_id' => $pengguna->id,
                'nisn' => $data['nisn'],
                'nis' => $data['nis'],
                'kelas_id' => $kelas?->id,
                'telepon' => null,
                'alamat' => null,
                'foto' => null,
            ]);

            BiodataSiswa::create([
                'siswa_id' => $siswa->id,
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2008-01-15',
                'jenis_kelamin' => $index % 2 === 0 ? 'P' : 'L',
                'agama' => 'Islam',
            ]);
        }
    }
}
