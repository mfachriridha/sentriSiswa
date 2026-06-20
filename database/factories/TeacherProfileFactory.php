<?php

namespace Database\Factories;

use App\Models\TeacherProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeacherProfile>
 */
class TeacherProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nip' => null,
            'phone' => null,
            'grade' => null,
        ];
    }

    public function homeroom(): static
    {
        return $this->state(fn (array $attributes) => [
            'nip' => fake()->unique()->numerify('19##########'),
            'phone' => fake()->numerify('08##########'),
            'grade' => null,
        ]);
    }

    public function counselor(): static
    {
        return $this->state(fn (array $attributes) => [
            'nip' => fake()->unique()->numerify('19##########'),
            'phone' => fake()->numerify('08##########'),
            'grade' => fake()->randomElement(['10', '11', '12']),
        ]);
    }
}
