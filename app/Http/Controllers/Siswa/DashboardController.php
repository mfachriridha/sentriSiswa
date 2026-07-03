<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\AbsenceWarningService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(AbsenceWarningService $absenceWarning): View
    {
        $student = Auth::user()->loadMissing('profilSiswa.absensi', 'profilSiswa.pelanggaranSiswa');
        $profile = $student->profilSiswa;
        $points = $profile?->poin ?? 100;
        $attendances = $profile?->absensi ?? collect();
        $alphaWarningCount = $profile ? $absenceWarning->alphaCountForStudentId($profile->nisn) : 0;
        $warningThreshold = AbsenceWarningService::Threshold;

        $charts = [
            'points' => [
                ['label' => 'Sisa Poin', 'value' => $points, 'variant' => $points > 75 ? 'success' : ($points > 50 ? 'warning' : 'error')],
                ['label' => 'Poin Terpakai', 'value' => max(0, 100 - $points), 'variant' => 'error'],
            ],
            'attendance' => [
                ['label' => 'Hadir', 'value' => $attendances->where('status', 'hadir')->count(), 'variant' => 'success'],
                ['label' => 'Terlambat', 'value' => $attendances->where('status', 'terlambat')->count(), 'variant' => 'warning'],
                ['label' => 'Izin/Sakit', 'value' => $attendances->whereIn('status', ['izin', 'sakit'])->count(), 'variant' => 'info'],
                ['label' => 'Alpha', 'value' => $attendances->where('status', 'alpha')->count(), 'variant' => 'error'],
            ],
        ];

        return view('siswa.dashboard', compact('charts', 'alphaWarningCount', 'warningThreshold'));
    }
}
