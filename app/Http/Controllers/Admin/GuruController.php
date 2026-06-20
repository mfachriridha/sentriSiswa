<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SimpanGuruRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuruController extends Controller
{
    public function index(Request $request): Response
    {
        $guru = User::whereIn('peran', [User::PERAN_WALI_KELAS, User::PERAN_BK, User::PERAN_KESISWAAN])
            ->when($request->cari, function ($query, $cari) {
                $query->where('nama', 'like', "%{$cari}%")->orWhere('email', 'like', "%{$cari}%");
            })
            ->when($request->peran, function ($query, $peran) {
                $query->where('peran', $peran);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/guru/Index', [
            'guru' => $guru,
            'cari' => $request->cari,
            'peranFilter' => $request->peran,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/guru/Create');
    }

    public function store(SimpanGuruRequest $request): RedirectResponse
    {
        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password ?? 'password'),
            'peran' => $request->peran,
            'status' => User::STATUS_TERDAFTAR,
        ]);

        return redirect()->route('admin.guru.index')->with('toast', ['type' => 'success', 'message' => 'Guru berhasil ditambahkan.']);
    }

    public function show(User $guru): Response
    {
        return Inertia::render('admin/guru/Show', [
            'guru' => $guru,
        ]);
    }

    public function edit(User $guru): Response
    {
        return Inertia::render('admin/guru/Edit', [
            'guru' => $guru,
        ]);
    }

    public function update(SimpanGuruRequest $request, User $guru): RedirectResponse
    {
        $guru->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'peran' => $request->peran,
        ]);

        if ($request->filled('password')) {
            $guru->update(['password' => bcrypt($request->password)]);
        }

        return redirect()->route('admin.guru.index')->with('toast', ['type' => 'success', 'message' => 'Guru berhasil diperbarui.']);
    }

    public function destroy(User $guru): RedirectResponse
    {
        $guru->delete();

        return redirect()->route('admin.guru.index')->with('toast', ['type' => 'success', 'message' => 'Guru berhasil dihapus.']);
    }

    public function hapusSemua(): RedirectResponse
    {
        User::whereIn('peran', [User::PERAN_WALI_KELAS, User::PERAN_BK, User::PERAN_KESISWAAN])->delete();

        return redirect()->route('admin.guru.index')->with('toast', ['type' => 'success', 'message' => 'Semua guru berhasil dihapus.']);
    }
}
