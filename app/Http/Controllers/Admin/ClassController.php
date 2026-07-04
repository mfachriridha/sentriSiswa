<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Class\StoreClassRequest;
use App\Http\Requests\Class\UpdateClassRequest;
use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterGrade = $request->get('tingkat', '');
        $sort = $request->get('sort', 'nama');
        $direction = $request->get('direction', 'asc');
        $allowed = ['nama', 'tingkat', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'nama';
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'asc';

        $classes = Kelas::with('waliKelas');

        if ($search) {
            $classes->where('nama', 'like', "%{$search}%");
        }

        if ($filterGrade) {
            $classes->where('tingkat', $filterGrade);
        }

        if ($sort === 'nama') {
            $classes = $classes->orderBy('tingkat', $direction)
                ->orderByRaw("CAST(SUBSTRING_INDEX(nama, '. ', -1) AS UNSIGNED) {$direction}")
                ->orderBy('nama', $direction);
        } elseif ($sort === 'tingkat') {
            $classes = $classes->orderBy('tingkat', $direction)->orderBy('nama', 'asc');
        } else {
            $classes = $classes->orderBy($sort, $direction);
        }

        $classes = $classes->paginate(25)->appends([
            'search' => $search,
            'tingkat' => $filterGrade,
            'sort' => $sort,
            'direction' => $direction,
        ]);

        return view('admin.kelas.index', compact('classes', 'sort', 'direction', 'search', 'filterGrade'));
    }

    public function create(): View
    {
        $homeroomTeachers = Pengguna::where('peran', 'wali_kelas')
            ->get();

        return view('admin.kelas.create', compact('homeroomTeachers'));
    }

    public function store(StoreClassRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $pengenal = $data['nama'];
        $separator = is_numeric($pengenal) ? '. ' : ' ';
        $data['nama'] = $data['tingkat'].$separator.$pengenal;

        if (Kelas::where('nama', $data['nama'])->where('tingkat', $data['tingkat'])->exists()) {
            return back()->withErrors(['nama' => 'Kelas dengan kombinasi ini sudah ada.'])->withInput();
        }

        Kelas::create($data);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kelas $class): View
    {
        $class->load(['waliKelas']);
        $class->loadCount('siswa');

        return view('admin.kelas.show', compact('class'));
    }

    public function edit(Kelas $class): View
    {
        $class->load('waliKelas');
        $identifier = trim((string) str_replace($class->tingkat, '', $class->nama));
        $homeroomTeachers = Pengguna::where('peran', 'wali_kelas')
            ->get();

        return view('admin.kelas.edit', compact('class', 'identifier', 'homeroomTeachers'));
    }

    public function update(UpdateClassRequest $request, Kelas $class): RedirectResponse
    {
        $data = $request->validated();
        $pengenal = $data['nama'];
        $separator = is_numeric($pengenal) ? '. ' : ' ';
        $data['nama'] = $data['tingkat'].$separator.$pengenal;

        $exists = Kelas::where('nama', $data['nama'])->where('tingkat', $data['tingkat'])
            ->where('id', '!=', $class->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['nama' => 'Kelas dengan kombinasi ini sudah ada.'])->withInput();
        }

        $class->update($data);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $class): RedirectResponse
    {
        $class->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function deleteAll(): RedirectResponse
    {
        Kelas::query()->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Semua kelas berhasil dihapus.');
    }
}
