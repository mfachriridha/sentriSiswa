<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@sentrisiswa.test',
            'status' => 'registered',
        ]);

        $this->call([
            ViolationTypeSeeder::class,
            DemoSchoolSeeder::class,
        ]);
    }
}
