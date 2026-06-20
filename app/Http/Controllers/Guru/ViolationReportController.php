<?php

namespace App\Http\Controllers\Guru;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\ViolationReportFilterRequest;
use App\Models\SchoolClass;
use App\Models\StudentViolation;
use App\Models\ViolationType;
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
        $categoryLabels = ViolationType::categoryLabels();
        $statusLabels = StudentViolation::statusLabels();
        $routeName = Auth::user()->isBk() ? 'bk.laporan' : 'kesiswaan.laporan';
        $title = Auth::user()->isBk() ? 'Laporan BK' : 'Laporan Kesiswaan';

        return view('kesiswaan.laporan-pelanggaran.index', compact('violations', 'filters', 'classes', 'categoryLabels', 'statusLabels', 'routeName', 'title'));
    }

    public function exportExcel(ViolationReportFilterRequest $request): BinaryFileResponse
    {
        [$violations] = $this->reportData($request, paginated: false);

        $rows = $violations->map(fn (StudentViolation $violation): array => [
            $violation->violation_date->format('Y-m-d'),
            $violation->studentProfile?->user?->name ?? '-',
            $violation->studentProfile?->nis ?? '-',
            $violation->studentProfile?->class?->name ?? '-',
            $violation->violation_name,
            ViolationType::categoryLabels()[$violation->violation_category] ?? $violation->violation_category,
            $violation->point_deduction,
            StudentViolation::statusLabels()[$violation->status] ?? $violation->status,
            $violation->recordedBy?->name ?? '-',
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
            'categoryLabels' => ViolationType::categoryLabels(),
            'statusLabels' => StudentViolation::statusLabels(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pelanggaran.pdf');
    }

    private function reportData(ViolationReportFilterRequest $request, bool $paginated = true): array
    {
        $filters = $request->validated();
        $user = Auth::user()->loadMissing('teacherProfile');

        $query = StudentViolation::with(['studentProfile.user', 'studentProfile.class', 'recordedBy', 'approvedBy'])
            ->when($filters['start_date'] ?? null, fn ($query, $date) => $query->whereDate('violation_date', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($query, $date) => $query->whereDate('violation_date', '<=', $date))
            ->when($filters['class_id'] ?? null, fn ($query, $classId) => $query->whereHas('studentProfile', fn ($studentQuery) => $studentQuery->where('class_id', $classId)))
            ->when($filters['grade'] ?? null, fn ($query, $grade) => $query->whereHas('studentProfile.class', fn ($classQuery) => $classQuery->where('grade', $grade)))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('violation_category', $category))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status));

        if ($user->isBk()) {
            $query->whereHas('studentProfile.class', fn (Builder $classQuery) => $classQuery->where('grade', $user->teacherProfile?->grade));
            $classes = SchoolClass::where('grade', $user->teacherProfile?->grade)->orderBy('name')->get();
            $filters['grade'] = $user->teacherProfile?->grade;
        } else {
            $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();
        }

        $query->latest('violation_date');

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
        $labels = StudentViolation::statusLabels();

        return collect(['pending', 'approved', 'rejected'])
            ->map(fn (string $status): array => [$labels[$status], $collection->where('status', $status)->count()])
            ->all();
    }
}
