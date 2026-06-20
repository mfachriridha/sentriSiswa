<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $waliKelas = User::where('peran', User::PERAN_WALI_KELAS)->first();

        Kelas::create([
            'nama' => 'X IPA 1',
            'tingkat' => '10',
            'wali_kelas_id' => $waliKelas?->id,
        ]);

        Kelas::create([
            'nama' => 'XI IPA 1',
            'tingkat' => '11',
            'wali_kelas_id' => $waliKelas?->id,
        ]);

        Kelas::create([
            'nama' => 'XII IPA 1',
            'tingkat' => '12',
            'wali_kelas_id' => null,
        ]);
    }
}
