<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\ViolationType\StoreViolationTypeRequest;
use App\Http\Requests\ViolationType\UpdateViolationTypeRequest;
use App\Models\ViolationType;
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
        $sort = $request->get('sort', 'category');
        $direction = $request->get('direction', 'asc');
        $allowed = ['name', 'category', 'point_deduction', 'is_active', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'category';
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'asc';

        $violationTypes = ViolationType::query();

        if ($search) {
            $violationTypes->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (array_key_exists($filterCategory, ViolationType::categoryLabels())) {
            $violationTypes->where('category', $filterCategory);
        }

        if ($filterStatus === 'active') {
            $violationTypes->where('is_active', true);
        } elseif ($filterStatus === 'inactive') {
            $violationTypes->where('is_active', false);
        }

        if ($sort === 'category') {
            $violationTypes = $violationTypes
                ->orderByRaw("CASE category WHEN 'light' THEN 1 WHEN 'medium' THEN 2 WHEN 'heavy' THEN 3 WHEN 'severe' THEN 4 ELSE 5 END {$direction}")
                ->orderBy('point_deduction', $direction)
                ->orderBy('name');
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

        $categoryLabels = ViolationType::categoryLabels();

        return view('kesiswaan.jenis-pelanggaran.index', compact('violationTypes', 'categoryLabels', 'sort', 'direction', 'search', 'filterCategory', 'filterStatus'));
    }

    public function create(): View
    {
        $categoryLabels = ViolationType::categoryLabels();
        $categoryRanges = ViolationType::categoryRanges();

        return view('kesiswaan.jenis-pelanggaran.create', compact('categoryLabels', 'categoryRanges'));
    }

    public function store(StoreViolationTypeRequest $request): RedirectResponse
    {
        ViolationType::create($request->validated());

        return redirect()->route('kesiswaan.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil ditambahkan.');
    }

    public function show(ViolationType $violationType): View
    {
        $categoryLabels = ViolationType::categoryLabels();

        return view('kesiswaan.jenis-pelanggaran.show', compact('violationType', 'categoryLabels'));
    }

    public function edit(ViolationType $violationType): View
    {
        $categoryLabels = ViolationType::categoryLabels();
        $categoryRanges = ViolationType::categoryRanges();

        return view('kesiswaan.jenis-pelanggaran.edit', compact('violationType', 'categoryLabels', 'categoryRanges'));
    }

    public function update(UpdateViolationTypeRequest $request, ViolationType $violationType): RedirectResponse
    {
        $violationType->update($request->validated());

        return redirect()->route('kesiswaan.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil diperbarui.');
    }

    public function destroy(ViolationType $violationType): RedirectResponse
    {
        if ($violationType->studentViolations()->exists()) {
            return redirect()->route('kesiswaan.jenis-pelanggaran.index')->with('error', 'Jenis pelanggaran sudah dipakai pada data pelanggaran siswa. Nonaktifkan jika tidak ingin digunakan lagi.');
        }

        $violationType->delete();

        return redirect()->route('kesiswaan.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }
}
