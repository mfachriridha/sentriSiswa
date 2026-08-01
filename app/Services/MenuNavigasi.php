<?php

namespace App\Services;

use App\Models\PengajuanPoin;
use App\Models\Pengguna;

/**
 * Satu-satunya sumber daftar menu navigasi.
 *
 * Sidebar (desktop) dan bilah bawah (ponsel) sama-sama membaca dari sini, supaya
 * menambah atau mengubah menu cukup dikerjakan di satu tempat dan keduanya tidak
 * pernah berbeda isi.
 *
 * Urutan menu sekaligus menentukan prioritas: bilah bawah mengambil yang paling
 * depan, sisanya masuk ke lembar "Lainnya".
 */
class MenuNavigasi
{
    /** Sebanyak ini yang muat di bilah bawah tanpa bikin sasaran sentuhnya kekecilan. */
    public const MAKS_BILAH_BAWAH = 5;

    /**
     * @return list<array{label: string, route: string, icon: string, aktif: list<string>, grup: string, badge?: int}>
     */
    public static function untuk(Pengguna $pengguna): array
    {
        return match (true) {
            $pengguna->isAdmin() => self::admin(),
            $pengguna->isSiswa() => self::siswa(),
            $pengguna->isGuru() => self::guru($pengguna),
            default => [],
        };
    }

    /**
     * Menu yang tampil di bilah bawah. Kalau menunya lebih banyak dari muatan
     * bilah, satu slot terakhir disisakan untuk tombol "Lainnya".
     *
     * @param  list<array<string, mixed>>  $menu
     * @return list<array<string, mixed>>
     */
    public static function utama(array $menu): array
    {
        if (count($menu) <= self::MAKS_BILAH_BAWAH) {
            return $menu;
        }

        return array_slice($menu, 0, self::MAKS_BILAH_BAWAH - 1);
    }

    /**
     * Menu yang tidak kebagian tempat di bilah bawah, ditampilkan di lembar
     * "Lainnya".
     *
     * @param  list<array<string, mixed>>  $menu
     * @return list<array<string, mixed>>
     */
    public static function selebihnya(array $menu): array
    {
        if (count($menu) <= self::MAKS_BILAH_BAWAH) {
            return [];
        }

        return array_slice($menu, self::MAKS_BILAH_BAWAH - 1);
    }

