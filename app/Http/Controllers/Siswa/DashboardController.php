<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $student = Auth::user()->loadMissing('profilSiswa.absensi', 'profilSiswa.pelanggaranSiswa', 'profilSiswa.kelas');
        $profile = $student->profilSiswa;
        $points = $profile?->poin ?? 100;
        $attendances = $profile?->absensi ?? collect();

        $stats = [
            'points' => $points,
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'izin_sakit' => $attendances->whereIn('status', ['izin', 'sakit', 'dispensasi'])->count(),
            'alpha' => $attendances->where('status', 'alpha')->count(),
        ];

        $identitas = [
            'kelas' => $profile?->kelas?->nama,
            'nisn' => $profile?->nisn,
            'nis' => $profile?->nis,
        ];

        return view('siswa.dashboard', compact('stats', 'identitas'));
    }
}
