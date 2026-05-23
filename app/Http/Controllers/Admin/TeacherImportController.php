<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\TeacherImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class TeacherImportController extends Controller
{
    protected int $perPage = 25;

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

        $page = (int) $request->get('page', 1);
        $collection = collect($allRows);
        $paginated = new LengthAwarePaginator(
            $collection->forPage($page, $this->perPage)->values(),
            $collection->count(),
            $this->perPage,
            $page,
            ['path' => route('admin.teachers.import.preview')],
        );

        return view('admin.teacher.import-preview', [
            'previewRows' => $paginated,
            'totalRows' => $collection->count(),
            'filePath' => $path,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file_path' => ['required', 'string'],
        ]);

        $import = new TeacherImport;
        $path = storage_path('app/private/'.$request->file_path);
        Excel::import($import, $path);

        session()->forget('import_file_path');

        return redirect()
            ->route('admin.teachers.index')
            ->with('import_result', [
                'teachers_created' => $import->teachersCreated,
                'teachers_existing' => $import->teachersExisting,
                'classes_created' => $import->classesCreated,
                'errors' => $import->errors,
            ]);
    }
}
