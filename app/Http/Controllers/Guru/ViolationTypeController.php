<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\ViolationType\StoreViolationTypeRequest;
use App\Http\Requests\ViolationType\UpdateViolationTypeRequest;
use App\Models\JenisPelanggaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ViolationTypeController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterCategory = $request->get('category', '');
        $filterStatus = $request->get('status', '');
        $sort = $request->get('sort', 'kategori');
        $direction = $request->get('direction', 'asc');
        $allowed = ['nama', 'kategori', 'pengurangan_poin', 'aktif', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'kategori';
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'asc';

        $violationTypes = JenisPelanggaran::query();

        if ($search) {
            $violationTypes->where(function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if (array_key_exists($filterCategory, JenisPelanggaran::categoryLabels())) {
            $violationTypes->where('kategori', $filterCategory);
        }

        if ($filterStatus === 'active') {
            $violationTypes->where('aktif', true);
        } elseif ($filterStatus === 'inactive') {
            $violationTypes->where('aktif', false);
        }

        if ($sort === 'kategori') {
            $violationTypes = $violationTypes
                ->orderByRaw("CASE kategori WHEN 'light' THEN 1 WHEN 'medium' THEN 2 WHEN 'heavy' THEN 3 WHEN 'severe' THEN 4 ELSE 5 END {$direction}")
                ->orderBy('pengurangan_poin', $direction)
                ->orderBy('nama');
        } else {
            $violationTypes = $violationTypes->orderBy($sort, $direction);
        }

        $violationTypes = $violationTypes->paginate(25)->appends([
            'search' => $search,
            'category' => $filterCategory,
            'status' => $filterStatus,
            'sort' => $sort,
            'direction' => $direction,
        ]);

        $categoryLabels = JenisPelanggaran::categoryLabels();

        return view('kesiswaan.jenis-pelanggaran.index', compact('violationTypes', 'categoryLabels', 'sort', 'direction', 'search', 'filterCategory', 'filterStatus'));
    }

    public function create(): View
    {
        $categoryLabels = JenisPelanggaran::categoryLabels();
        $categoryRanges = JenisPelanggaran::categoryRanges();

        return view('kesiswaan.jenis-pelanggaran.create', compact('categoryLabels', 'categoryRanges'));
    }

    public function store(StoreViolationTypeRequest $request): RedirectResponse
    {
        JenisPelanggaran::create($request->validated());

        return redirect()->route('kesiswaan.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil ditambahkan.');
    }

    public function show(JenisPelanggaran $violationType): View
    {
        $categoryLabels = JenisPelanggaran::categoryLabels();

        return view('kesiswaan.jenis-pelanggaran.show', compact('violationType', 'categoryLabels'));
    }

    public function edit(JenisPelanggaran $violationType): View
    {
        $categoryLabels = JenisPelanggaran::categoryLabels();
        $categoryRanges = JenisPelanggaran::categoryRanges();

        return view('kesiswaan.jenis-pelanggaran.edit', compact('violationType', 'categoryLabels', 'categoryRanges'));
    }

    public function update(UpdateViolationTypeRequest $request, JenisPelanggaran $violationType): RedirectResponse
    {
        $violationType->update($request->validated());

        return redirect()->route('kesiswaan.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil diperbarui.');
    }

    public function destroy(JenisPelanggaran $violationType): RedirectResponse
    {
        if ($violationType->pelanggaranSiswa()->exists()) {
            return redirect()->route('kesiswaan.jenis-pelanggaran.index')->with('error', 'Jenis pelanggaran sudah dipakai pada data pelanggaran siswa. Nonaktifkan jika tidak ingin digunakan lagi.');
        }

        $violationType->delete();

        return redirect()->route('kesiswaan.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }
}
