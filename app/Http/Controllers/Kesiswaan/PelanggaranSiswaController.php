<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Guru\StudentViolationController;
use App\Http\Requests\Guru\RejectStudentViolationRequest;
use App\Models\JenisPelanggaran;
use App\Models\PelanggaranSiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PelanggaranSiswaController extends StudentViolationController
{
    public function persetujuan(): View
    {
        $pending = PelanggaranSiswa::with(['profilSiswa.pengguna', 'profilSiswa.kelas', 'dicatatOleh'])
            ->where('status', 'pending')
            ->latest('tanggal_pelanggaran')
            ->get();

        $categoryLabels = JenisPelanggaran::categoryLabels();

        return view('kesiswaan.pelanggaran-siswa.persetujuan', compact('pending', 'categoryLabels'));
    }

    public function approve(PelanggaranSiswa $studentViolation): RedirectResponse
    {
        abort_unless($studentViolation->status === 'pending', 403);

        $studentViolation->update([
            'status' => 'approved',
            'disetujui_oleh_id' => Auth::id(),
            'disetujui_pada' => now(),
            'alasan_penolakan' => null,
        ]);

        return redirect()->route('kesiswaan.pelanggaran-siswa.persetujuan')
            ->with('success', 'Pelanggaran berhasil diterima.');
    }

    public function reject(RejectStudentViolationRequest $request, PelanggaranSiswa $studentViolation): RedirectResponse
    {
        abort_unless($studentViolation->status === 'pending', 403);

        $studentViolation->update([
            'status' => 'rejected',
            'disetujui_oleh_id' => Auth::id(),
            'disetujui_pada' => now(),
            'alasan_penolakan' => $request->validated()['alasan_penolakan'],
        ]);

        return redirect()->route('kesiswaan.pelanggaran-siswa.persetujuan')
            ->with('success', 'Pelanggaran berhasil ditolak.');
    }
}
