<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kelas\StoreKelasRequest;
use App\Http\Requests\Kelas\UpdateKelasRequest;
use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterGrade = $request->get('tingkat', '');
        $sort = $request->get('sort', 'nama');
        $direction = $request->get('direction', 'asc');
        $allowed = ['nama', 'tingkat', 'dibuat_pada'];
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
        $availableSiswa = ProfilSiswa::whereNull('kelas_id')->with('pengguna')->get();

        return view('admin.kelas.create', compact('homeroomTeachers', 'availableSiswa'));
    }

    public function store(StoreKelasRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $siswaNisn = $data['siswa_nisn'] ?? [];
        unset($data['siswa_nisn']);

        $pengenal = $data['nama'];
        $separator = is_numeric($pengenal) ? '. ' : ' ';
        $data['nama'] = $data['tingkat'].$separator.$pengenal;

        if (Kelas::where('nama', $data['nama'])->where('tingkat', $data['tingkat'])->exists()) {
            return back()->withErrors(['nama' => 'Kelas dengan kombinasi ini sudah ada.'])->withInput();
        }

        DB::transaction(function () use ($data, $siswaNisn) {
            $kelas = Kelas::create($data);

            if ($siswaNisn) {
                ProfilSiswa::whereIn('nisn', $siswaNisn)->update(['kelas_id' => $kelas->id]);
            }
        });

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kelas): View
    {
        $kelas->load(['waliKelas']);
        $kelas->loadCount('siswa');

        return view('admin.kelas.show', compact('kelas'));
    }

    public function edit(Kelas $kelas): View
    {
        $kelas->load('waliKelas');
        $identifier = trim((string) str_replace($kelas->tingkat, '', $kelas->nama));
        $homeroomTeachers = Pengguna::where('peran', 'wali_kelas')
            ->get();
        $availableSiswa = ProfilSiswa::where(fn ($q) => $q->whereNull('kelas_id')->orWhere('kelas_id', $kelas->id))
            ->with('pengguna')->get();

        return view('admin.kelas.edit', compact('kelas', 'identifier', 'homeroomTeachers', 'availableSiswa'));
    }

    public function update(UpdateKelasRequest $request, Kelas $kelas): RedirectResponse
    {
        $data = $request->validated();
        $siswaNisn = $data['siswa_nisn'] ?? [];
        unset($data['siswa_nisn']);

        $pengenal = $data['nama'];
        $separator = is_numeric($pengenal) ? '. ' : ' ';
        $data['nama'] = $data['tingkat'].$separator.$pengenal;

        $exists = Kelas::where('nama', $data['nama'])->where('tingkat', $data['tingkat'])
            ->where('id', '!=', $kelas->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['nama' => 'Kelas dengan kombinasi ini sudah ada.'])->withInput();
        }

        DB::transaction(function () use ($data, $siswaNisn, $kelas) {
            $kelas->update($data);

            ProfilSiswa::where('kelas_id', $kelas->id)->whereNotIn('nisn', $siswaNisn)->update(['kelas_id' => null]);

            if ($siswaNisn) {
                ProfilSiswa::whereIn('nisn', $siswaNisn)->update(['kelas_id' => $kelas->id]);
            }
        });

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function deleteAll(): RedirectResponse
    {
        Kelas::query()->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Semua kelas berhasil dihapus.');
    }
}
