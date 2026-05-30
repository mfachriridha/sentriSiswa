<?php

namespace Database\Factories;

use App\Models\ViolationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ViolationType>
 */
class ViolationTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = fake()->randomElement(array_keys(ViolationType::categoryRanges()));
        [$minimumPoint, $maximumPoint] = ViolationType::categoryRanges()[$category];

        return [
            'name' => fake()->unique()->sentence(3),
            'category' => $category,
            'point_deduction' => fake()->numberBetween($minimumPoint, $maximumPoint),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
