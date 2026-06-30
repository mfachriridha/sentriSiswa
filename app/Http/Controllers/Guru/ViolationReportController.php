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

class ViolationReportController extends Controller
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

        $rows = $violations->map(fn (PelanggaranSiswa $violation): array => [
            $violation->tanggal_pelanggaran->format('Y-m-d'),
            $violation->profilSiswa?->pengguna?->nama ?? '-',
            $violation->profilSiswa?->nis ?? '-',
            $violation->profilSiswa?->kelas?->nama ?? '-',
            $violation->nama_pelanggaran,
            JenisPelanggaran::categoryLabels()[$violation->kategori_pelanggaran] ?? $violation->kategori_pelanggaran,
            '-'.$violation->pengurangan_poin,
            PelanggaranSiswa::statusLabels()[$violation->status] ?? $violation->status,
            $violation->dicatatOleh?->nama ?? '-',
        ])->values()->all();

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
            'chartRows' => $this->statusChartRows($violations),
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
            ->when($filters['start_date'] ?? null, fn ($query, $date) => $query->whereDate('tanggal_pelanggaran', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($query, $date) => $query->whereDate('tanggal_pelanggaran', '<=', $date))
            ->when($filters['class_id'] ?? null, fn ($query, $classId) => $query->whereHas('profilSiswa', fn ($studentQuery) => $studentQuery->where('kelas_id', $classId)))
            ->when($filters['grade'] ?? null, fn ($query, $grade) => $query->whereHas('profilSiswa.kelas', fn ($classQuery) => $classQuery->where('tingkat', $grade)))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('kategori_pelanggaran', $category))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status));

        if ($user->isBk()) {
            $query->whereHas('profilSiswa.kelas', fn (Builder $classQuery) => $classQuery->where('tingkat', $user->profilGuru?->tingkat));
            $classes = Kelas::where('tingkat', $user->profilGuru?->tingkat)->orderBy('nama')->get();
            $filters['grade'] = $user->profilGuru?->tingkat;
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
     * @return list<array{0: string, 1: int}>
     */
    private function statusChartRows(iterable $violations): array
    {
        $collection = collect($violations);
        $labels = PelanggaranSiswa::statusLabels();

        return collect(['pending', 'approved', 'rejected'])
            ->map(fn (string $status): array => [$labels[$status], $collection->where('status', $status)->count()])
            ->all();
    }
}
