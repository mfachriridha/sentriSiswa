<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = User::where('role', 'teacher')
            ->with(['teacherProfile', 'homeroomClass'])
            ->latest()
            ->get();

        return view('admin.teacher.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('admin.teacher.create');
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'teacher',
            ]);

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
        User::where('role', 'teacher')->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Semua guru berhasil dihapus.');
    }
}
