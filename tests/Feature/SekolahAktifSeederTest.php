<?php

use App\Models\Absensi;
use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\PengajuanPoin;
use App\Models\Pengaturan;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Database\Seeders\SekolahAktifSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Seeder Sekolah Aktif
|--------------------------------------------------------------------------
|
| Seeder ini menghidupkan data sekolah yang sudah diimpor: siswa didaftarkan,
| absensi sebulan terakhir dibuat, sebagian siswa diberi pelanggaran, dan antrean
| pengajuan poin diisi.
|
| Janji yang harus dipegang: seeder tidak pernah menghapus apa pun, dan
| SEMUA kelas ikut dihidupkan - tidak ada kelas yang sengaja dilewati.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

/** Sekolah kecil dengan dua kelas biasa, keduanya harus ikut dihidupkan. */
function sekolahSebelumDihidupkan(): array
{
    $kesiswaan = Pengguna::factory()->studentAffairs()->create(['status' => 'registered']);

    $wali = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $kelasA = Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10', 'wali_kelas_id' => $wali->id]);
    $kelasB = Kelas::create(['nama' => '11 IPA 6', 'tingkat' => '11', 'wali_kelas_id' => $wali->id]);

    JenisPelanggaran::create([
        'nama' => 'Terlambat masuk kelas',
        'kategori' => 'ringan',
        'pengurangan_poin' => 10,
        'aktif' => true,
    ]);

    $buatSiswa = function (Kelas $kelas, int $nomor): ProfilSiswa {
        $pengguna = Pengguna::factory()->student()->create([
            'nama' => "Siswa {$nomor}",
            'status' => 'unregistered',
            'email' => null,
        ]);

        return ProfilSiswa::factory()->create([
            'pengguna_id' => $pengguna->id,
            'nisn' => str_pad((string) (1000000000 + $nomor), 10, '0', STR_PAD_LEFT),
            'nis' => (string) (10000 + $nomor),
            'kelas_id' => $kelas->id,
        ]);
    };

    $siswaA = collect(range(1, 24))->map(fn (int $n) => $buatSiswa($kelasA, $n));
    $siswaB = collect(range(101, 103))->map(fn (int $n) => $buatSiswa($kelasB, $n));

    return [$kesiswaan, $kelasA, $siswaA, $siswaB];
}

test('seeder mendaftarkan siswa dan mengisi absensi, pelanggaran, serta pengajuan poin', function () {
    Carbon::setTestNow('2026-07-10 08:00:00'); // Jumat.
    [, , $siswa] = sekolahSebelumDihidupkan();

    $this->seed(SekolahAktifSeeder::class);

    // Semua siswa di kelas biasa jadi punya akun.
    $belumDaftar = Pengguna::where('peran', 'siswa')
        ->whereIn('id', $siswa->pluck('pengguna_id'))
        ->where('status', '!=', 'registered')
        ->count();
    expect($belumDaftar)->toBe(0);

    // Absensinya terbentuk, dan hanya pada hari absensi aktif.
    $tanggalAbsensi = Absensi::pluck('tanggal')->unique();
    expect($tanggalAbsensi)->not->toBeEmpty();

    $tanggalDiLuarHariAbsen = $tanggalAbsensi->filter(
        fn ($tanggal): bool => ! Pengaturan::hariAbsenAktif(Carbon::parse($tanggal))
    );
    expect($tanggalDiLuarHariAbsen)->toBeEmpty();

    // Ada siswa yang alpha sampai menembus ambang peringatan (3 kali).
    $alphaTerbanyak = Presensi::where('status', 'alpha')
        ->selectRaw('profil_siswa_id, COUNT(*) as jumlah')
        ->groupBy('profil_siswa_id')
        ->orderByDesc('jumlah')
        ->value('jumlah');
    expect((int) $alphaTerbanyak)->toBeGreaterThanOrEqual(3);

    // Pelanggaran dan pengajuan poin terisi
    expect(PelanggaranSiswa::where('status', 'approved')->exists())->toBeTrue()
        ->and(PengajuanPoin::where('status', 'approved')->exists())->toBeTrue();
});

test('seeder sekolah aktif idempotent dan tidak menggandakan data saat dijalankan ulang', function () {
    Carbon::setTestNow('2026-07-15 08:00:00');

    $this->seed(SekolahAktifSeeder::class);

    $nisnKelasKedua = ProfilSiswa::where('kelas_id', '!=', Kelas::first()->id)->pluck('nisn');

    expect($nisnKelasKedua)->not->isEmpty()
        ->and(Presensi::whereIn('profil_siswa_id', $nisnKelasKedua)->exists())->toBeTrue();

    $sebelum = [
        'kelas' => Kelas::count(),
        'siswa' => ProfilSiswa::count(),
        'pengguna' => Pengguna::count(),
        'presensi' => Presensi::count(),
        'pelanggaran' => PelanggaranSiswa::count(),
        'poin' => PengajuanPoin::count(),
    ];

    $this->seed(SekolahAktifSeeder::class);

    $sesudahSekali = [
        'kelas' => Kelas::count(),
        'siswa' => ProfilSiswa::count(),
        'pengguna' => Pengguna::count(),
        'presensi' => Presensi::count(),
        'pelanggaran' => PelanggaranSiswa::count(),
        'poin' => PengajuanPoin::count(),
    ];

    expect($sesudahSekali)->toBe($sebelum);

    $this->seed(SekolahAktifSeeder::class);

    expect(Kelas::count())->toBe($sesudahSekali['kelas'])
        ->and(ProfilSiswa::count())->toBe($sesudahSekali['siswa'])
        ->and(Pengguna::count())->toBe($sesudahSekali['pengguna'])
        ->and(Presensi::count())->toBe($sesudahSekali['presensi'])
        ->and(PelanggaranSiswa::count())->toBe($sesudahSekali['pelanggaran'])
        ->and(PengajuanPoin::count())->toBe($sesudahSekali['poin']);
});
