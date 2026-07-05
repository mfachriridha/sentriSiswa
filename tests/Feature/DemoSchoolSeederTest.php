<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use App\Models\TataTertib;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::statement('PRAGMA ignore_check_constraints = ON');
});

afterEach(function () {
    DB::statement('PRAGMA ignore_check_constraints = OFF');
});

test('demo school seeder creates requested actor and class composition', function () {
    $this->seed();

    expect(Pengguna::where('peran', 'admin')->count())->toBe(1);
    expect(Pengguna::where('peran', 'wali_kelas')->whereHas('profilGuru', fn ($query) => $query->where('tipe_guru', 'wali_kelas'))->count())->toBe(3);
    expect(Pengguna::where('peran', 'bk')->whereHas('profilGuru', fn ($query) => $query->where('tipe_guru', 'bk'))->count())->toBe(3);
    expect(Pengguna::where('peran', 'kesiswaan')->whereHas('profilGuru', fn ($query) => $query->where('tipe_guru', 'kesiswaan'))->count())->toBe(1);

    expect(Kelas::count())->toBe(3);
    expect(Kelas::where('tingkat', '10')->count())->toBe(1);
    expect(Kelas::where('tingkat', '11')->count())->toBe(1);
    expect(Kelas::where('tingkat', '12')->count())->toBe(1);

    expect(ProfilSiswa::count())->toBe(9);
    expect(Pengguna::where('peran', 'siswa')->where('status', 'registered')->count())->toBe(9);
    expect(Pengguna::where('peran', 'siswa')->where('status', 'unregistered')->count())->toBe(0);
    expect(Kelas::withCount('siswa')->get()->every(fn (Kelas $class): bool => $class->siswa_count === 3))->toBeTrue();

    expect(TataTertib::where('dipublikasikan', true)->count())->toBe(1);
});

test('demo school seeder uses human names without numbers', function () {
    $this->seed();

    expect(Pengguna::pluck('nama')->filter(fn (string $name): bool => preg_match('/\d/', $name) === 1)->count())->toBe(0);
});
