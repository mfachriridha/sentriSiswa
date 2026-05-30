<?php

namespace Database\Factories;

use App\Models\StudentProfile;
use App\Models\StudentViolation;
use App\Models\User;
use App\Models\ViolationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentViolation>
 */
class StudentViolationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $violationType = ViolationType::factory()->create();

        return [
            'student_profile_id' => function (): int {
                $student = User::factory()->student()->create([
                    'status' => 'registered',
                ]);

                return StudentProfile::factory()->create([
                    'user_id' => $student->id,
                    'nisn' => fake()->unique()->numerify('##########'),
                    'nis' => fake()->unique()->numerify('#####'),
                ])->id;
            },
            'violation_type_id' => $violationType->id,
            'recorded_by_user_id' => User::factory()->homeroom(),
            'violation_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'violation_name' => $violationType->name,
            'violation_category' => $violationType->category,
            'point_deduction' => $violationType->point_deduction,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
