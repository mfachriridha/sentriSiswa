<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Models\SchoolClass;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterType = $request->get('teacher_type', '');
        $filterGrade = $request->get('grade', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $allowed = ['name', 'email', 'created_at', 'nip', 'teacher_type', 'phone', 'class_name'];
        $sort = in_array($sort, $allowed) ? $sort : 'created_at';
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        $teachers = User::where('role', 'teacher')
            ->with(['teacherProfile', 'homeroomClass']);

        if ($search) {
            $teachers->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('teacherProfile', fn ($q) => $q->where('nip', 'like', "%{$search}%"));
            });
        }

        if ($filterType) {
            $teachers->whereHas('teacherProfile', fn ($q) => $q->where('teacher_type', $filterType));
        }

        if ($filterGrade) {
            $teachers->where(function ($q) use ($filterGrade) {
                $q->whereHas('teacherProfile', fn ($q) => $q->where('grade', $filterGrade))
                    ->orWhereHas('homeroomClass', fn ($q) => $q->where('grade', $filterGrade));
            });
        }

        if ($sort === 'class_name') {
            $teachers = $teachers->orderBy(
                SchoolClass::select('name')
                    ->whereColumn('homeroom_teacher_id', 'users.id')
                    ->limit(1),
                $direction,
            );
        } elseif (in_array($sort, ['nip', 'teacher_type', 'phone'])) {
            $teachers = $teachers->orderBy(
                TeacherProfile::select($sort)
                    ->whereColumn('teacher_profiles.user_id', 'users.id')
                    ->limit(1),
                $direction,
            );
        } else {
            $teachers = $teachers->orderBy($sort, $direction);
        }

        $teachers = $teachers->paginate(25)->appends([
            'search' => $search,
            'teacher_type' => $filterType,
            'grade' => $filterGrade,
            'sort' => $sort,
            'direction' => $direction,
        ]);

        return view('admin.teacher.index', compact('teachers', 'sort', 'direction', 'search', 'filterType', 'filterGrade'));
    }

    public function create(): View
    {
        return view('admin.teacher.create');
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => 'teacher',
                'status' => 'unregistered',
                'password' => Hash::make($request->filled('password') ? $request->password : 'password'),
            ];

            $user = User::create($data);

            $user->teacherProfile()->create([
                'nip' => $request->nip,
                'phone' => $request->phone,
                'teacher_type' => $request->teacher_type,
                'grade' => $request->teacher_type === 'counselor' ? $request->grade : null,
            ]);
        });

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function show(User $teacher): View
    {
        $teacher->load(['teacherProfile', 'homeroomClass']);

        return view('admin.teacher.show', compact('teacher'));
    }

    public function edit(User $teacher): View
    {
        $teacher->load('teacherProfile');

        return view('admin.teacher.edit', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, User $teacher): RedirectResponse
    {
        DB::transaction(function () use ($request, $teacher) {
            $teacher->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $teacher->update(['password' => Hash::make($request->password)]);
            }

            $teacher->teacherProfile()->updateOrCreate(
                ['user_id' => $teacher->id],
                [
                    'nip' => $request->nip,
                    'phone' => $request->phone,
                    'teacher_type' => $request->teacher_type,
                    'grade' => $request->teacher_type === 'counselor' ? $request->grade : null,
                ],
            );
        });

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(User $teacher): RedirectResponse
    {
        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil dihapus.');
    }

    public function deleteAll(): RedirectResponse
    {
        TeacherProfile::whereHas('user', fn ($q) => $q->where('role', 'teacher'))->delete();
        User::where('role', 'teacher')->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Semua guru berhasil dihapus.');
    }
}
