@extends('layouts.app')

@section('title', 'Dashboard Guru Mapel')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Presensi Kelas XII IPA 1</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ now()->translatedFormat('l, d F Y') }} &middot; Guru: Siti Nurhaliza, M.Pd</p>
    </div>
    <span class="badge badge-blue self-start">Matematika</span>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Total Siswa</p><p class="stat-value mt-1">30</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Hadir</p><p class="stat-value text-emerald-600 mt-1">28</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Izin/Sakit</p><p class="stat-value text-amber-600 mt-1">2</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Belum</p><p class="stat-value text-red-500 mt-1">0</p></div>
</div>

<!-- Table -->
<div class="card anim-up" style="animation-delay:.1s">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div>
            <h3 class="font-bold text-slate-900">Daftar Kehadiran</h3>
            <p class="text-xs text-slate-500">Data dari presensi pagi (tanpa selfie)</p>
        </div>
        <div class="flex gap-2">
            <select class="input-field !w-auto !py-1.5 !text-xs"><option>Semua</option><option>Hadir</option><option>Izin</option><option>Sakit</option></select>
            <button onclick="saveMapel()" class="btn-brand"><i class="bi bi-check-circle"></i> Simpan</button>
        </div>
    </div>

    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">No</th><th class="table-header">Nama</th><th class="table-header">NIS</th><th class="table-header">Status</th><th class="table-header">Keterangan</th></tr></thead>
            <tbody>
                <tr><td class="table-cell">1</td><td class="table-cell font-semibold">Ahmad Fauzi</td><td class="table-cell">S001</td><td class="table-cell"><select class="input-field !py-1 !px-2 !text-xs !w-24"><option>Hadir</option><option>Izin</option><option>Sakit</option><option>Alpha</option></select></td><td class="table-cell"><input class="input-field !py-1 !text-xs" placeholder="-"></td></tr>
                <tr><td class="table-cell">2</td><td class="table-cell font-semibold">Nurul Hidayah</td><td class="table-cell">S002</td><td class="table-cell"><select class="input-field !py-1 !px-2 !text-xs !w-24"><option>Izin</option><option>Hadir</option><option>Sakit</option><option>Alpha</option></select></td><td class="table-cell"><input class="input-field !py-1 !text-xs" value="Sakit flu"></td></tr>
                <tr><td class="table-cell">3</td><td class="table-cell font-semibold">Budi Darmawan</td><td class="table-cell">S003</td><td class="table-cell"><select class="input-field !py-1 !px-2 !text-xs !w-24"><option>Hadir</option><option>Izin</option><option>Sakit</option><option>Alpha</option></select></td><td class="table-cell"><input class="input-field !py-1 !text-xs" placeholder="-"></td></tr>
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-start gap-2 p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-700">
        <i class="bi bi-info-circle-fill shrink-0 mt-0.5"></i>
        <span>Data presensi dari pagi oleh wali kelas. Guru mapel hanya menandai kehadiran tanpa mengirim notifikasi WhatsApp.</span>
    </div>
</div>

<script>
function saveMapel() { alert('UI Only: Presensi berhasil disimpan!\n(Tidak ada notifikasi WhatsApp untuk guru mapel)'); }
</script>
@endsection