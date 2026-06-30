<?php

namespace Database\Factories;

use App\Models\JenisPelanggaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JenisPelanggaran>
 */
class JenisPelanggaranFactory extends Factory
{
    public function definition(): array
    {
        $kategori = fake()->randomElement(array_keys(JenisPelanggaran::categoryRanges()));
        [$min, $max] = JenisPelanggaran::categoryRanges()[$kategori];

        return [
            'nama' => fake()->unique()->sentence(3),
            'kategori' => $kategori,
            'pengurangan_poin' => fake()->numberBetween($min, $max),
            'keterangan' => fake()->optional()->sentence(),
            'aktif' => true,
        ];
    }
}
