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
            'nisn' => null,
            'nis' => null,
            'kelas_id' => null,
            'telepon' => null,
            'alamat' => null,
        ];
    }
}
