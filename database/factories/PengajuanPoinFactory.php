<?php

namespace Database\Factories;

use App\Models\PengajuanPoin;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengajuanPoin>
 */
class PengajuanPoinFactory extends Factory
{
    public function definition(): array
    {
        return [
            'profil_siswa_id' => function (): string {
                $student = Pengguna::factory()->student()->create([
                    'status' => 'registered',
                ]);

                return ProfilSiswa::factory()->create([
                    'pengguna_id' => $student->id,
                ])->nisn;
            },
            'diajukan_oleh_id' => Pengguna::factory()->homeroom(),
            'alasan' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}
