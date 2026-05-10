<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@sentrisiswa.sch.id'],
            [
                'name' => 'Admin Kesiswaan',
                'password' => 'admin123',
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'siswa@sentrisiswa.sch.id'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => 'siswa123',
                'role' => 'siswa',
            ]
        );
    }
}