<?php

namespace Database\Seeders;

use App\Models\BiodataSiswa;
use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Database\Seeder;

class DemoStudentBiodataSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::first();

        $pengguna = Pengguna::create([
            'nama' => 'Siti Nurhaliza',
            'email' => 'siti.demo@sentrisiswa.test',
            'password' => bcrypt('password'),
            'peran' => 'siswa',
        ]);

        $profil = ProfilSiswa::create([
            'pengguna_id' => $pengguna->id,
            'nisn' => '0012345678',
            'nis' => '20240001',
            'kelas_id' => $kelas->id,
            'telepon' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 45, Jakarta Selatan',
        ]);

        BiodataSiswa::create([
            'profil_siswa_id' => $profil->id,
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2008-05-15',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'status_keluarga' => 'Kandung',
            'anak_ke' => 2,
            'asal_sekolah' => 'SMP Negeri 1 Jakarta',
            'tanggal_masuk' => '2024-07-15',
            'nama_ayah' => 'Budi Santoso',
            'pekerjaan_ayah' => 'Pegawai Negeri',
            'nama_ibu' => 'Sari Dewi',
            'pekerjaan_ibu' => 'Guru',
            'alamat_ortu' => 'Jl. Merdeka No. 45, Jakarta Selatan',
            'telepon_ortu' => '081298765432',
            'nama_wali' => null,
            'pekerjaan_wali' => null,
            'alamat_wali' => null,
            'telepon_wali' => null,
        ]);

        $this->command->info("Demo student created: {$pengguna->nama} (ID: {$pengguna->id})");
    }
}