    /** @return list<array<string, mixed>> */
    private static function admin(): array
    {
        return [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'dashboard', 'aktif' => ['admin.dashboard'], 'grup' => 'Utama'],
            ['label' => 'Guru', 'route' => 'admin.guru.index', 'icon' => 'guru', 'aktif' => ['admin.guru.*'], 'grup' => 'Manajemen Data'],
            ['label' => 'Siswa', 'route' => 'admin.siswa.index', 'icon' => 'siswa', 'aktif' => ['admin.siswa.*'], 'grup' => 'Manajemen Data'],
            ['label' => 'Kelas', 'route' => 'admin.kelas.index', 'icon' => 'kelas', 'aktif' => ['admin.kelas.*'], 'grup' => 'Manajemen Data'],
            ['label' => 'Waktu Absen', 'route' => 'admin.pengaturan.waktu-absen.index', 'icon' => 'jam', 'aktif' => ['admin.pengaturan.waktu-absen.*'], 'grup' => 'Konfigurasi'],
            ['label' => 'Periode & Alpha', 'route' => 'admin.pengaturan.periode-absen.index', 'icon' => 'dokumen-peringatan', 'aktif' => ['admin.pengaturan.periode-absen.*'], 'grup' => 'Konfigurasi'],
            ['label' => 'Lokasi Absen', 'route' => 'admin.pengaturan.lokasi-absen.index', 'icon' => 'lokasi', 'aktif' => ['admin.pengaturan.lokasi-absen.*'], 'grup' => 'Konfigurasi'],
            ['label' => 'WhatsApp API', 'route' => 'admin.pengaturan.whatsapp.index', 'icon' => 'chat', 'aktif' => ['admin.pengaturan.whatsapp.*'], 'grup' => 'Konfigurasi'],
            ['label' => 'Profil', 'route' => 'admin.profil', 'icon' => 'profil', 'aktif' => ['admin.profil', 'admin.profil.*'], 'grup' => 'Akun'],
        ];
    }

    /** @return list<array<string, mixed>> */
    private static function siswa(): array
    {
        return [
            ['label' => 'Dashboard', 'route' => 'siswa.dashboard', 'icon' => 'dashboard', 'aktif' => ['siswa.dashboard'], 'grup' => 'Utama'],
            ['label' => 'Absensi', 'route' => 'siswa.absensi', 'icon' => 'absensi', 'aktif' => ['siswa.absensi', 'siswa.absensi.*'], 'grup' => 'Akademik'],
            ['label' => 'Poin Saya', 'route' => 'siswa.poin', 'icon' => 'bintang', 'aktif' => ['siswa.poin'], 'grup' => 'Akademik'],
            ['label' => 'Tata Tertib', 'route' => 'siswa.tata-tertib.index', 'icon' => 'dokumen-peringatan', 'aktif' => ['siswa.tata-tertib.*'], 'grup' => 'Akademik'],
            ['label' => 'Profil Saya', 'route' => 'siswa.profil', 'icon' => 'profil', 'aktif' => ['siswa.profil', 'siswa.profil.*'], 'grup' => 'Akun'],
        ];
    }

    /** @return list<array<string, mixed>> */
    private static function guru(Pengguna $pengguna): array
    {
        $menu = [
            ['label' => 'Dashboard', 'route' => $pengguna->dashboardRouteName(), 'icon' => 'dashboard', 'aktif' => ['wali-kelas.dashboard', 'bk.dashboard', 'kesiswaan.dashboard'], 'grup' => 'Utama'],
        ];

        if ($pengguna->isKesiswaan()) {
            $menu = array_merge($menu, [
                ['label' => 'Monitoring Siswa', 'route' => 'kesiswaan.monitoring.index', 'icon' => 'mata', 'aktif' => ['kesiswaan.monitoring.*'], 'grup' => 'Kesiswaan'],
                ['label' => 'Pelanggaran Siswa', 'route' => 'kesiswaan.pelanggaran-siswa.index', 'icon' => 'peringatan', 'aktif' => ['kesiswaan.pelanggaran-siswa.*'], 'grup' => 'Kesiswaan'],
                [
                    'label' => 'Pengajuan Poin',
                    'route' => 'kesiswaan.pengajuan-poin.persetujuan',
                    'icon' => 'centang',
                    'aktif' => ['kesiswaan.pengajuan-poin.*'],
                    'grup' => 'Kesiswaan',
                    // Dihitung sekali di sini, bukan di tiap tampilan yang menampilkannya.
                    'badge' => PengajuanPoin::where('status', 'pending')->count(),
                ],
                ['label' => 'Jenis Pelanggaran', 'route' => 'kesiswaan.jenis-pelanggaran.index', 'icon' => 'dokumen', 'aktif' => ['kesiswaan.jenis-pelanggaran.*'], 'grup' => 'Kesiswaan'],
                ['label' => 'Laporan Kesiswaan', 'route' => 'kesiswaan.laporan.index', 'icon' => 'grafik', 'aktif' => ['kesiswaan.laporan.*'], 'grup' => 'Kesiswaan'],
                ['label' => 'Tata Tertib', 'route' => 'kesiswaan.tata-tertib.index', 'icon' => 'dokumen-peringatan', 'aktif' => ['kesiswaan.tata-tertib.*'], 'grup' => 'Kesiswaan'],
            ]);
        }

        if ($pengguna->isBk()) {
            $menu = array_merge($menu, [
                ['label' => 'Monitoring BK', 'route' => 'bk.monitoring.index', 'icon' => 'mata', 'aktif' => ['bk.monitoring.*'], 'grup' => 'BK'],
                ['label' => 'Rekap Absensi', 'route' => 'bk.laporan.index', 'icon' => 'grafik', 'aktif' => ['bk.laporan.*'], 'grup' => 'BK'],
            ]);
        }

        if ($pengguna->isWaliKelas()) {
            $menu = array_merge($menu, [
                ['label' => 'Kelas Saya', 'route' => 'wali-kelas.kelas-saya', 'icon' => 'kelompok', 'aktif' => ['wali-kelas.kelas-saya', 'wali-kelas.kelas-saya.*'], 'grup' => 'Wali Kelas'],
                ['label' => 'Rekap Absensi', 'route' => 'wali-kelas.absensi.index', 'icon' => 'absensi', 'aktif' => ['wali-kelas.absensi.*'], 'grup' => 'Wali Kelas'],
                ['label' => 'Pengajuan Poin', 'route' => 'wali-kelas.pengajuan-poin.index', 'icon' => 'tambah', 'aktif' => ['wali-kelas.pengajuan-poin.*'], 'grup' => 'Wali Kelas'],
                ['label' => 'Riwayat Pelanggaran', 'route' => 'wali-kelas.pelanggaran', 'icon' => 'dokumen', 'aktif' => ['wali-kelas.pelanggaran'], 'grup' => 'Wali Kelas'],
            ]);
        }

        $menu[] = ['label' => 'Profil Saya', 'route' => $pengguna->profilRouteName(), 'icon' => 'profil', 'aktif' => ['wali-kelas.profil', 'wali-kelas.profil.*', 'bk.profil', 'bk.profil.*', 'kesiswaan.profil', 'kesiswaan.profil.*'], 'grup' => 'Akun'];

        return $menu;
    }
}
