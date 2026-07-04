<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreGuruRequest;
use App\Http\Requests\Guru\UpdateGuruRequest;
use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilGuru;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterRole = $request->get('peran', '');
        $filterGrade = $request->get('tingkat', '');
        $filterStatus = $request->get('status', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $allowed = ['nama', 'email', 'created_at', 'nip', 'peran', 'telepon', 'class_name'];
        $sort = in_array($sort, $allowed) ? $sort : 'created_at';
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        $teachers = Pengguna::whereIn('peran', ['wali_kelas', 'bk', 'kesiswaan'])
            ->with(['profilGuru', 'kelasWali']);

        if ($search) {
            $teachers->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhereHas('profilGuru', fn ($q) => $q->where('nip', 'like', "%{$search}%"));
            });
        }

        if (in_array($filterRole, ['wali_kelas', 'bk', 'kesiswaan'], true)) {
            $teachers->where('peran', $filterRole);
        }

        if ($filterGrade) {
            $teachers->where(function ($q) use ($filterGrade) {
                $q->whereHas('profilGuru', fn ($q) => $q->where('tingkat', $filterGrade))
                    ->orWhereHas('kelasWali', fn ($q) => $q->where('tingkat', $filterGrade));
            });
        }

        if (in_array($filterStatus, ['registered', 'unregistered'], true)) {
            $teachers->where('status', $filterStatus);
        }

        if ($sort === 'class_name') {
            $teachers = $teachers->orderBy(
                Kelas::select('nama')
                    ->whereColumn('wali_kelas_id', 'pengguna.id')
                    ->limit(1),
                $direction,
            );
        } elseif (in_array($sort, ['nip', 'telepon'])) {
            $teachers = $teachers->orderBy(
                ProfilGuru::select($sort)
                    ->whereColumn('profil_guru.pengguna_id', 'pengguna.id')
                    ->limit(1),
                $direction,
            );
        } else {
            $teachers = $teachers->orderBy($sort, $direction);
        }

        $teachers = $teachers->paginate(25)->appends([
            'search' => $search,
            'peran' => $filterRole,
            'tingkat' => $filterGrade,
            'status' => $filterStatus,
            'sort' => $sort,
            'direction' => $direction,
        ]);

        return view('admin.guru.index', compact('teachers', 'sort', 'direction', 'search', 'filterRole', 'filterGrade', 'filterStatus'));
    }

    public function create(): View
    {
        return view('admin.guru.create');
    }

    private function tipeGuru(string $peran): string
    {
        return match ($peran) {
            'wali_kelas' => 'homeroom',
            'bk'         => 'counselor',
            default      => 'student_affairs',
        };
    }

    public function store(StoreGuruRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = [
                'nama' => $request->nama,
                'peran' => $request->peran,
                'status' => 'unregistered',
                'password' => Hash::make('password'),
            ];

            $user = Pengguna::create($data);

            $user->profilGuru()->create([
                'nip'      => $request->nip,
                'telepon'  => $request->telepon,
                'tipe_guru'=> $this->tipeGuru($request->peran),
                'tingkat'  => $request->peran === 'bk' ? $request->tingkat : null,
            ]);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function show(Pengguna $guru): View
    {
        $guru->load(['profilGuru', 'kelasWali']);

        return view('admin.guru.show', compact('guru'));
    }

    public function edit(Pengguna $guru): View
    {
        $guru->load('profilGuru');

        return view('admin.guru.edit', compact('guru'));
    }

    public function update(UpdateGuruRequest $request, Pengguna $guru): RedirectResponse
    {
        DB::transaction(function () use ($request, $guru) {
            $guru->update([
                'nama' => $request->nama,
                'peran' => $request->peran,
            ]);

            $guru->profilGuru()->updateOrCreate(
                ['pengguna_id' => $guru->id],
                [
                    'nip'       => $request->nip,
                    'telepon'   => $request->telepon,
                    'tipe_guru' => $this->tipeGuru($request->peran),
                    'tingkat'   => $request->peran === 'bk' ? $request->tingkat : null,
                ],
            );
        });

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(Pengguna $guru): RedirectResponse
    {
        $guru->delete();

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil dihapus.');
    }

    public function deleteAll(): RedirectResponse
    {
        ProfilGuru::whereHas('pengguna', fn ($q) => $q->whereIn('peran', ['wali_kelas', 'bk', 'kesiswaan']))->delete();
        Pengguna::whereIn('peran', ['wali_kelas', 'bk', 'kesiswaan'])->delete();

        return redirect()->route('admin.guru.index')->with('success', 'Semua guru berhasil dihapus.');
    }
}
