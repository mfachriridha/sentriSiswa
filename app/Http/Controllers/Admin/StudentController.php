<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
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
        $filterGrade = $request->get('tingkat', '');
        $filterStatus = $request->get('status', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $allowed = ['nama', 'email', 'created_at', 'nisn', 'nis', 'class_name'];
        $sort = in_array($sort, $allowed) ? $sort : 'created_at';
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        $students = Pengguna::where('peran', 'siswa')
            ->with('profilSiswa.kelas');

        if ($search) {
            $students->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhereHas('profilSiswa', fn ($q) => $q
                        ->where('nisn', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%"));
            });
        }

        if ($filterGrade) {
            $students->whereHas('profilSiswa.kelas', fn ($q) => $q->where('tingkat', $filterGrade));
        }

        if (in_array($filterStatus, ['registered', 'unregistered'], true)) {
            $students->where('status', $filterStatus);
        }

        if ($sort === 'class_name') {
            $students = $students->orderByRaw(
                "(SELECT k.nama FROM kelas k JOIN profil_siswa ps ON ps.kelas_id = k.id WHERE ps.pengguna_id = pengguna.id LIMIT 1) {$direction}"
            );
        } elseif (in_array($sort, ['nisn', 'nis'])) {
            $students = $students->orderBy(
                ProfilSiswa::select($sort)
                    ->whereColumn('profil_siswa.pengguna_id', 'pengguna.id')
                    ->limit(1),
                $direction,
            );
        } else {
            $students = $students->orderBy($sort, $direction);
        }

        $students = $students->paginate(25)->appends([
            'search' => $search,
            'tingkat' => $filterGrade,
            'status' => $filterStatus,
            'sort' => $sort,
            'direction' => $direction,
        ]);

        return view('admin.siswa.index', compact('students', 'sort', 'direction', 'search', 'filterGrade', 'filterStatus'));
    }

    public function create(): View
    {
        $classes = Kelas::orderBy('tingkat')->orderBy('nama')->get();

        return view('admin.siswa.create', compact('classes'));
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = [
                'nama' => $request->nama,
                'peran' => 'siswa',
                'status' => 'unregistered',
                'password' => Hash::make('password'),
            ];

            $user = Pengguna::create($data);

            $user->profilSiswa()->create([
                'nisn' => $request->nisn,
                'nis' => $request->nis,
                'kelas_id' => $request->kelas_id,
                'telepon' => $request->telepon,
                'alamat' => $request->alamat,
            ]);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(Pengguna $student): View
    {
        $student->load('profilSiswa.kelas');

        return view('admin.siswa.show', compact('student'));
    }

    public function edit(Pengguna $student): View
    {
        $student->load('profilSiswa');
        $classes = Kelas::orderBy('tingkat')->orderBy('nama')->get();

        return view('admin.siswa.edit', compact('student', 'classes'));
    }

    public function update(UpdateStudentRequest $request, Pengguna $student): RedirectResponse
    {
        DB::transaction(function () use ($request, $student) {
            $student->update([
                'nama' => $request->nama,
            ]);

            $student->profilSiswa()->updateOrCreate(
                ['pengguna_id' => $student->id],
                [
                    'nisn' => $request->nisn,
                    'nis' => $request->nis,
                    'kelas_id' => $request->kelas_id,
                    'telepon' => $request->telepon,
                    'alamat' => $request->alamat,
                ],
            );
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Pengguna $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function deleteAll(): RedirectResponse
    {
        ProfilSiswa::whereHas('pengguna', fn ($q) => $q->where('peran', 'siswa'))->delete();
        Pengguna::where('peran', 'siswa')->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Semua siswa berhasil dihapus.');
    }
}
