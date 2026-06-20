<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Services\AbsenceWarningService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function index(Request $request, AbsenceWarningService $absenceWarning): View
    {
        $search = $request->get('search', '');
        $filterClass = $request->get('class_id', '');

        $query = StudentProfile::with(['user', 'class'])
            ->withSum(['studentViolations' => fn ($query) => $query->approved()], 'point_deduction')
            ->with(['attendances' => fn ($query) => $query->whereDate('date', today())])
            ->withCount(['attendances as total_attendances'])
            ->withCount(['attendances as present_attendances' => fn ($query) => $query->whereIn('status', ['hadir', 'terlambat'])]);

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

        $students = $query->paginate(25)->appends([
            'search' => $search,
            'class_id' => $filterClass,
        ]);

        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();
        $routePrefix = 'kesiswaan.monitoring';
        $title = 'Monitoring Siswa';
        $description = 'Pantau kehadiran dan pelanggaran siswa secara keseluruhan.';
        $alphaWarnings = $absenceWarning->alphaCountsForStudentIds($students->getCollection()->pluck('id'));
        $warningThreshold = AbsenceWarningService::Threshold;

        return view('kesiswaan.monitoring.index', compact('students', 'classes', 'search', 'filterClass', 'routePrefix', 'title', 'description', 'alphaWarnings', 'warningThreshold'));
    }

    public function show(StudentProfile $monitoring, AbsenceWarningService $absenceWarning): View
    {
        // Load relasi yang diperlukan untuk detail
        $monitoring->load([
            'user',
            'class',
            'biodata',
            'studentViolations' => fn ($q) => $q->approved()->latest('violation_date')->with(['recordedBy', 'violationType']),
            'attendances' => fn ($q) => $q->latest('date')->take(30),
        ]);

        return view('kesiswaan.monitoring.show', [
            'student' => $monitoring,
            'backRoute' => route('kesiswaan.monitoring.index'),
            'createViolationRoute' => route('kesiswaan.pelanggaran-siswa.create', ['student_profile_id' => $monitoring->id]),
            'alphaWarningCount' => $absenceWarning->alphaCountForStudentId($monitoring->id),
            'warningThreshold' => AbsenceWarningService::Threshold,
        ]);
    }
}
