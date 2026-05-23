<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\TeacherImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class TeacherImportController extends Controller
{
    public function create(): View
    {
        return view('admin.teacher.import');
    }

    public function preview(Request $request): View
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $file = $request->file('file');
        $rows = Excel::toArray(new TeacherImport, $file);
        $allRows = $rows[0] ?? [];
        $previewRows = $allRows;

        return view('admin.teacher.import-preview', [
            'previewRows' => $previewRows,
            'totalRows' => count($allRows),
            'filePath' => $file->store('imports'),
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
