@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Presensi Kelas XII IPA 1</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ now()->translatedFormat('l, d F Y') }} &middot; Wali Kelas: Budi Santoso, S.Pd</p>
    </div>
    <span class="badge badge-blue self-start">30 Siswa</span>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Total</p><p class="stat-value mt-1">30</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Hadir</p><p class="stat-value text-emerald-600 mt-1">28</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Terlambat</p><p class="stat-value text-amber-600 mt-1">2</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Belum</p><p class="stat-value text-red-500 mt-1">0</p></div>
</div>

<!-- Student List -->
<div class="card anim-up" style="animation-delay:.1s">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div>
            <h3 class="font-bold text-slate-900">Daftar Presensi Siswa</h3>
            <p class="text-xs text-slate-500">Verifikasi foto selfie &amp; GPS</p>
        </div>
        <div class="flex gap-2">
            <select class="input-field !w-auto !py-1.5 !text-xs"><option>Semua</option><option>Hadir</option><option>Terlambat</option><option>Belum</option></select>
            <button onclick="saveAll()" class="btn-brand"><i class="bi bi-check-circle"></i> Simpan & Kirim WA</button>
        </div>
    </div>

    <div class="space-y-3">
        <!-- Hadir -->
        <div class="border border-slate-200 rounded-xl p-4 hover:border-emerald-300 hover:bg-emerald-50/30 transition-all">
            <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-9 h-9 bg-emerald-100 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-person-fill text-emerald-600"></i></div>
                    <div class="flex-1"><p class="font-semibold text-sm">Ahmad Fauzi</p><p class="text-xs text-slate-500">NIS: S001 &middot; 07:15</p></div>
                    <span class="badge badge-green">Hadir</span>
                </div>
                <div class="flex items-center gap-3 lg:w-auto">
                    <div class="w-14 h-14 bg-slate-100 rounded-lg flex items-center justify-center shrink-0"><i class="bi bi-person text-slate-300 text-xl"></i></div>
                    <div class="text-xs flex-1 lg:w-52"><p class="text-slate-600"><i class="bi bi-geo-alt text-emerald-500"></i> 106.588123, -6.119123</p><p class="text-emerald-600 mt-0.5"><i class="bi bi-check-circle"></i> Dalam area</p></div>
                    <div class="flex gap-2 shrink-0">
                        <button class="btn-outline !py-1.5 !px-2.5 !text-xs"><i class="bi bi-eye"></i></button>
                        <select class="input-field !py-1.5 !px-2 !text-xs !w-28"><option>Hadir</option><option>Terlambat</option><option>Izin</option><option>Sakit</option></select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="border border-slate-200 rounded-xl p-4 hover:border-amber-300 hover:bg-amber-50/30 transition-all">
            <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-person-fill text-amber-600"></i></div>
                    <div class="flex-1"><p class="font-semibold text-sm">Nurul Hidayah</p><p class="text-xs text-slate-500">NIS: S002 &middot; 07:45</p></div>
                    <span class="badge badge-amber">Terlambat</span>
                </div>
                <div class="flex items-center gap-3 lg:w-auto">
                    <div class="w-14 h-14 bg-slate-100 rounded-lg flex items-center justify-center shrink-0"><i class="bi bi-person text-slate-300 text-xl"></i></div>
                    <div class="text-xs flex-1 lg:w-52"><p class="text-slate-600"><i class="bi bi-geo-alt text-emerald-500"></i> 106.587957, -6.118907</p><p class="text-emerald-600 mt-0.5"><i class="bi bi-check-circle"></i> Dalam area</p></div>
                    <div class="flex gap-2 shrink-0">
                        <button class="btn-outline !py-1.5 !px-2.5 !text-xs"><i class="bi bi-eye"></i></button>
                        <select class="input-field !py-1.5 !px-2 !text-xs !w-28"><option>Terlambat</option><option>Hadir</option><option>Izin</option><option>Sakit</option></select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Belum -->
        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-slate-200 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-person-fill text-slate-400"></i></div>
                <div class="flex-1"><p class="font-semibold text-sm text-slate-600">Budi Darmawan</p><p class="text-xs text-slate-500">NIS: S003</p></div>
                <span class="badge badge-red">Belum</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sukses -->
<div id="successModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" onclick="if(event.target===this)closeSuccess()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center anim-fade" onclick="event.stopPropagation()">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-check-circle-fill text-emerald-500 text-4xl"></i>
        </div>
        <h3 class="font-bold text-lg mb-2">Data Presensi Tersimpan!</h3>
        <p class="text-sm text-slate-500 mb-4">Notifikasi WhatsApp telah dikirim ke <strong>28 orang tua/wali</strong> siswa.</p>
        <button onclick="closeSuccess()" class="btn-brand w-full !py-2.5">Selesai</button>
    </div>
</div>

<script>
function saveAll(){ document.getElementById('successModal').classList.remove('hidden'); }
function closeSuccess(){ document.getElementById('successModal').classList.add('hidden'); }
</script>
@endsection