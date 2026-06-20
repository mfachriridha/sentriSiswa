<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SimpanSiswaRequest;
use App\Http\Requests\Admin\UpdateBiodataSiswaRequest;
use App\Models\BiodataSiswa;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SiswaController extends Controller
{
    public function index(Request $request): Response
    {
        $siswa = Siswa::with(['pengguna', 'kelas'])
            ->when($request->cari, function ($query, $cari) {
                $query->whereHas('pengguna', function ($q) use ($cari) {
                    $q->where('nama', 'like', "%{$cari}%");
                })->orWhere('nisn', 'like', "%{$cari}%")->orWhere('nis', 'like', "%{$cari}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/siswa/Index', [
            'siswa' => $siswa,
            'cari' => $request->cari,
        ]);
    }

    public function create(): Response
    {
        $kelas = Kelas::orderBy('nama')->get();

        return Inertia::render('admin/siswa/Create', [
            'kelas' => $kelas,
        ]);
    }

    public function store(SimpanSiswaRequest $request): RedirectResponse
    {
        $pengguna = User::create([
            'nama' => $request->nama,
            'email' => null,
            'password' => bcrypt('password'),
            'peran' => User::PERAN_SISWA,
            'status' => User::STATUS_BELUM_TERDAFTAR,
        ]);

        Siswa::create([
            'pengguna_id' => $pengguna->id,
            'nisn' => $request->nisn,
            'nis' => $request->nis,
            'kelas_id' => $request->kelas_id,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('admin.siswa.index')->with('toast', ['type' => 'success', 'message' => 'Siswa berhasil ditambahkan.']);
    }

    public function show(Siswa $siswa): Response
    {
        $siswa->load(['pengguna', 'kelas', 'biodata']);

        return Inertia::render('admin/siswa/Show', [
            'siswa' => $siswa,
        ]);
    }

    public function edit(Siswa $siswa): Response
    {
        $siswa->load(['pengguna', 'kelas']);
        $kelas = Kelas::orderBy('nama')->get();

        return Inertia::render('admin/siswa/Edit', [
            'siswa' => $siswa,
            'kelas' => $kelas,
        ]);
    }

    public function update(SimpanSiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $siswa->pengguna->update(['nama' => $request->nama]);
        $siswa->update($request->only('nisn', 'nis', 'kelas_id', 'telepon', 'alamat'));

        return redirect()->route('admin.siswa.index')->with('toast', ['type' => 'success', 'message' => 'Siswa berhasil diperbarui.']);
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }

        $siswa->pengguna->delete();
        $siswa->delete();

        return redirect()->route('admin.siswa.index')->with('toast', ['type' => 'success', 'message' => 'Siswa berhasil dihapus.']);
    }

    public function hapusSemua(): RedirectResponse
    {
        $semuaSiswa = Siswa::with('pengguna')->get();

        foreach ($semuaSiswa as $siswa) {
            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }

            $siswa->pengguna->delete();
            $siswa->delete();
        }

        return redirect()->route('admin.siswa.index')->with('toast', ['type' => 'success', 'message' => 'Semua siswa berhasil dihapus.']);
    }

    public function editBiodata(Siswa $siswa): Response
    {
        $siswa->load(['pengguna', 'biodata']);

        return Inertia::render('admin/siswa/Biodata', [
            'siswa' => $siswa,
        ]);
    }

    public function updateBiodata(UpdateBiodataSiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        BiodataSiswa::updateOrCreate(
            ['siswa_id' => $siswa->id],
            $request->validated(),
        );

        return redirect()->route('admin.siswa.show', $siswa)->with('toast', ['type' => 'success', 'message' => 'Biodata berhasil disimpan.']);
    }

    public function uploadFoto(Request $request, Siswa $siswa): RedirectResponse
    {
        $request->validate(['foto' => ['required', 'image', 'max:2048']]);

        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }

        $path = $request->file('foto')->store('foto-siswa', 'public');
        $siswa->update(['foto' => $path]);

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Foto berhasil diunggah.']);
    }

    public function hapusFoto(Siswa $siswa): RedirectResponse
    {
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
            $siswa->update(['foto' => null]);
        }

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Foto berhasil dihapus.']);
    }
}
