<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppNotification;
use App\Models\Kelas;
use App\Models\PesanWhatsapp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiwayatPesanController extends Controller
{
    public function index(Request $request): View
    {
        $query = PesanWhatsapp::with('kelas')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('dibuat_pada', $request->tanggal);
        }

        $pesan = $query->paginate(20)->withQueryString();
        $kelas = Kelas::orderBy('nama')->get();

        return view('admin.pengaturan.riwayat-pesan.index', compact('pesan', 'kelas'));
    }

    public function show(PesanWhatsapp $pesanWhatsapp): View
    {
        $pesanWhatsapp->load('kelas');

        return view('admin.pengaturan.riwayat-pesan.show', compact('pesanWhatsapp'));
    }

    public function resend(PesanWhatsapp $pesanWhatsapp): RedirectResponse
    {
        if ($pesanWhatsapp->status !== 'failed') {
            return back()->with('error', 'Hanya pesan gagal yang bisa dikirim ulang.');
        }

        $pesanWhatsapp->update([
            'status' => 'pending',
            'percobaan' => 0,
            'respons' => null,
            'dikirim_pada' => null,
        ]);

        SendWhatsAppNotification::dispatch($pesanWhatsapp);

        return redirect()->route('admin.pengaturan.whatsapp.riwayat.show', $pesanWhatsapp)
            ->with('success', 'Pesan sedang diproses ulang.');
    }
}
