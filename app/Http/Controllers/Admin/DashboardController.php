<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const TEACHER_ROLES = ['wali_kelas', 'bk', 'kesiswaan'];

    public function index(): View
    {
        $studentsByGrade = Kelas::withCount(['siswa' => fn ($query) => $query->whereHas('pengguna', fn ($userQuery) => $userQuery->where('status', 'registered'))])
            ->orderBy('tingkat')
            ->get()
            ->groupBy('tingkat')
            ->map(fn ($classes): int => $classes->sum('siswa_count'));

        $charts = [
            'studentsByGrade' => collect(['10', '11', '12'])->map(fn (string $grade): array => [
                'label' => 'Tingkat '.$grade,
                'value' => $studentsByGrade->get($grade, 0),
                'variant' => 'primary',
            ])->all(),
            'registration' => [
                ['label' => 'Terdaftar', 'value' => Pengguna::where('peran', 'siswa')->where('status', 'registered')->count(), 'variant' => 'success'],
                ['label' => 'Belum Daftar', 'value' => Pengguna::where('peran', 'siswa')->where('status', 'unregistered')->count(), 'variant' => 'warning'],
            ],
            'teacherRegistration' => [
                ['label' => 'Terdaftar', 'value' => Pengguna::whereIn('peran', self::TEACHER_ROLES)->where('status', 'registered')->count(), 'variant' => 'success'],
                ['label' => 'Belum Daftar', 'value' => Pengguna::whereIn('peran', self::TEACHER_ROLES)->where('status', 'unregistered')->count(), 'variant' => 'warning'],
            ],
        ];

        // Kartu ringkasan: hitung yang sudah punya akun aktif, bukan seluruh baris
        // hasil impor - siswa/guru yang belum daftar belum bisa dipakai di sistem.
        $totals = [
            'students' => ProfilSiswa::whereHas('pengguna', fn ($query) => $query->where('status', 'registered'))->count(),
            'teachers' => Pengguna::whereIn('peran', self::TEACHER_ROLES)->where('status', 'registered')->count(),
            'classes' => Kelas::count(),
        ];

        return view('admin.dashboard', compact('charts', 'totals'));
    }
}
