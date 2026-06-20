<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentViolation\StoreStudentViolationRequest;
use App\Models\StudentProfile;
use App\Models\StudentViolation;
use App\Models\ViolationType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BkViolationSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $grade = Auth::user()->teacherProfile?->grade;
        $status = $request->get('status', '');

        $studentViolations = StudentViolation::with(['studentProfile.user', 'studentProfile.class', 'recordedBy', 'approvedBy'])
            ->whereHas('studentProfile.class', fn ($query) => $query->where('grade', $grade))
            ->where('recorded_by_user_id', Auth::id())
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('violation_date')
            ->paginate(20)
            ->withQueryString();

        $statusLabels = StudentViolation::statusLabels();

        return view('bk.pelanggaran.index', compact('studentViolations', 'status', 'statusLabels', 'grade'));
    }

    public function create(Request $request): View
    {
        $students = $this->students();
        $violationTypes = ViolationType::where('is_active', true)
            ->orderBy('point_deduction')
            ->orderBy('name')
            ->get();
        $categoryLabels = ViolationType::categoryLabels();
        $selectedStudentId = $request->get('student_profile_id', '');

        return view('bk.pelanggaran.create', compact('students', 'violationTypes', 'categoryLabels', 'selectedStudentId'));
    }

    public function store(StoreStudentViolationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $student = StudentProfile::with('class')->findOrFail($data['student_profile_id']);
        $this->authorizeStudentGrade($student);

        $violationType = ViolationType::findOrFail($data['violation_type_id']);

        StudentViolation::create([
            'student_profile_id' => $student->id,
            'violation_type_id' => $violationType->id,
            'recorded_by_user_id' => Auth::id(),
            'violation_date' => $data['violation_date'],
            'violation_name' => $violationType->name,
            'violation_category' => $violationType->category,
            'point_deduction' => $violationType->point_deduction,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('bk.pelanggaran.index')->with('success', 'Pengajuan pelanggaran berhasil dikirim ke kesiswaan.');
    }

    /**
     * @return Collection<int, StudentProfile>
     */
    private function students(): Collection
    {
        $grade = Auth::user()->teacherProfile?->grade;

        return StudentProfile::with(['user', 'class'])
            ->whereHas('class', fn ($query) => $query->where('grade', $grade))
            ->whereHas('user', fn ($query) => $query->where('role', 'siswa'))
            ->get()
            ->sortBy(fn (StudentProfile $studentProfile) => $studentProfile->user?->name ?? '')
            ->values();
    }

    private function authorizeStudentGrade(StudentProfile $student): void
    {
        abort_unless($student->class?->grade === Auth::user()->teacherProfile?->grade, 403);
    }
}
