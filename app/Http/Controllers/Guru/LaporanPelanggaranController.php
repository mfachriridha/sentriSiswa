<?php

namespace App\Http\Controllers\Guru;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\ViolationReportFilterRequest;
use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
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
        $categoryLabels = JenisPelanggaran::categoryLabels();
        $statusLabels = PelanggaranSiswa::statusLabels();
        $routeName = Auth::user()->isBk() ? 'bk.laporan' : 'kesiswaan.laporan';
        $title = Auth::user()->isBk() ? 'Laporan BK' : 'Laporan Kesiswaan';

        return view('kesiswaan.laporan-pelanggaran.index', compact('violations', 'filters', 'classes', 'categoryLabels', 'statusLabels', 'routeName', 'title'));
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
                PelanggaranSiswa::statusLabels()[$violation->status] ?? $violation->status,
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
            'Status',
            'Dicatat Oleh',
            'Sisa Poin Siswa',
            'Keterangan',
        ], $rows), 'laporan-pelanggaran.xlsx');
    }

    public function exportPdf(ViolationReportFilterRequest $request): Response
    {
        [$violations, $filters] = $this->reportData($request, paginated: false);
        $title = Auth::user()->isBk() ? 'Laporan BK' : 'Laporan Kesiswaan';

        $pdf = Pdf::loadView('exports.violation-report-pdf', [
            'title' => $title,
            'violations' => $violations,
            'filters' => $filters,
            'pointsSummary' => $this->studentPointsSummary($violations),
            'categoryLabels' => JenisPelanggaran::categoryLabels(),
            'statusLabels' => PelanggaranSiswa::statusLabels(),
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
            ->when($filters['kategori'] ?? null, fn ($query, $kategori) => $query->where('kategori_pelanggaran', $kategori))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status));

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
            ->map(fn ($group) => max(0, 100 - $group->sum('pengurangan_poin')))
            ->all();
    }

    /**
     * @return list<array{nama: string, nis: string, kelas: string, total_terpotong: int, sisa_poin: int}>
     */
    private function studentPointsSummary(iterable $violations): array
    {
        return collect($violations)
            ->where('status', 'approved')
            ->groupBy('profil_siswa_id')
            ->map(function ($group) {
                $first = $group->first();
                $totalDeducted = $group->sum('pengurangan_poin');

                return [
                    'nama' => $first->profilSiswa?->pengguna?->nama ?? '-',
                    'nis' => $first->profilSiswa?->nis ?? '-',
                    'kelas' => $first->profilSiswa?->kelas?->nama ?? '-',
                    'total_terpotong' => $totalDeducted,
                    'sisa_poin' => max(0, 100 - $totalDeducted),
                ];
            })
            ->sortBy('sisa_poin')
            ->values()
            ->all();
    }
}
