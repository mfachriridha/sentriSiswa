<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TemplateGuruExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImporGuruRequest;
use App\Imports\ImporGuru;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImporGuruController extends Controller
{
    protected int $perPage = 25;

    public function template(): BinaryFileResponse
    {
        return Excel::download(new TemplateGuruExport, 'template-impor-guru.xlsx');
    }

    public function create(): Response
    {
        return Inertia::render('admin/guru/Impor');
    }

    public function unggah(ImporGuruRequest $request): RedirectResponse
    {
        $file = $request->file('file');

        if (! $file instanceof UploadedFile) {
            return back()->withErrors(['file' => 'File impor tidak valid.']);
        }

        session(['impor_guru_path' => $file->store('imports')]);

        return redirect()->route('admin.guru.impor.pratinjau');
    }

    public function pratinjau(): RedirectResponse|Response
    {
        $path = session('impor_guru_path');

        if (! $path) {
            return redirect()->route('admin.guru.impor');
        }

        $fullPath = storage_path('app/'.$path);
        $rows = Excel::toArray(new ImporGuru, $fullPath);
        $allRows = $rows[0] ?? [];

        $page = (int) request()->get('page', 1);
        $total = count($allRows);
        $offset = ($page - 1) * $this->perPage;
        $paginated = new LengthAwarePaginator(
            array_slice($allRows, $offset, $this->perPage),
            $total,
            $this->perPage,
            $page,
            ['path' => route('admin.guru.impor.pratinjau')]
        );

        return Inertia::render('admin/guru/Impor', [
            'pratinjau' => $paginated,
            'totalBaris' => $total,
            'pathFile' => $path,
        ]);
    }

    public function simpan(): RedirectResponse
    {
        $path = session('impor_guru_path');

        if (! $path) {
            return redirect()->route('admin.guru.impor')->withErrors(['file' => 'Sesi impor kedaluwarsa.']);
        }

        set_time_limit(0);

        $import = new ImporGuru;
        $mulai = microtime(true);
        $fullPath = storage_path('app/'.$path);
        Excel::import($import, $fullPath);
        $durasi = round(microtime(true) - $mulai, 1);

        session()->forget('impor_guru_path');

        return redirect()
            ->route('admin.guru.index')
            ->with('toast', [
                'type' => 'success',
                'message' => "Import selesai dalam {$durasi} detik. {$import->guruDibuat} guru dibuat, {$import->guruDilewati} dilewati.",
            ]);
    }
}
