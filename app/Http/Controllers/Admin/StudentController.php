<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterGrade = $request->get('grade', '');
        $filterStatus = $request->get('status', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $allowed = ['name', 'email', 'created_at', 'nisn', 'nis', 'class_name'];
        $sort = in_array($sort, $allowed) ? $sort : 'created_at';
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        $students = User::where('role', 'siswa')
            ->with('studentProfile.class');

        if ($search) {
            $students->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('studentProfile', fn ($q) => $q
                        ->where('nisn', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%"));
            });
        }

        if ($filterGrade) {
            $students->whereHas('studentProfile.class', fn ($q) => $q->where('grade', $filterGrade));
        }

        if (in_array($filterStatus, ['registered', 'unregistered'], true)) {
            $students->where('status', $filterStatus);
        }

        if ($sort === 'class_name') {
            $students = $students->orderBy(
                SchoolClass::select('name')
                    ->whereColumn('id', 'student_profiles.class_id')
                    ->limit(1),
                $direction,
            );
        } elseif (in_array($sort, ['nisn', 'nis'])) {
            $students = $students->orderBy(
                StudentProfile::select($sort)
                    ->whereColumn('student_profiles.user_id', 'users.id')
                    ->limit(1),
                $direction,
            );
        } else {
            $students = $students->orderBy($sort, $direction);
        }

        $students = $students->paginate(25)->appends([
            'search' => $search,
            'grade' => $filterGrade,
            'status' => $filterStatus,
            'sort' => $sort,
            'direction' => $direction,
        ]);

        return view('admin.siswa.index', compact('students', 'sort', 'direction', 'search', 'filterGrade', 'filterStatus'));
    }

    public function create(): View
    {
        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();

        return view('admin.siswa.create', compact('classes'));
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => 'siswa',
                'status' => 'unregistered',
                'password' => Hash::make($request->filled('password') ? $request->password : 'password'),
            ];

            $user = User::create($data);

            $user->studentProfile()->create([
                'nisn' => $request->nisn,
                'nis' => $request->nis,
                'class_id' => $request->class_id,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(User $student): View
    {
        $student->load('studentProfile.class');

        return view('admin.siswa.show', compact('student'));
    }

    public function edit(User $student): View
    {
        $student->load('studentProfile');
        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();

        return view('admin.siswa.edit', compact('student', 'classes'));
    }

    public function update(UpdateStudentRequest $request, User $student): RedirectResponse
    {
        DB::transaction(function () use ($request, $student) {
            $student->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $student->update(['password' => Hash::make($request->password)]);
            }

            $student->studentProfile()->updateOrCreate(
                ['user_id' => $student->id],
                [
                    'nisn' => $request->nisn,
                    'nis' => $request->nis,
                    'class_id' => $request->class_id,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ],
            );
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(User $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function deleteAll(): RedirectResponse
    {
        StudentProfile::whereHas('user', fn ($q) => $q->where('role', 'siswa'))->delete();
        User::where('role', 'siswa')->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Semua siswa berhasil dihapus.');
    }
}
