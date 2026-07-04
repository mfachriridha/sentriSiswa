<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TemplateSiswaExport;
use App\Http\Controllers\Controller;
use App\Imports\ImporSiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImporSiswaController extends Controller
{
    protected int $perPage = 25;

    public function template(): BinaryFileResponse
    {
        return Excel::download(new TemplateSiswaExport, 'template-impor-siswa.xlsx');
    }

    public function create(): View
    {
        return view('admin.siswa.import');
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $file = $request->file('file');

        if (! $file instanceof UploadedFile) {
            return back()->withErrors(['file' => 'File impor tidak valid.']);
        }

        session(['import_siswa_file_path' => $file->store('imports')]);

        return redirect()->route('admin.siswa.impor.pratinjau');
    }

    public function preview(Request $request): RedirectResponse|View
    {
        $path = session('import_siswa_file_path');

        if (! $path) {
            return redirect()->route('admin.siswa.impor');
        }

        $fullPath = storage_path('app/private/'.$path);
        $rows = Excel::toArray(new ImporSiswa, $fullPath);
        $allRows = $rows[0] ?? [];

        $page = (int) $request->get('page', 1);
        $total = count($allRows);
        $offset = ($page - 1) * $this->perPage;
        $paginated = new LengthAwarePaginator(
            array_slice($allRows, $offset, $this->perPage),
            $total,
            $this->perPage,
            $page,
            ['path' => route('admin.siswa.impor.pratinjau')],
        );

        return view('admin.siswa.import-preview', [
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

        $import = new ImporSiswa;
        $start = microtime(true);
        $path = storage_path('app/private/'.$request->file_path);
        Excel::import($import, $path);
        $duration = round(microtime(true) - $start, 1);

        session()->forget('import_siswa_file_path');

        return redirect()
            ->route('admin.siswa.index')
            ->with('import_result', [
                'students_created' => $import->studentsCreated,
                'students_existing' => $import->studentsExisting,
                'errors' => $import->errors,
                'error_details' => $import->errorDetails,
                'duration' => $duration,
            ]);
    }
}
