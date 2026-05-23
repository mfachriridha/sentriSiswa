<?php

namespace Database\Factories;

use App\Models\StudentProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentProfile>
 */
class StudentProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nisn' => null,
            'nis' => null,
            'class_id' => null,
            'phone' => null,
            'address' => null,
        ];
    }
}
