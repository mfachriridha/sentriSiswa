<?php

namespace Database\Factories;

use App\Models\SchoolRule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolRule>
 */
class SchoolRuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => 'Tata Tertib Sekolah',
            'file_path' => 'school-rules/tata-tertib.pdf',
            'is_published' => true,
            'uploaded_by_user_id' => User::factory()->homeroom(),
        ];
    }
}
