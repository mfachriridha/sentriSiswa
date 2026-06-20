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
        $filterRole = $request->get('role', '');
        $filterGrade = $request->get('grade', '');
        $filterStatus = $request->get('status', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $allowed = ['name', 'email', 'created_at', 'nip', 'role', 'phone', 'class_name'];
        $sort = in_array($sort, $allowed) ? $sort : 'created_at';
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        $teachers = User::whereIn('role', ['wali_kelas', 'bk', 'kesiswaan'])
            ->with(['teacherProfile', 'homeroomClass']);

        if ($search) {
            $teachers->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('teacherProfile', fn ($q) => $q->where('nip', 'like', "%{$search}%"));
            });
        }

        if (in_array($filterRole, ['wali_kelas', 'bk', 'kesiswaan'], true)) {
            $teachers->where('role', $filterRole);
        }

        if ($filterGrade) {
            $teachers->where(function ($q) use ($filterGrade) {
                $q->whereHas('teacherProfile', fn ($q) => $q->where('grade', $filterGrade))
                    ->orWhereHas('homeroomClass', fn ($q) => $q->where('grade', $filterGrade));
            });
        }

        if (in_array($filterStatus, ['registered', 'unregistered'], true)) {
            $teachers->where('status', $filterStatus);
        }

        if ($sort === 'class_name') {
            $teachers = $teachers->orderBy(
                SchoolClass::select('name')
                    ->whereColumn('homeroom_teacher_id', 'users.id')
                    ->limit(1),
                $direction,
            );
        } elseif (in_array($sort, ['nip', 'phone'])) {
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
            'role' => $filterRole,
            'grade' => $filterGrade,
            'status' => $filterStatus,
            'sort' => $sort,
            'direction' => $direction,
        ]);

        return view('admin.guru.index', compact('teachers', 'sort', 'direction', 'search', 'filterRole', 'filterGrade', 'filterStatus'));
    }

    public function create(): View
    {
        return view('admin.guru.create');
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'status' => 'unregistered',
                'password' => Hash::make($request->filled('password') ? $request->password : 'password'),
            ];

            $user = User::create($data);

            $user->teacherProfile()->create([
                'nip' => $request->nip,
                'phone' => $request->phone,
                'grade' => $request->role === 'bk' ? $request->grade : null,
            ]);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function show(User $teacher): View
    {
        $teacher->load(['teacherProfile', 'homeroomClass']);

        return view('admin.guru.show', compact('teacher'));
    }

    public function edit(User $teacher): View
    {
        $teacher->load('teacherProfile');

        return view('admin.guru.edit', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, User $teacher): RedirectResponse
    {
        DB::transaction(function () use ($request, $teacher) {
            $teacher->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ]);

            if ($request->filled('password')) {
                $teacher->update(['password' => Hash::make($request->password)]);
            }

            $teacher->teacherProfile()->updateOrCreate(
                ['user_id' => $teacher->id],
                [
                    'nip' => $request->nip,
                    'phone' => $request->phone,
                    'grade' => $request->role === 'bk' ? $request->grade : null,
                ],
            );
        });

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(User $teacher): RedirectResponse
    {
        $teacher->delete();

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil dihapus.');
    }

    public function deleteAll(): RedirectResponse
    {
        TeacherProfile::whereHas('user', fn ($q) => $q->whereIn('role', ['wali_kelas', 'bk', 'kesiswaan']))->delete();
        User::whereIn('role', ['wali_kelas', 'bk', 'kesiswaan'])->delete();

        return redirect()->route('admin.guru.index')->with('success', 'Semua guru berhasil dihapus.');
    }
}
