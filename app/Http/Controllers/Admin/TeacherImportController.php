<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TeacherTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\TeacherImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TeacherImportController extends Controller
{
    protected int $perPage = 25;

    public function template(): BinaryFileResponse
    {
        return Excel::download(new TeacherTemplateExport, 'template-impor-guru.xlsx');
    }

    public function create(): View
    {
        return view('admin.teacher.import');
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $file = $request->file('file');

        session(['import_file_path' => $file->store('imports')]);

        return redirect()->route('admin.teachers.import.preview');
    }

    public function preview(Request $request): View
    {
        $path = session('import_file_path');

        if (! $path) {
            return redirect()->route('admin.teachers.import');
        }

        $fullPath = storage_path('app/private/'.$path);
        $rows = Excel::toArray(new TeacherImport, $fullPath);
        $allRows = $rows[0] ?? [];

        $isOldFormat = ! empty($allRows) && isset($allRows[0]['walas']);

        $normalized = array_map(function ($row) use ($isOldFormat) {
            if ($isOldFormat) {
                $name = trim((string) ($row['walas'] ?? ''));
                $nipRaw = isset($row['nip']) && $row['nip'] !== '-' ? trim((string) $row['nip']) : null;
                $nip = $nipRaw ? str_replace(' ', '', $nipRaw) : null;
                $className = trim((string) ($row['kelas'] ?? ''));
                $type = 'Wali Kelas';
            } else {
                $name = trim((string) ($row['nama'] ?? ''));
                $nipRaw = isset($row['nip']) && $row['nip'] !== '-' ? trim((string) $row['nip']) : null;
                $nip = $nipRaw ? str_replace(' ', '', $nipRaw) : null;
                $className = trim((string) ($row['kelas'] ?? ''));
                $typeRaw = trim((string) ($row['tipe'] ?? ''));
                $type = match (true) {
                    in_array(strtolower($typeRaw), ['bk', 'guru bk']) => 'BK',
                    in_array(strtolower($typeRaw), ['kesiswaan', 'student_affairs']) => 'Kesiswaan',
                    default => 'Wali Kelas',
                };
            }

            return [
                'nama' => $name,
                'nip' => $nip ?? ($nipRaw ?? ''),
                'tipe' => $type,
                'kelas' => $className,
            ];
        }, $allRows);

        $page = (int) $request->get('page', 1);
        $total = count($normalized);
        $offset = ($page - 1) * $this->perPage;
        $paginated = new LengthAwarePaginator(
            array_slice($normalized, $offset, $this->perPage),
            $total,
            $this->perPage,
            $page,
            ['path' => route('admin.teachers.import.preview')],
        );

        return view('admin.teacher.import-preview', [
            'previewRows' => $paginated,
            'totalRows' => $total,
            'filePath' => $path,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file_path' => ['required', 'string'],
        ]);

        set_time_limit(0);

        $import = new TeacherImport;
        $start = microtime(true);
        $path = storage_path('app/private/'.$request->file_path);
        Excel::import($import, $path);
        $duration = round(microtime(true) - $start, 1);

        session()->forget('import_file_path');

        return redirect()
            ->route('admin.teachers.index')
            ->with('import_result', [
                'teachers_created' => $import->teachersCreated,
                'teachers_existing' => $import->teachersExisting,
                'classes_created' => $import->classesCreated,
                'errors' => $import->errors,
                'duration' => $duration,
            ]);
    }
}
