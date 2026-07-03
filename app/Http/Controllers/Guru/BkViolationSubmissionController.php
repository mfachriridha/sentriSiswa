<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentViolation\StoreStudentViolationRequest;
use App\Models\JenisPelanggaran;
use App\Models\PelanggaranSiswa;
use App\Models\ProfilSiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BkViolationSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $grade = Auth::user()->profilGuru?->tingkat;
        $status = $request->get('status', '');

        $studentViolations = PelanggaranSiswa::with(['profilSiswa.pengguna', 'profilSiswa.kelas', 'dicatatOleh', 'disetujuiOleh'])
            ->whereHas('profilSiswa.kelas', fn ($query) => $query->where('tingkat', $grade))
            ->where('dicatat_oleh_id', Auth::id())
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('tanggal_pelanggaran')
            ->paginate(20)
            ->withQueryString();

        $statusLabels = PelanggaranSiswa::statusLabels();

        return view('bk.pelanggaran.index', compact('studentViolations', 'status', 'statusLabels', 'grade'));
    }

    public function create(Request $request): View
    {
        $students = $this->students();
        $violationTypes = JenisPelanggaran::where('aktif', true)
            ->orderBy('pengurangan_poin')
            ->orderBy('nama')
            ->get();
        $categoryLabels = JenisPelanggaran::categoryLabels();
        $selectedStudentId = $request->get('profil_siswa_id', '');

        return view('bk.pelanggaran.create', compact('students', 'violationTypes', 'categoryLabels', 'selectedStudentId'));
    }

    public function store(StoreStudentViolationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $student = ProfilSiswa::with('kelas')->findOrFail($data['profil_siswa_id']);
        $this->authorizeStudentGrade($student);

        $violationType = JenisPelanggaran::findOrFail($data['jenis_pelanggaran_id']);

        PelanggaranSiswa::create([
            'profil_siswa_id' => $student->nisn,
            'jenis_pelanggaran_id' => $violationType->id,
            'dicatat_oleh_id' => Auth::id(),
            'tanggal_pelanggaran' => $data['tanggal_pelanggaran'],
            'nama_pelanggaran' => $violationType->nama,
            'kategori_pelanggaran' => $violationType->kategori,
            'pengurangan_poin' => $violationType->pengurangan_poin,
            'catatan' => $data['catatan'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('bk.pelanggaran.index')->with('success', 'Pengajuan pelanggaran berhasil dikirim ke kesiswaan.');
    }

    /**
     * @return Collection<int, ProfilSiswa>
     */
    private function students(): Collection
    {
        $grade = Auth::user()->profilGuru?->tingkat;

        return ProfilSiswa::with(['pengguna', 'kelas'])
            ->whereHas('kelas', fn ($query) => $query->where('tingkat', $grade))
            ->whereHas('pengguna', fn ($query) => $query->where('peran', 'siswa'))
            ->get()
            ->sortBy(fn (ProfilSiswa $profilSiswa) => $profilSiswa->pengguna?->nama ?? '')
            ->values();
    }

    private function authorizeStudentGrade(ProfilSiswa $student): void
    {
        abort_unless($student->kelas?->tingkat === Auth::user()->profilGuru?->tingkat, 403);
    }
}
