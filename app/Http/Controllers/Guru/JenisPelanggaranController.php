<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\JenisPelanggaran\StoreJenisPelanggaranRequest;
use App\Http\Requests\JenisPelanggaran\UpdateJenisPelanggaranRequest;
use App\Models\JenisPelanggaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JenisPelanggaranController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $filterCategory = $request->get('category', '');
        $filterStatus = $request->get('status', '');
        $sort = $request->get('sort', 'kategori');
        $direction = $request->get('direction', 'asc');
        $allowed = ['nama', 'kategori', 'pengurangan_poin', 'aktif', 'dibuat_pada'];
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
                ->orderByRaw("CASE kategori WHEN 'ringan' THEN 1 WHEN 'sedang' THEN 2 WHEN 'berat' THEN 3 WHEN 'sangat_berat' THEN 4 ELSE 5 END {$direction}")
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

    public function store(StoreJenisPelanggaranRequest $request): RedirectResponse
    {
        JenisPelanggaran::create($request->validated());

        return redirect()->route('kesiswaan.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil ditambahkan.');
    }

    public function show(JenisPelanggaran $jenisPelanggaran): View
    {
        $categoryLabels = JenisPelanggaran::categoryLabels();

        return view('kesiswaan.jenis-pelanggaran.show', compact('jenisPelanggaran', 'categoryLabels'));
    }

    public function edit(JenisPelanggaran $jenisPelanggaran): View
    {
        $categoryLabels = JenisPelanggaran::categoryLabels();
        $categoryRanges = JenisPelanggaran::categoryRanges();

        return view('kesiswaan.jenis-pelanggaran.edit', compact('jenisPelanggaran', 'categoryLabels', 'categoryRanges'));
    }

    public function update(UpdateJenisPelanggaranRequest $request, JenisPelanggaran $jenisPelanggaran): RedirectResponse
    {
        $jenisPelanggaran->update($request->validated());

        return redirect()->route('kesiswaan.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil diperbarui.');
    }

    public function destroy(JenisPelanggaran $jenisPelanggaran): RedirectResponse
    {
        if ($jenisPelanggaran->pelanggaranSiswa()->exists()) {
            return redirect()->route('kesiswaan.jenis-pelanggaran.index')->with('error', 'Jenis pelanggaran sudah dipakai pada data pelanggaran siswa. Nonaktifkan jika tidak ingin digunakan lagi.');
        }

        $jenisPelanggaran->delete();

        return redirect()->route('kesiswaan.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }
}
