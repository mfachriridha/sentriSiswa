<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Guru\StudentViolationController;
use App\Http\Requests\Guru\RejectStudentViolationRequest;
use App\Models\StudentViolation;
use App\Models\ViolationType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PelanggaranSiswaController extends StudentViolationController
{
    public function persetujuan(): View
    {
        $pending = StudentViolation::with(['studentProfile.user', 'studentProfile.class', 'recordedBy'])
            ->where('status', 'pending')
            ->latest('violation_date')
            ->get();

        $categoryLabels = ViolationType::categoryLabels();

        return view('kesiswaan.pelanggaran-siswa.persetujuan', compact('pending', 'categoryLabels'));
    }

    public function approve(StudentViolation $studentViolation): RedirectResponse
    {
        abort_unless($studentViolation->status === 'pending', 403);

        $studentViolation->update([
            'status' => 'approved',
            'approved_by_user_id' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()->route('kesiswaan.pelanggaran-siswa.persetujuan')
            ->with('success', 'Pelanggaran berhasil diterima.');
    }

    public function reject(RejectStudentViolationRequest $request, StudentViolation $studentViolation): RedirectResponse
    {
        abort_unless($studentViolation->status === 'pending', 403);

        $studentViolation->update([
            'status' => 'rejected',
            'approved_by_user_id' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->validated()['rejection_reason'],
        ]);

        return redirect()->route('kesiswaan.pelanggaran-siswa.persetujuan')
            ->with('success', 'Pelanggaran berhasil ditolak.');
    }
}
