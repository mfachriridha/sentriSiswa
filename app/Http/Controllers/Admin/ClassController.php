<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Class\StoreClassRequest;
use App\Http\Requests\Class\UpdateClassRequest;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterGrade = $request->get('grade', '');
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $allowed = ['name', 'grade', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'name';
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'asc';

        $classes = SchoolClass::with('homeroomTeacher');

        if ($search) {
            $classes->where('name', 'like', "%{$search}%");
        }

        if ($filterGrade) {
            $classes->where('grade', $filterGrade);
        }

        if ($sort === 'name') {
            $classes = $classes->orderBy('grade', $direction)
                ->orderByRaw("CAST(SUBSTRING_INDEX(name, '. ', -1) AS UNSIGNED) {$direction}")
                ->orderBy('name', $direction);
        } elseif ($sort === 'grade') {
            $classes = $classes->orderBy('grade', $direction)->orderBy('name', 'asc');
        } else {
            $classes = $classes->orderBy($sort, $direction);
        }

        $classes = $classes->paginate(25)->appends([
            'search' => $search,
            'grade' => $filterGrade,
            'sort' => $sort,
            'direction' => $direction,
        ]);

        return view('admin.class.index', compact('classes', 'sort', 'direction', 'search', 'filterGrade'));
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

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(SchoolClass $class): View
    {
        $class->load(['homeroomTeacher']);
        $class->loadCount('students');

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

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function deleteAll(): RedirectResponse
    {
        SchoolClass::query()->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Semua kelas berhasil dihapus.');
    }
}
