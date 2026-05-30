<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterClass = $request->get('class_id', '');

        $query = StudentProfile::with(['user', 'class'])
            // Untuk Sisa Poin (opsional withSum untuk optimasi, tapi kita panggil di query)
            ->withSum('studentViolations', 'point_deduction')
            // Untuk kehadiran hari ini
            ->with(['attendances' => function ($query) {
                $query->whereDate('date', today());
            }])
            // Untuk persentase (total hari hadir vs total hari dicatat)
            ->withCount(['attendances as total_attendances'])
            ->withCount(['attendances as present_attendances' => function ($query) {
                $query->whereIn('status', ['present', 'late']);
            }]);

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('nisn', 'like', "%{$search}%")
              ->orWhere('nis', 'like', "%{$search}%");
        }

        if ($filterClass) {
            $query->where('class_id', $filterClass);
        }

        $students = $query->paginate(25)->appends([
            'search' => $search,
            'class_id' => $filterClass,
        ]);

        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();

        return view('guru.monitoring.index', compact('students', 'classes', 'search', 'filterClass'));
    }

    public function show(StudentProfile $monitoring): View
    {
        // Load relasi yang diperlukan untuk detail
        $monitoring->load([
            'user', 
            'class',
            'biodata',
            'studentViolations' => function($q) {
                $q->latest('violation_date')->with(['recordedBy', 'violationType']);
            },
            'attendances' => function($q) {
                $q->latest('date')->take(30); // 30 hari terakhir
            }
        ]);

        return view('guru.monitoring.show', [
            'student' => $monitoring
        ]);
    }
}
