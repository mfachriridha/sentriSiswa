<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Class\StoreClassRequest;
use App\Http\Requests\Class\UpdateClassRequest;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View
    {
        $classes = SchoolClass::with('homeroomTeacher')
            ->latest()
            ->get();

        return view('admin.class.index', compact('classes'));
    }

    public function create(): View
    {
        $homeroomTeachers = User::where('role', 'teacher')
            ->whereHas('teacherProfile', fn ($q) => $q->where('teacher_type', 'homeroom'))
            ->get();

        return view('admin.class.create', compact('homeroomTeachers'));
    }

    public function store(StoreClassRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $separator = is_numeric($data['identifier']) ? '. ' : ' ';
        $data['name'] = $data['grade'].$separator.$data['identifier'];

        SchoolClass::create($data);

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(SchoolClass $class): View
    {
        $class->load('homeroomTeacher');

        return view('admin.class.show', compact('class'));
    }

    public function edit(SchoolClass $class): View
    {
        $class->load('homeroomTeacher');
        $identifier = trim((string) str_replace($class->grade, '', $class->name));
        $homeroomTeachers = User::where('role', 'teacher')
            ->whereHas('teacherProfile', fn ($q) => $q->where('teacher_type', 'homeroom'))
            ->get();

        return view('admin.class.edit', compact('class', 'identifier', 'homeroomTeachers'));
    }

    public function update(UpdateClassRequest $request, SchoolClass $class): RedirectResponse
    {
        $data = $request->validated();
        $separator = is_numeric($data['identifier']) ? '. ' : ' ';
        $data['name'] = $data['grade'].$separator.$data['identifier'];

        $class->update($data);

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function deleteAll(): RedirectResponse
    {
        SchoolClass::query()->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Semua kelas berhasil dihapus.');
    }
}
