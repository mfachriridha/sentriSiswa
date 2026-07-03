<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengajuanPoin\ApprovePengajuanPoinRequest;
use App\Http\Requests\PengajuanPoin\RejectPengajuanPoinRequest;
use App\Models\PengajuanPoin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PersetujuanPoinController extends Controller
{
    public function persetujuan(): View
    {
        $pending = PengajuanPoin::with(['profilSiswa.pengguna', 'profilSiswa.kelas', 'diajukanOleh'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('kesiswaan.pengajuan-poin.persetujuan', compact('pending'));
    }

    public function approve(ApprovePengajuanPoinRequest $request, PengajuanPoin $pengajuanPoin): RedirectResponse
    {
        abort_unless($pengajuanPoin->status === 'pending', 403);

        $pengajuanPoin->update([
            'status' => 'approved',
            'jumlah_poin' => $request->validated()['jumlah_poin'],
            'disetujui_oleh_id' => Auth::id(),
            'disetujui_pada' => now(),
            'alasan_penolakan' => null,
        ]);

        return redirect()->route('kesiswaan.pengajuan-poin.persetujuan')
            ->with('success', 'Pengajuan penambahan poin berhasil diterima.');
    }

    public function reject(RejectPengajuanPoinRequest $request, PengajuanPoin $pengajuanPoin): RedirectResponse
    {
        abort_unless($pengajuanPoin->status === 'pending', 403);

        $pengajuanPoin->update([
            'status' => 'rejected',
            'disetujui_oleh_id' => Auth::id(),
            'disetujui_pada' => now(),
            'alasan_penolakan' => $request->validated()['alasan_penolakan'],
        ]);

        return redirect()->route('kesiswaan.pengajuan-poin.persetujuan')
            ->with('success', 'Pengajuan penambahan poin berhasil ditolak.');
    }

    public function pendingCount(): JsonResponse
    {
        return response()->json(['pending' => PengajuanPoin::where('status', 'pending')->count()]);
    }
}
