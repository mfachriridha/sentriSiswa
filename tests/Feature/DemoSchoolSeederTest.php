<?php

use App\Models\BiodataSiswa;
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
    expect(BiodataSiswa::pluck('nama_ayah')->filter(fn (string $name): bool => preg_match('/\d/', $name) === 1)->count())->toBe(0);
    expect(BiodataSiswa::pluck('nama_ibu')->filter(fn (string $name): bool => preg_match('/\d/', $name) === 1)->count())->toBe(0);
    expect(BiodataSiswa::pluck('nama_wali')->filter(fn (string $name): bool => preg_match('/\d/', $name) === 1)->count())->toBe(0);
});

test('demo school seeder creates complete biodata for every student', function () {
    $this->seed();

    expect(BiodataSiswa::count())->toBe(9);
    expect(BiodataSiswa::get()->every(fn (BiodataSiswa $biodata): bool => filled($biodata->tempat_lahir)
        && filled($biodata->tanggal_lahir)
        && filled($biodata->jenis_kelamin)
        && filled($biodata->agama)
        && filled($biodata->status_keluarga)
        && filled($biodata->anak_ke)
        && filled($biodata->asal_sekolah)
        && filled($biodata->tanggal_masuk)
        && filled($biodata->nama_ayah)
        && filled($biodata->pekerjaan_ayah)
        && filled($biodata->nama_ibu)
        && filled($biodata->pekerjaan_ibu)
        && filled($biodata->alamat_ortu)
        && filled($biodata->telepon_ortu)
        && filled($biodata->nama_wali)
        && filled($biodata->pekerjaan_wali)
        && filled($biodata->alamat_wali)
        && filled($biodata->telepon_wali)
    ))->toBeTrue();
});
