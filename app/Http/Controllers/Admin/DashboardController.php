<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $studentsByGrade = SchoolClass::withCount('students')
            ->orderBy('grade')
            ->get()
            ->groupBy('grade')
            ->map(fn ($classes): int => $classes->sum('students_count'));

        $charts = [
            'studentsByGrade' => collect(['10', '11', '12'])->map(fn (string $grade): array => [
                'label' => 'Tingkat '.$grade,
                'value' => $studentsByGrade->get($grade, 0),
                'variant' => 'primary',
            ])->all(),
            'registration' => [
                ['label' => 'Registered', 'value' => User::where('role', 'student')->where('status', 'registered')->count(), 'variant' => 'success'],
                ['label' => 'Belum Register', 'value' => User::where('role', 'student')->where('status', 'unregistered')->count(), 'variant' => 'warning'],
            ],
            'roles' => [
                ['label' => 'Admin', 'value' => User::where('role', 'admin')->count(), 'variant' => 'neutral'],
                ['label' => 'Guru', 'value' => User::where('role', 'teacher')->count(), 'variant' => 'info'],
                ['label' => 'Siswa', 'value' => StudentProfile::count(), 'variant' => 'primary'],
            ],
        ];

        return view('admin.dashboard', compact('charts'));
    }
}
