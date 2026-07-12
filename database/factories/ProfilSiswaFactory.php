<?php

namespace Database\Factories;

use App\Models\ProfilSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfilSiswa>
 */
class ProfilSiswaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nisn' => fake()->unique()->numerify('##########'),
            'nis' => fake()->unique()->numerify('#####'),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'kelas_id' => null,
            'telepon' => null,
            'alamat' => null,
        ];
    }
}
