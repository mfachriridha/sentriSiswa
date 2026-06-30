<?php

namespace Database\Factories;

use App\Models\JenisPelanggaran;
use App\Models\PelanggaranSiswa;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PelanggaranSiswa>
 */
class PelanggaranSiswaFactory extends Factory
{
    public function definition(): array
    {
        $jenisPelanggaran = JenisPelanggaran::factory()->create();

        return [
            'profil_siswa_id' => function (): int {
                $student = Pengguna::factory()->student()->create([
                    'status' => 'registered',
                ]);

                return ProfilSiswa::factory()->create([
                    'pengguna_id' => $student->id,
                    'nisn' => fake()->unique()->numerify('##########'),
                    'nis' => fake()->unique()->numerify('#####'),
                ])->id;
            },
            'jenis_pelanggaran_id' => $jenisPelanggaran->id,
            'dicatat_oleh_id' => Pengguna::factory()->homeroom(),
            'tanggal_pelanggaran' => fake()->dateTimeBetween('-1 month', 'now'),
            'nama_pelanggaran' => $jenisPelanggaran->nama,
            'kategori_pelanggaran' => $jenisPelanggaran->kategori,
            'pengurangan_poin' => $jenisPelanggaran->pengurangan_poin,
            'catatan' => fake()->optional()->sentence(),
            'status' => 'approved',
            'disetujui_pada' => now(),
        ];
    }
}
