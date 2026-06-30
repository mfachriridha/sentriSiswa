<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiwayatPesanController extends Controller
{
    public function index(Request $request): View
    {
        $query = WhatsappMessage::with('schoolClass')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $pesan = $query->paginate(20)->withQueryString();
        $kelas = SchoolClass::orderBy('name')->get();

        return view('admin.pengaturan.riwayat-pesan.index', compact('pesan', 'kelas'));
    }

    public function show(WhatsappMessage $pesanWhatsapp): View
    {
        $pesanWhatsapp->load('schoolClass');

        return view('admin.pengaturan.riwayat-pesan.show', compact('pesanWhatsapp'));
    }
}
