<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = User::where('role', 'student')
            ->with('studentProfile.class')
            ->latest()
            ->get();

        return view('admin.student.index', compact('students'));
    }

    public function create(): View
    {
        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();

        return view('admin.student.create', compact('classes'));
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
            ]);

            $user->studentProfile()->create([
                'nisn' => $request->nisn,
                'nis' => $request->nis,
                'class_id' => $request->class_id,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        });

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(User $student): View
    {
        $student->load('studentProfile.class');

        return view('admin.student.show', compact('student'));
    }

    public function edit(User $student): View
    {
        $student->load('studentProfile');
        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();

        return view('admin.student.edit', compact('student', 'classes'));
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

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(User $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function deleteAll(): RedirectResponse
    {
        User::where('role', 'student')->delete();

        return redirect()->route('admin.students.index')->with('success', 'Semua siswa berhasil dihapus.');
    }
}
