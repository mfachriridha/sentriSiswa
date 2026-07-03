<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentViolation\StoreStudentViolationRequest;
use App\Http\Requests\StudentViolation\UpdateStudentViolationRequest;
use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\ProfilSiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentViolationController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterClass = $request->get('kelas_id', '');
        $filterCategory = $request->get('category', '');
        $filterViolationType = $request->get('jenis_pelanggaran_id', '');
        $filterDate = $request->get('tanggal_pelanggaran', '');
        $filterStatus = $request->get('status', $request->route('status', ''));
        $sort = $request->get('sort', 'tanggal_pelanggaran');
        $direction = $request->get('direction', 'desc');
        $allowed = ['tanggal_pelanggaran', 'nama_pelanggaran', 'kategori_pelanggaran', 'pengurangan_poin', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'tanggal_pelanggaran';
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        $studentViolations = PelanggaranSiswa::with(['profilSiswa.pengguna', 'profilSiswa.kelas', 'dicatatOleh']);

        if ($search) {
            $studentViolations->where(function ($query) use ($search) {
                $query->where('nama_pelanggaran', 'like', "%{$search}%")
                    ->orWhereHas('profilSiswa', fn ($profileQuery) => $profileQuery
                        ->where('nisn', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%"))
                    ->orWhereHas('profilSiswa.pengguna', fn ($userQuery) => $userQuery->where('nama', 'like', "%{$search}%"));
            });
        }

        if ($filterClass) {
            $studentViolations->whereHas('profilSiswa', fn ($query) => $query->where('kelas_id', $filterClass));
        }

        if (array_key_exists($filterCategory, JenisPelanggaran::categoryLabels())) {
            $studentViolations->where('kategori_pelanggaran', $filterCategory);
        }

        if ($filterViolationType) {
            $studentViolations->where('jenis_pelanggaran_id', $filterViolationType);
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate) === 1) {
            $studentViolations->whereDate('tanggal_pelanggaran', $filterDate);
        }

        if (array_key_exists($filterStatus, PelanggaranSiswa::statusLabels())) {
            $studentViolations->where('status', $filterStatus);
        }

        if ($sort === 'kategori_pelanggaran') {
            $studentViolations = $studentViolations
                ->orderByRaw("CASE kategori_pelanggaran WHEN 'light' THEN 1 WHEN 'medium' THEN 2 WHEN 'heavy' THEN 3 WHEN 'severe' THEN 4 ELSE 5 END {$direction}")
                ->orderBy('pengurangan_poin', $direction)
                ->orderBy('nama_pelanggaran');
        } else {
            $studentViolations = $studentViolations->orderBy($sort, $direction);
        }

        $studentViolations = $studentViolations->paginate(25)->appends([
            'search' => $search,
            'kelas_id' => $filterClass,
            'category' => $filterCategory,
            'jenis_pelanggaran_id' => $filterViolationType,
            'tanggal_pelanggaran' => $filterDate,
            'status' => $filterStatus,
            'sort' => $sort,
            'direction' => $direction,
        ]);

        $classes = Kelas::orderBy('tingkat')->orderBy('nama')->get();
        $violationTypes = $this->violationTypes();
        $categoryLabels = JenisPelanggaran::categoryLabels();
        $statusLabels = PelanggaranSiswa::statusLabels();

        return view('kesiswaan.pelanggaran-siswa.index', compact('studentViolations', 'classes', 'violationTypes', 'categoryLabels', 'statusLabels', 'sort', 'direction', 'search', 'filterClass', 'filterCategory', 'filterViolationType', 'filterDate', 'filterStatus'));
    }

    public function create(): View
    {
        $students = $this->students();
        $violationTypes = $this->violationTypes(activeOnly: true);
        $categoryLabels = JenisPelanggaran::categoryLabels();

        return view('kesiswaan.pelanggaran-siswa.create', compact('students', 'violationTypes', 'categoryLabels'));
    }

    public function store(StoreStudentViolationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $violationType = JenisPelanggaran::findOrFail($data['jenis_pelanggaran_id']);

        PelanggaranSiswa::create($this->violationData($data, $violationType, includeRecorder: true, approved: true));

        return redirect()->route('kesiswaan.pelanggaran-siswa.index')->with('success', 'Pelanggaran siswa berhasil dicatat.');
    }

    public function show(PelanggaranSiswa $studentViolation): View
    {
        $studentViolation->load(['profilSiswa.pengguna', 'profilSiswa.kelas', 'jenisPelanggaran', 'dicatatOleh', 'disetujuiOleh']);
        $categoryLabels = JenisPelanggaran::categoryLabels();
        $statusLabels = PelanggaranSiswa::statusLabels();

        return view('kesiswaan.pelanggaran-siswa.show', compact('studentViolation', 'categoryLabels', 'statusLabels'));
    }

    public function edit(PelanggaranSiswa $studentViolation): View
    {
        $studentViolation->load(['profilSiswa.pengguna', 'profilSiswa.kelas', 'jenisPelanggaran']);
        $students = $this->students();
        $violationTypes = $this->violationTypes(activeOnly: true, currentViolationType: $studentViolation->jenisPelanggaran);
        $categoryLabels = JenisPelanggaran::categoryLabels();

        return view('kesiswaan.pelanggaran-siswa.edit', compact('studentViolation', 'students', 'violationTypes', 'categoryLabels'));
    }

    public function update(UpdateStudentViolationRequest $request, PelanggaranSiswa $studentViolation): RedirectResponse
    {
        $data = $request->validated();
        $violationType = JenisPelanggaran::findOrFail($data['jenis_pelanggaran_id']);

        $studentViolation->update($this->violationData($data, $violationType));

        return redirect()->route('kesiswaan.pelanggaran-siswa.index')->with('success', 'Pelanggaran siswa berhasil diperbarui.');
    }

    public function destroy(PelanggaranSiswa $studentViolation): RedirectResponse
    {
        $studentViolation->delete();

        return redirect()->route('kesiswaan.pelanggaran-siswa.index')->with('success', 'Pelanggaran siswa berhasil dihapus.');
    }

    private function students(): Collection
    {
        return ProfilSiswa::with(['pengguna', 'kelas'])
            ->whereHas('pengguna', fn ($query) => $query->where('peran', 'siswa'))
            ->get()
            ->sortBy(fn (ProfilSiswa $profilSiswa) => $profilSiswa->pengguna?->nama ?? '')
            ->values();
    }

    private function violationTypes(bool $activeOnly = false, ?JenisPelanggaran $currentViolationType = null): Collection
    {
        $query = JenisPelanggaran::query();

        if ($activeOnly || $currentViolationType) {
            $query->where(function ($query) use ($currentViolationType) {
                $query->where('aktif', true);

                if ($currentViolationType) {
                    $query->orWhere('id', $currentViolationType->id);
                }
            });
        }

        return $query
            ->orderByRaw("CASE kategori WHEN 'light' THEN 1 WHEN 'medium' THEN 2 WHEN 'heavy' THEN 3 WHEN 'severe' THEN 4 ELSE 5 END")
            ->orderBy('pengurangan_poin')
            ->orderBy('nama')
            ->get();
    }

    /**
     * @param  array{profil_siswa_id: int|string, jenis_pelanggaran_id: int|string, tanggal_pelanggaran: string, catatan?: string|null}  $data
     * @return array<string, mixed>
     */
    private function violationData(array $data, JenisPelanggaran $violationType, bool $includeRecorder = false, bool $approved = false): array
    {
        $violationData = [
            'profil_siswa_id' => $data['profil_siswa_id'],
            'jenis_pelanggaran_id' => $violationType->id,
            'tanggal_pelanggaran' => $data['tanggal_pelanggaran'],
            'nama_pelanggaran' => $violationType->nama,
            'kategori_pelanggaran' => $violationType->kategori,
            'pengurangan_poin' => $violationType->pengurangan_poin,
            'catatan' => $data['catatan'] ?? null,
        ];

        if ($includeRecorder) {
            $violationData['dicatat_oleh_id'] = Auth::id();
        }

        if ($approved) {
            $violationData['status'] = 'approved';
            $violationData['disetujui_oleh_id'] = Auth::id();
            $violationData['disetujui_pada'] = now();
        }

        return $violationData;
    }
}
