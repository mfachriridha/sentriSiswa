<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalSiswa = Siswa::count();
        $totalGuru = User::whereIn('peran', [User::PERAN_WALI_KELAS, User::PERAN_BK, User::PERAN_KESISWAAN])->count();
        $totalKelas = Kelas::count();

        $statistik = [
            'totalSiswa' => $totalSiswa,
            'totalGuru' => $totalGuru,
            'totalKelas' => $totalKelas,
            'totalTerdaftar' => User::where('peran', User::PERAN_SISWA)
                ->where('status', User::STATUS_TERDAFTAR)
                ->count(),
        ];

        $tingkatSiswa = Siswa::query()
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->selectRaw('kelas.tingkat, count(*) as total')
            ->groupBy('kelas.tingkat')
            ->orderBy('kelas.tingkat')
            ->pluck('total', 'tingkat');

        $statusRegistrasi = User::query()
            ->where('peran', User::PERAN_SISWA)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $peranPengguna = User::query()
            ->selectRaw('peran, count(*) as total')
            ->groupBy('peran')
            ->pluck('total', 'peran');

        $charts = [
            'studentsByGrade' => [
                ['label' => 'Tingkat 10', 'value' => (int) ($tingkatSiswa['10'] ?? 0)],
                ['label' => 'Tingkat 11', 'value' => (int) ($tingkatSiswa['11'] ?? 0)],
                ['label' => 'Tingkat 12', 'value' => (int) ($tingkatSiswa['12'] ?? 0)],
            ],
            'registration' => [
                ['label' => 'Terdaftar', 'value' => (int) ($statusRegistrasi[User::STATUS_TERDAFTAR] ?? 0)],
                ['label' => 'Belum Terdaftar', 'value' => (int) ($statusRegistrasi[User::STATUS_BELUM_TERDAFTAR] ?? 0)],
            ],
            'roles' => [
                ['label' => 'Admin', 'value' => (int) ($peranPengguna[User::PERAN_ADMIN] ?? 0)],
                ['label' => 'Wali Kelas', 'value' => (int) ($peranPengguna[User::PERAN_WALI_KELAS] ?? 0)],
                ['label' => 'BK', 'value' => (int) ($peranPengguna[User::PERAN_BK] ?? 0)],
                ['label' => 'Kesiswaan', 'value' => (int) ($peranPengguna[User::PERAN_KESISWAAN] ?? 0)],
                ['label' => 'Siswa', 'value' => (int) ($peranPengguna[User::PERAN_SISWA] ?? 0)],
            ],
        ];

        return Inertia::render('admin/Dashboard', [
            'statistik' => $statistik,
            'charts' => $charts,
        ]);
    }
}
