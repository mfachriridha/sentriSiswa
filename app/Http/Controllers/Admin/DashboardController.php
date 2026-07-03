<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $studentsByGrade = Kelas::withCount('siswa')
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
        ];

        return view('admin.dashboard', compact('charts'));
    }
}
