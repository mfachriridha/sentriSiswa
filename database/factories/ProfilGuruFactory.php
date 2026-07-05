<?php

namespace Database\Factories;

use App\Models\ProfilGuru;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfilGuru>
 */
class ProfilGuruFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nip' => fake()->unique()->numerify('19##########'),
            'telepon' => null,
            'tingkat' => null,
        ];
    }

    public function homeroom(): static
    {
        return $this->state(fn (array $attributes) => [
            'nip' => fake()->unique()->numerify('19##########'),
            'telepon' => fake()->numerify('08##########'),
            'tipe_guru' => 'wali_kelas',
            'tingkat' => null,
        ]);
    }

    public function counselor(): static
    {
        return $this->state(fn (array $attributes) => [
            'nip' => fake()->unique()->numerify('19##########'),
            'telepon' => fake()->numerify('08##########'),
            'tipe_guru' => 'bk',
            'tingkat' => fake()->randomElement(['10', '11', '12']),
        ]);
    }
}
