<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SimpanKelasRequest;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KelasController extends Controller
{
    public function index(Request $request): Response
    {
        $kelas = Kelas::with(['waliKelas', 'siswa'])
            ->withCount('siswa')
            ->when($request->cari, function ($query, $cari) {
                $query->where('nama', 'like', "%{$cari}%");
            })
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/kelas/Index', [
            'kelas' => $kelas,
            'cari' => $request->cari,
        ]);
    }

    public function create(): Response
    {
        $waliKelas = User::where('peran', User::PERAN_WALI_KELAS)->orderBy('nama')->get();

        return Inertia::render('admin/kelas/Create', [
            'waliKelas' => $waliKelas,
        ]);
    }

    public function store(SimpanKelasRequest $request): RedirectResponse
    {
        Kelas::create($request->validated());

        return redirect()->route('admin.kelas.index')->with('toast', ['type' => 'success', 'message' => 'Kelas berhasil ditambahkan.']);
    }

    public function show(Kelas $kelas): Response
    {
        $kelas->load(['waliKelas', 'siswa.pengguna']);

        return Inertia::render('admin/kelas/Show', [
            'kelas' => $kelas,
        ]);
    }

    public function edit(Kelas $kelas): Response
    {
        $waliKelas = User::where('peran', User::PERAN_WALI_KELAS)->orderBy('nama')->get();

        return Inertia::render('admin/kelas/Edit', [
            'kelas' => $kelas,
            'waliKelas' => $waliKelas,
        ]);
    }

    public function update(SimpanKelasRequest $request, Kelas $kelas): RedirectResponse
    {
        $kelas->update($request->validated());

        return redirect()->route('admin.kelas.index')->with('toast', ['type' => 'success', 'message' => 'Kelas berhasil diperbarui.']);
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('toast', ['type' => 'success', 'message' => 'Kelas berhasil dihapus.']);
    }

    public function hapusSemua(): RedirectResponse
    {
        Kelas::query()->delete();

        return redirect()->route('admin.kelas.index')->with('toast', ['type' => 'success', 'message' => 'Semua kelas berhasil dihapus.']);
    }
}
