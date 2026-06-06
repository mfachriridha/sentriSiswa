<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\RejectStudentViolationRequest;
use App\Http\Requests\StudentViolation\StoreStudentViolationRequest;
use App\Http\Requests\StudentViolation\UpdateStudentViolationRequest;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\StudentViolation;
use App\Models\ViolationType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentViolationController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterClass = $request->get('class_id', '');
        $filterCategory = $request->get('category', '');
        $filterViolationType = $request->get('violation_type_id', '');
        $filterDate = $request->get('violation_date', '');
        $filterStatus = $request->get('status', $request->route('status', ''));
        $sort = $request->get('sort', 'violation_date');
        $direction = $request->get('direction', 'desc');
        $allowed = ['violation_date', 'violation_name', 'violation_category', 'point_deduction', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'violation_date';
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        $studentViolations = StudentViolation::with(['studentProfile.user', 'studentProfile.class', 'recordedBy']);

        if ($search) {
            $studentViolations->where(function ($query) use ($search) {
                $query->where('violation_name', 'like', "%{$search}%")
                    ->orWhereHas('studentProfile', fn ($profileQuery) => $profileQuery
                        ->where('nisn', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%"))
                    ->orWhereHas('studentProfile.user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($filterClass) {
            $studentViolations->whereHas('studentProfile', fn ($query) => $query->where('class_id', $filterClass));
        }

        if (array_key_exists($filterCategory, ViolationType::categoryLabels())) {
            $studentViolations->where('violation_category', $filterCategory);
        }

        if ($filterViolationType) {
            $studentViolations->where('violation_type_id', $filterViolationType);
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate) === 1) {
            $studentViolations->whereDate('violation_date', $filterDate);
        }

        if (array_key_exists($filterStatus, StudentViolation::statusLabels())) {
            $studentViolations->where('status', $filterStatus);
        }

        if ($sort === 'violation_category') {
            $studentViolations = $studentViolations
                ->orderByRaw("CASE violation_category WHEN 'light' THEN 1 WHEN 'medium' THEN 2 WHEN 'heavy' THEN 3 WHEN 'severe' THEN 4 ELSE 5 END {$direction}")
                ->orderBy('point_deduction', $direction)
                ->orderBy('violation_name');
        } else {
            $studentViolations = $studentViolations->orderBy($sort, $direction);
        }

        $studentViolations = $studentViolations->paginate(25)->appends([
            'search' => $search,
            'class_id' => $filterClass,
            'category' => $filterCategory,
            'violation_type_id' => $filterViolationType,
            'violation_date' => $filterDate,
            'status' => $filterStatus,
            'sort' => $sort,
            'direction' => $direction,
        ]);

        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();
        $violationTypes = $this->violationTypes();
        $categoryLabels = ViolationType::categoryLabels();
        $statusLabels = StudentViolation::statusLabels();

        return view('guru.pelanggaran-siswa.index', compact('studentViolations', 'classes', 'violationTypes', 'categoryLabels', 'statusLabels', 'sort', 'direction', 'search', 'filterClass', 'filterCategory', 'filterViolationType', 'filterDate', 'filterStatus'));
    }

    public function create(): View
    {
        $students = $this->students();
        $violationTypes = $this->violationTypes(activeOnly: true);
        $categoryLabels = ViolationType::categoryLabels();

        return view('guru.pelanggaran-siswa.create', compact('students', 'violationTypes', 'categoryLabels'));
    }

    public function store(StoreStudentViolationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $violationType = ViolationType::findOrFail($data['violation_type_id']);

        StudentViolation::create($this->violationData($data, $violationType, includeRecorder: true, approved: true));

        return redirect()->route('guru.pelanggaran-siswa.index')->with('success', 'Pelanggaran siswa berhasil dicatat.');
    }

    public function show(StudentViolation $studentViolation): View
    {
        $studentViolation->load(['studentProfile.user', 'studentProfile.class', 'violationType', 'recordedBy', 'approvedBy']);
        $categoryLabels = ViolationType::categoryLabels();
        $statusLabels = StudentViolation::statusLabels();

        return view('guru.pelanggaran-siswa.show', compact('studentViolation', 'categoryLabels', 'statusLabels'));
    }

    public function edit(StudentViolation $studentViolation): View
    {
        $studentViolation->load(['studentProfile.user', 'studentProfile.class', 'violationType']);
        $students = $this->students();
        $violationTypes = $this->violationTypes(activeOnly: true, currentViolationType: $studentViolation->violationType);
        $categoryLabels = ViolationType::categoryLabels();

        return view('guru.pelanggaran-siswa.edit', compact('studentViolation', 'students', 'violationTypes', 'categoryLabels'));
    }

    public function update(UpdateStudentViolationRequest $request, StudentViolation $studentViolation): RedirectResponse
    {
        $data = $request->validated();
        $violationType = ViolationType::findOrFail($data['violation_type_id']);

        $studentViolation->update($this->violationData($data, $violationType));

        return redirect()->route('guru.pelanggaran-siswa.index')->with('success', 'Pelanggaran siswa berhasil diperbarui.');
    }

    public function approve(StudentViolation $studentViolation): RedirectResponse
    {
        abort_unless($studentViolation->status === 'pending', 403);

        $studentViolation->update([
            'status' => 'approved',
            'approved_by_user_id' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()->route('guru.pelanggaran-siswa.show', $studentViolation)->with('success', 'Pengajuan pelanggaran berhasil disetujui.');
    }

    public function reject(RejectStudentViolationRequest $request, StudentViolation $studentViolation): RedirectResponse
    {
        abort_unless($studentViolation->status === 'pending', 403);

        $studentViolation->update([
            'status' => 'rejected',
            'approved_by_user_id' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->validated()['rejection_reason'],
        ]);

        return redirect()->route('guru.pelanggaran-siswa.show', $studentViolation)->with('success', 'Pengajuan pelanggaran berhasil ditolak.');
    }

    public function destroy(StudentViolation $studentViolation): RedirectResponse
    {
        $studentViolation->delete();

        return redirect()->route('guru.pelanggaran-siswa.index')->with('success', 'Pelanggaran siswa berhasil dihapus.');
    }

    private function students(): Collection
    {
        return StudentProfile::with(['user', 'class'])
            ->whereHas('user', fn ($query) => $query->where('role', 'student'))
            ->get()
            ->sortBy(fn (StudentProfile $studentProfile) => $studentProfile->user?->name ?? '')
            ->values();
    }

    private function violationTypes(bool $activeOnly = false, ?ViolationType $currentViolationType = null): Collection
    {
        $query = ViolationType::query();

        if ($activeOnly || $currentViolationType) {
            $query->where(function ($query) use ($currentViolationType) {
                $query->where('is_active', true);

                if ($currentViolationType) {
                    $query->orWhere('id', $currentViolationType->id);
                }
            });
        }

        return $query
            ->orderByRaw("CASE category WHEN 'light' THEN 1 WHEN 'medium' THEN 2 WHEN 'heavy' THEN 3 WHEN 'severe' THEN 4 ELSE 5 END")
            ->orderBy('point_deduction')
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array{student_profile_id: int|string, violation_type_id: int|string, violation_date: string, notes?: string|null}  $data
     * @return array<string, mixed>
     */
    private function violationData(array $data, ViolationType $violationType, bool $includeRecorder = false, bool $approved = false): array
    {
        $violationData = [
            'student_profile_id' => $data['student_profile_id'],
            'violation_type_id' => $violationType->id,
            'violation_date' => $data['violation_date'],
            'violation_name' => $violationType->name,
            'violation_category' => $violationType->category,
            'point_deduction' => $violationType->point_deduction,
            'notes' => $data['notes'] ?? null,
        ];

        if ($includeRecorder) {
            $violationData['recorded_by_user_id'] = Auth::id();
        }

        if ($approved) {
            $violationData['status'] = 'approved';
            $violationData['approved_by_user_id'] = Auth::id();
            $violationData['approved_at'] = now();
        }

        return $violationData;
    }
}
