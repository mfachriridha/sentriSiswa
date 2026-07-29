<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreGuruRequest;
use App\Http\Requests\Guru\UpdateGuruRequest;
use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilGuru;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
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
        $sort = $request->get('sort', 'dibuat_pada');
        $direction = $request->get('direction', 'desc');
        $allowed = ['nama', 'email', 'dibuat_pada', 'nip', 'peran', 'telepon', 'class_name'];
        $sort = in_array($sort, $allowed) ? $sort : 'dibuat_pada';
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
        $availableKelas = Kelas::whereNull('wali_kelas_id')->orderBy('tingkat')->orderBy('nama')->get();

        return view('admin.guru.create', compact('availableKelas'));
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
                'nip' => $request->nip,
                'tipe_guru' => $request->peran,
                'tingkat' => $request->peran === 'bk' ? $request->tingkat : null,
            ]);

            if ($request->peran === 'wali_kelas' && $request->kelas_id) {
                Kelas::where('id', $request->kelas_id)->update(['wali_kelas_id' => $user->id]);
            }
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
        $guru->load(['profilGuru', 'kelasWali']);

        $availableKelas = Kelas::where(fn ($q) => $q->whereNull('wali_kelas_id')->orWhere('wali_kelas_id', $guru->id))
            ->orderBy('tingkat')->orderBy('nama')->get();

        return view('admin.guru.edit', compact('guru', 'availableKelas'));
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
                    'nip' => $request->nip,
                    'tipe_guru' => $request->peran,
                    'tingkat' => $request->peran === 'bk' ? $request->tingkat : null,
                ],
            );

            Kelas::where('wali_kelas_id', $guru->id)->update(['wali_kelas_id' => null]);

            if ($request->peran === 'wali_kelas' && $request->kelas_id) {
                Kelas::where('id', $request->kelas_id)->update(['wali_kelas_id' => $guru->id]);
            }
        });

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(Pengguna $guru): RedirectResponse
    {
        $guru->delete();

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil dihapus.');
    }

    /**
     * Hitung berapa guru yang cocok dengan kriteria yang dicentang, dipakai
     * modal buat nampilin pratinjau sebelum admin benar-benar menghapus.
     */
    public function previewDeleteAll(Request $request): JsonResponse
    {
        $filters = $this->deleteAllFilters($request);

        return response()->json(['count' => $this->deleteAllQuery($filters)->count()]);
    }

    public function deleteAll(Request $request): RedirectResponse
    {
        $filters = $this->deleteAllFilters($request);

        if ($filters['peran'] === [] && $filters['status'] === [] && $filters['tingkat'] === []) {
            return redirect()->route('admin.guru.index')->with('error', 'Pilih minimal satu kriteria yang mau dihapus.');
        }

        $ids = $this->deleteAllQuery($filters)->pluck('id');

        if ($ids->isEmpty()) {
            return redirect()->route('admin.guru.index')->with('error', 'Tidak ada guru yang cocok dengan kriteria yang dipilih.');
        }

        DB::transaction(function () use ($ids) {
            ProfilGuru::whereIn('pengguna_id', $ids)->delete();
            Pengguna::whereIn('id', $ids)->delete();
        });

        return redirect()->route('admin.guru.index')->with('success', "{$ids->count()} guru berhasil dihapus.");
    }

    /**
     * Guru yang boleh dihapus massal disaring dari kriteria yang dicentang di
     * modal - checkbox kosong di satu grup berarti grup itu tidak membatasi
     * (cocok semua), bukan berarti "tidak ada yang dihapus".
     *
     * @return array{peran: list<string>, status: list<string>, tingkat: list<string>}
     */
    private function deleteAllFilters(Request $request): array
    {
        $validated = $request->validate([
            'peran' => ['array'],
            'peran.*' => ['in:wali_kelas,bk,kesiswaan'],
            'status' => ['array'],
            'status.*' => ['in:registered,unregistered'],
            'tingkat' => ['array'],
            'tingkat.*' => ['in:10,11,12'],
        ]);

        return [
            'peran' => $validated['peran'] ?? [],
            'status' => $validated['status'] ?? [],
            'tingkat' => $validated['tingkat'] ?? [],
        ];
    }

    /**
     * @param  array{peran: list<string>, status: list<string>, tingkat: list<string>}  $filters
     * @return Builder<Pengguna>
     */
    private function deleteAllQuery(array $filters): Builder
    {
        $query = Pengguna::whereIn('peran', ['wali_kelas', 'bk', 'kesiswaan']);

        if ($filters['peran'] !== []) {
            $query->whereIn('peran', $filters['peran']);
        }

        if ($filters['status'] !== []) {
            $query->whereIn('status', $filters['status']);
        }

        if ($filters['tingkat'] !== []) {
            $query->whereHas('profilGuru', fn ($q) => $q->whereIn('tingkat', $filters['tingkat']));
        }

        return $query;
    }
}
