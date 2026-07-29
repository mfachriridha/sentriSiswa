<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Siswa\StoreSiswaRequest;
use App\Http\Requests\Siswa\UpdateSiswaRequest;
use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterGrade = $request->get('tingkat', '');
        $filterStatus = $request->get('status', '');
        $sort = $request->get('sort', 'dibuat_pada');
        $direction = $request->get('direction', 'desc');
        $allowed = ['nama', 'email', 'dibuat_pada', 'nisn', 'nis', 'class_name'];
        $sort = in_array($sort, $allowed) ? $sort : 'dibuat_pada';
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

        if ($filterGrade === 'tanpa_kelas') {
            $students->whereHas('profilSiswa', fn ($q) => $q->whereNull('kelas_id'));
        } elseif ($filterGrade) {
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

    public function store(StoreSiswaRequest $request): RedirectResponse
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
                'jenis_kelamin' => $request->jenis_kelamin,
                'kelas_id' => $request->kelas_id,
                'telepon' => $request->telepon,
                'alamat' => $request->alamat,
            ]);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(Pengguna $siswa): View
    {
        $siswa->load('profilSiswa.kelas');

        return view('admin.siswa.show', compact('siswa'));
    }

    public function edit(Pengguna $siswa): View
    {
        $siswa->load('profilSiswa');
        $classes = Kelas::orderBy('tingkat')->orderBy('nama')->get();

        return view('admin.siswa.edit', compact('siswa', 'classes'));
    }

    public function update(UpdateSiswaRequest $request, Pengguna $siswa): RedirectResponse
    {
        DB::transaction(function () use ($request, $siswa) {
            $siswa->update([
                'nama' => $request->nama,
            ]);

            $siswa->profilSiswa()->updateOrCreate(
                ['pengguna_id' => $siswa->id],
                [
                    'nisn' => $request->nisn,
                    'nis' => $request->nis,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'kelas_id' => $request->kelas_id,
                    'telepon' => $request->telepon,
                    'alamat' => $request->alamat,
                ],
            );
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Pengguna $siswa): RedirectResponse
    {
        $siswa->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function deleteAll(): RedirectResponse
    {
        ProfilSiswa::whereHas('pengguna', fn ($q) => $q->where('peran', 'siswa'))->delete();
        Pengguna::where('peran', 'siswa')->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Semua siswa berhasil dihapus.');
    }
}
