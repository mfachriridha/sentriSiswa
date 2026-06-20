<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TemplateSiswaExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImporSiswaRequest;
use App\Imports\ImporSiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImporSiswaController extends Controller
{
    protected int $perPage = 25;

    public function template(): BinaryFileResponse
    {
        return Excel::download(new TemplateSiswaExport, 'template-impor-siswa.xlsx');
    }

    public function create(): Response
    {
        return Inertia::render('admin/siswa/Impor');
    }

    public function unggah(ImporSiswaRequest $request): RedirectResponse
    {
        $file = $request->file('file');

        if (! $file instanceof UploadedFile) {
            return back()->withErrors(['file' => 'File impor tidak valid.']);
        }

        session(['impor_siswa_path' => $file->store('imports')]);

        return redirect()->route('admin.siswa.impor.pratinjau');
    }

    public function pratinjau(): RedirectResponse|Response
    {
        $path = session('impor_siswa_path');

        if (! $path) {
            return redirect()->route('admin.siswa.impor');
        }

        $fullPath = storage_path('app/'.$path);
        $rows = Excel::toArray(new ImporSiswa, $fullPath);
        $allRows = $rows[0] ?? [];

        $page = (int) request()->get('page', 1);
        $total = count($allRows);
        $offset = ($page - 1) * $this->perPage;
        $paginated = new LengthAwarePaginator(
            array_slice($allRows, $offset, $this->perPage),
            $total,
            $this->perPage,
            $page,
            ['path' => route('admin.siswa.impor.pratinjau')]
        );

        return Inertia::render('admin/siswa/Impor', [
            'pratinjau' => $paginated,
            'totalBaris' => $total,
            'pathFile' => $path,
        ]);
    }

    public function simpan(): RedirectResponse
    {
        $path = session('impor_siswa_path');

        if (! $path) {
            return redirect()->route('admin.siswa.impor')->withErrors(['file' => 'Sesi impor kedaluwarsa.']);
        }

        set_time_limit(0);

        $import = new ImporSiswa;
        $mulai = microtime(true);
        $fullPath = storage_path('app/'.$path);
        Excel::import($import, $fullPath);
        $durasi = round(microtime(true) - $mulai, 1);

        session()->forget('impor_siswa_path');

        return redirect()
            ->route('admin.siswa.index')
            ->with('toast', [
                'type' => 'success',
                'message' => "Import selesai dalam {$durasi} detik. {$import->siswaDibuat} siswa dibuat, {$import->siswaDilewati} dilewati.",
            ]);
    }
}
