<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BkMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $grade = Auth::user()->teacherProfile?->grade;
        $search = $request->get('search', '');
        $filterClass = $request->get('class_id', '');

        $query = StudentProfile::with(['user', 'class'])
            ->withSum(['studentViolations' => fn ($query) => $query->approved()], 'point_deduction')
            ->with(['attendances' => fn ($query) => $query->whereDate('date', today())])
            ->withCount(['attendances as total_attendances'])
            ->withCount(['attendances as present_attendances' => fn ($query) => $query->whereIn('status', ['hadir', 'terlambat'])])
            ->whereHas('class', fn ($query) => $query->where('grade', $grade));

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($filterClass) {
            $query->where('class_id', $filterClass);
        }

        $students = $query->paginate(25)->withQueryString();
        $classes = SchoolClass::where('grade', $grade)->orderBy('name')->get();
        $routePrefix = 'guru.bk.monitoring';
        $title = 'Monitoring BK';
        $description = 'Pantau absensi dan pelanggaran siswa tingkat '.$grade.'.';

        return view('guru.monitoring.index', compact('students', 'classes', 'search', 'filterClass', 'routePrefix', 'title', 'description'));
    }

    public function show(StudentProfile $monitoring): View
    {
        $grade = Auth::user()->teacherProfile?->grade;

        abort_unless($monitoring->class?->grade === $grade, 403);

        $monitoring->load([
            'user',
            'class',
            'biodata',
            'studentViolations' => fn ($query) => $query->approved()->latest('violation_date')->with(['recordedBy', 'violationType']),
            'attendances' => fn ($query) => $query->latest('date')->take(30),
        ]);

        return view('guru.monitoring.show', [
            'student' => $monitoring,
            'backRoute' => route('guru.bk.monitoring.index'),
            'createViolationRoute' => route('guru.bk.pelanggaran.create', ['student_profile_id' => $monitoring->id]),
        ]);
    }
}
