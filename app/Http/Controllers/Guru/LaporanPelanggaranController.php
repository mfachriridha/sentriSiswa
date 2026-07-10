<?php

namespace App\Http\Controllers\Guru;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\ViolationReportFilterRequest;
use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\PengajuanPoin;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class LaporanPelanggaranController extends Controller
{
    public function index(ViolationReportFilterRequest $request): View
    {
        [$violations, $filters, $classes] = $this->reportData($request);
        $user = Auth::user()->loadMissing('profilGuru');
        $pengajuanPoin = $this->approvedPengajuanPoinQuery($filters, $user)
            ->paginate(15, ['*'], 'pengajuan_page')
            ->withQueryString();
        $categoryLabels = JenisPelanggaran::categoryLabels();
        $routeName = $user->isBk() ? 'bk.laporan' : 'kesiswaan.laporan';
        $title = $user->isBk() ? 'Laporan BK' : 'Laporan Kesiswaan';

        return view('kesiswaan.laporan-pelanggaran.index', compact('violations', 'filters', 'classes', 'categoryLabels', 'pengajuanPoin', 'routeName', 'title'));
    }

    public function exportExcel(ViolationReportFilterRequest $request): BinaryFileResponse
    {
        [$violations] = $this->reportData($request, paginated: false);

        $remainingPoints = $this->remainingPointsByStudent($violations);

        $rows = $violations->map(function (PelanggaranSiswa $violation) use ($remainingPoints): array {
            $sisaPoin = $remainingPoints[$violation->profil_siswa_id] ?? 100;

            return [
                $violation->tanggal_pelanggaran->format('Y-m-d'),
                $violation->profilSiswa?->pengguna?->nama ?? '-',
                $violation->profilSiswa?->nis ?? '-',
                $violation->profilSiswa?->kelas?->nama ?? '-',
                $violation->nama_pelanggaran,
                JenisPelanggaran::categoryLabels()[$violation->kategori_pelanggaran] ?? $violation->kategori_pelanggaran,
                '-'.$violation->pengurangan_poin,
                $violation->dicatatOleh?->nama ?? '-',
                $sisaPoin,
                $sisaPoin <= 50 ? 'Perhatian' : '-',
            ];
        })->values()->all();

        return Excel::download(new ArrayExport([
            'Tanggal',
            'Nama',
            'NIS',
            'Kelas',
            'Pelanggaran',
            'Kategori',
            'Poin',
            'Dicatat Oleh',
            'Sisa Poin Siswa',
            'Keterangan',
        ], $rows), 'laporan-pelanggaran.xlsx');
    }

    public function exportPdf(ViolationReportFilterRequest $request): Response
    {
        [$violations, $filters] = $this->reportData($request, paginated: false);
        $user = Auth::user()->loadMissing('profilGuru');
        $title = $user->isBk() ? 'Laporan BK' : 'Laporan Kesiswaan';

        $pdf = Pdf::loadView('exports.violation-report-pdf', [
            'title' => $title,
            'violations' => $violations,
            'filters' => $filters,
            'pengajuanPoin' => $this->approvedPengajuanPoinQuery($filters, $user)->get(),
            'pointsSummary' => $this->allStudentsPointsSummary($filters, $user),
            'categoryLabels' => JenisPelanggaran::categoryLabels(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pelanggaran.pdf');
    }

    private function reportData(ViolationReportFilterRequest $request, bool $paginated = true): array
    {
        $filters = $request->validated();
        $user = Auth::user()->loadMissing('profilGuru');

        $query = PelanggaranSiswa::with(['profilSiswa.pengguna', 'profilSiswa.kelas', 'dicatatOleh', 'disetujuiOleh'])
            ->when($filters['mulai'] ?? null, fn ($query, $date) => $query->whereDate('tanggal_pelanggaran', '>=', $date))
            ->when($filters['selesai'] ?? null, fn ($query, $date) => $query->whereDate('tanggal_pelanggaran', '<=', $date))
            ->when($filters['kelas_id'] ?? null, fn ($query, $classId) => $query->whereHas('profilSiswa', fn ($studentQuery) => $studentQuery->where('kelas_id', $classId)))
            ->when($filters['tingkat'] ?? null, fn ($query, $grade) => $query->whereHas('profilSiswa.kelas', fn ($classQuery) => $classQuery->where('tingkat', $grade)))
            ->when($filters['kategori'] ?? null, fn ($query, $kategori) => $query->where('kategori_pelanggaran', $kategori));

        if ($user->isBk()) {
            $query->whereHas('profilSiswa.kelas', fn (Builder $classQuery) => $classQuery->where('tingkat', $user->profilGuru?->tingkat));
            $classes = Kelas::where('tingkat', $user->profilGuru?->tingkat)->orderBy('nama')->get();
            $filters['tingkat'] = $user->profilGuru?->tingkat;
        } else {
            $classes = Kelas::orderBy('tingkat')->orderBy('nama')->get();
        }

        $query->latest('tanggal_pelanggaran');

        return [
            $paginated ? $query->paginate(25)->withQueryString() : $query->get(),
            $filters,
            $classes,
        ];
    }

    /**
     * @return array<string, int>
     */
    private function remainingPointsByStudent(iterable $violations): array
    {
        return collect($violations)
            ->where('status', 'approved')
            ->groupBy('profil_siswa_id')
            ->map(fn ($group) => $group->first()->profilSiswa?->poin ?? 100)
            ->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<PengajuanPoin>
     */
    private function approvedPengajuanPoinQuery(array $filters, Pengguna $user): Builder
    {
        $query = PengajuanPoin::with(['profilSiswa.pengguna', 'profilSiswa.kelas', 'diajukanOleh', 'disetujuiOleh'])
            ->where('status', 'approved')
            ->when($filters['mulai'] ?? null, fn ($query, $date) => $query->whereDate('disetujui_pada', '>=', $date))
            ->when($filters['selesai'] ?? null, fn ($query, $date) => $query->whereDate('disetujui_pada', '<=', $date))
            ->when($filters['kelas_id'] ?? null, fn ($query, $classId) => $query->whereHas('profilSiswa', fn ($studentQuery) => $studentQuery->where('kelas_id', $classId)))
            ->when($filters['tingkat'] ?? null, fn ($query, $grade) => $query->whereHas('profilSiswa.kelas', fn ($classQuery) => $classQuery->where('tingkat', $grade)));

        if ($user->isBk()) {
            $query->whereHas('profilSiswa.kelas', fn (Builder $classQuery) => $classQuery->where('tingkat', $user->profilGuru?->tingkat));
        }

        return $query->latest('disetujui_pada');
    }

    /**
     * Snapshot sisa poin SEMUA siswa (bukan cuma yang punya pelanggaran di filter tanggal/kategori
     * yang lagi jalan) - discope ke kelas/tingkat aja, karena ini kondisi sekarang bukan riwayat kejadian.
     *
     * @return list<array{nama: string, nis: string, kelas: string, sisa_poin: int}>
     */
    private function allStudentsPointsSummary(array $filters, Pengguna $user): array
    {
        $query = ProfilSiswa::with(['pengguna', 'kelas'])
            ->withSum(['pelanggaranSiswa' => fn ($query) => $query->disetujui()], 'pengurangan_poin')
            ->withSum(['pengajuanPoin' => fn ($query) => $query->disetujui()], 'jumlah_poin')
            ->when($filters['kelas_id'] ?? null, fn ($query, $classId) => $query->where('kelas_id', $classId))
            ->when($filters['tingkat'] ?? null, fn ($query, $grade) => $query->whereHas('kelas', fn ($classQuery) => $classQuery->where('tingkat', $grade)));

        if ($user->isBk()) {
            $query->whereHas('kelas', fn (Builder $classQuery) => $classQuery->where('tingkat', $user->profilGuru?->tingkat));
        }

        return $query->get()
            ->map(fn (ProfilSiswa $student): array => [
                'nama' => $student->pengguna?->nama ?? '-',
                'nis' => $student->nis ?? '-',
                'kelas' => $student->kelas?->nama ?? '-',
                'sisa_poin' => $student->poin,
            ])
            ->sortBy('sisa_poin')
            ->values()
            ->all();
    }
}
