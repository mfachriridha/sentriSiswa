<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\PesanWhatsapp;
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

        if ($request->filled('class_id')) {
            $query->where('kelas_id', $request->class_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
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
}
