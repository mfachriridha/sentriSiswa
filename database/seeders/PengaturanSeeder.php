<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        Pengaturan::simpan('waktu_mulai', '06:30');
        Pengaturan::simpan('waktu_selesai', '07:00');
        Pengaturan::simpan('toleransi_terlambat', '15');
        Pengaturan::simpan('toleransi_meter', '50');
    }
}
