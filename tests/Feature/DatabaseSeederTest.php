<?php

use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Database Seeder (baseline)
|--------------------------------------------------------------------------
|
| Seeder default cuma menyiapkan akun tetap yang gak tergantung data impor:
| admin, satu BK per tingkat, dan kesiswaan - plus katalog jenis pelanggaran.
| Siswa, kelas, dan wali kelas baru muncul lewat impor data asli, lalu
| dihidupkan oleh SekolahAktifSeeder (lihat SekolahAktifSeederTest).
|
*/

test('database seeder membuat akun admin, BK per tingkat, dan kesiswaan', function () {
    $this->seed();

    expect(Pengguna::where('email', 'admin@sentrisiswa.test')->where('peran', 'admin')->exists())->toBeTrue();

    foreach (['10', '11', '12'] as $tingkat) {
        expect(
            Pengguna::where('email', "kls{$tingkat}bk@sentrisiswa.test")
                ->where('peran', 'bk')
                ->whereHas('profilGuru', fn ($query) => $query->where('tingkat', $tingkat))
                ->exists()
        )->toBeTrue();
    }
    expect(Pengguna::where('peran', 'bk')->count())->toBe(3);

    expect(Pengguna::where('email', 'kesiswaan@sentrisiswa.test')->where('peran', 'kesiswaan')->exists())->toBeTrue();

    expect(JenisPelanggaran::count())->toBeGreaterThan(0);
});

test('database seeder tidak membuat wali kelas, kelas, atau siswa apa pun', function () {
    $this->seed();

    expect(Pengguna::where('peran', 'wali_kelas')->count())->toBe(0);
    expect(Kelas::count())->toBe(0);
    expect(ProfilSiswa::count())->toBe(0);
});
