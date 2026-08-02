<?php

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

test('database seeder membuat akun admin dan seeder demo ipa2', function () {
    $this->seed();

    expect(Pengguna::where('email', 'admin@sentrisiswa.test')->where('peran', 'admin')->exists())->toBeTrue();
    expect(Pengguna::where('email', 'walasipa2@sentrisiswa.test')->exists())->toBeTrue();
    expect(Pengguna::where('email', 'bk10@sentrisiswa.test')->exists())->toBeTrue();
    expect(Pengguna::where('email', 'kesiswaan@sentrisiswa.test')->exists())->toBeTrue();

    expect(Kelas::where('nama', '10 IPA 2')->exists())->toBeTrue();
    expect(ProfilSiswa::count())->toBe(4);
});
