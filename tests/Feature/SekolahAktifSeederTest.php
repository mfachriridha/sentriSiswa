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
    $alphaTerbanyak = Absensi::where('status', 'alpha')
        ->selectRaw('profil_siswa_id, COUNT(*) as jumlah')
        ->groupBy('profil_siswa_id')
        ->orderByDesc('jumlah')
        ->value('jumlah');
    expect((int) $alphaTerbanyak)->toBeGreaterThanOrEqual(3);

    // Sebagian siswa punya pelanggaran, sebagian sengaja dibiarkan bersih.
    $siswaBerpelanggaran = PelanggaranSiswa::distinct('profil_siswa_id')->count('profil_siswa_id');
    expect($siswaBerpelanggaran)->toBeGreaterThan(0)
        ->and($siswaBerpelanggaran)->toBeLessThan($siswa->count());

    // Antrean persetujuan kesiswaan terisi, dan riwayatnya beragam.
    expect(PengajuanPoin::where('status', 'pending')->exists())->toBeTrue()
        ->and(PengajuanPoin::where('status', 'approved')->exists())->toBeTrue();
});

test('seeder menghidupkan semua kelas, tidak ada yang dilewati', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , , $siswaKelasKedua] = sekolahSebelumDihidupkan();

    $this->seed(SekolahAktifSeeder::class);

    $nisnKelasKedua = $siswaKelasKedua->pluck('nisn');

    $terdaftar = Pengguna::whereIn('id', $siswaKelasKedua->pluck('pengguna_id'))
        ->where('status', 'registered')
        ->count();

    expect($terdaftar)->toBe($siswaKelasKedua->count())
        ->and(Absensi::whereIn('profil_siswa_id', $nisnKelasKedua)->exists())->toBeTrue();
});

test('seeder yang dijalankan dua kali tidak menggandakan data', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    sekolahSebelumDihidupkan();

    $this->seed(SekolahAktifSeeder::class);

    $sesudahSekali = [
        'siswa' => ProfilSiswa::count(),
        'absensi' => Absensi::count(),
        'pelanggaran' => PelanggaranSiswa::count(),
        'pengajuan' => PengajuanPoin::count(),
    ];

    $this->seed(SekolahAktifSeeder::class);

    expect(ProfilSiswa::count())->toBe($sesudahSekali['siswa'])
        ->and(Absensi::count())->toBe($sesudahSekali['absensi'])
        ->and(PelanggaranSiswa::count())->toBe($sesudahSekali['pelanggaran'])
        ->and(PengajuanPoin::count())->toBe($sesudahSekali['pengajuan']);
});
