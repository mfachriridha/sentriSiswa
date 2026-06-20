<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PenggunaSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,
            JenisPelanggaranSeeder::class,
            PengaturanSeeder::class,
        ]);
    }
}
