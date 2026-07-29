<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppNotification;
use App\Models\Kelas;
use App\Models\PesanWhatsapp;
use App\Services\AttendanceReportMessageBuilder;
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

    public function resend(PesanWhatsapp $pesanWhatsapp, AttendanceReportMessageBuilder $messageBuilder): RedirectResponse
    {
        // Pesan yang dibuat di hari lain sudah pasti basi - tanggal, hitungan
        // sudah/belum absen, dan link absensinya semua mengacu ke hari itu, bukan
        // hari ini. Mengirim ulang apa adanya cuma menyesatkan penerimanya.
        if (! $pesanWhatsapp->dibuat_pada->isToday()) {
            return redirect()->route('admin.pengaturan.whatsapp.riwayat.show', $pesanWhatsapp)
                ->with('error', 'Pesan ini dibuat di hari lain, datanya sudah kedaluwarsa. Kirim ulang tidak tersedia untuk pesan lama.');
        }

        if ($pesanWhatsapp->tipe_pesan === 'attendance_report' && $pesanWhatsapp->kelas) {
            // Isi pesan disusun ulang dari kondisi absensi TERKINI, bukan sekadar
            // mengirim ulang teks lama yang mungkin sudah tidak sesuai kalau status
            // absensi ada yang diubah wali kelas setelah laporan pertama terkirim.
            $refreshed = $messageBuilder->build($pesanWhatsapp->kelas, $pesanWhatsapp->dibuat_pada->toDateString());

            if ($refreshed === null || blank($refreshed['telepon_penerima'])) {
                return redirect()->route('admin.pengaturan.whatsapp.riwayat.show', $pesanWhatsapp)
                    ->with('error', 'Pesan tidak bisa disusun ulang: kelas ini tidak lagi punya siswa atau wali kelasnya tidak punya nomor HP.');
            }

            $pesanWhatsapp->fill($refreshed);
        }

        $pesanWhatsapp->status = 'pending';
        $pesanWhatsapp->percobaan = 0;
        $pesanWhatsapp->respons = null;
        $pesanWhatsapp->dikirim_pada = null;
        $pesanWhatsapp->save();

        SendWhatsAppNotification::dispatch($pesanWhatsapp);

        return redirect()->route('admin.pengaturan.whatsapp.riwayat.show', $pesanWhatsapp)
            ->with('success', 'Pesan sedang diproses ulang.');
    }
}
