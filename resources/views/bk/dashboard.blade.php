@extends('layouts.app')

@section('title', 'Dashboard BK')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Monitoring Presensi</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ now()->translatedFormat('l, d F Y') }} &middot; Guru BK</p>
    </div>
    <span class="badge badge-blue self-start">Semua Kelas</span>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Total Siswa</p><p class="stat-value mt-1">360</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Hadir</p><p class="stat-value text-emerald-600 mt-1">342</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Terlambat</p><p class="stat-value text-amber-600 mt-1">12</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Belum Presensi</p><p class="stat-value text-red-500 mt-1">6</p></div>
</div>

<div class="grid lg:grid-cols-3 gap-5 mb-6">
    <!-- Rekap Tabel -->
    <div class="card lg:col-span-2 anim-up" style="animation-delay:.1s">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <h3 class="font-bold text-slate-900">Rekap Presensi per Kelas</h3>
            <div class="flex gap-2">
                <select class="input-field !w-auto !py-1.5 !text-xs"><option>Semua Tingkat</option><option>Kelas X</option><option>Kelas XI</option><option>Kelas XII</option></select>
            </div>
        </div>
        <div class="table-container">
            <table class="w-full">
                <thead><tr><th class="table-header">Kelas</th><th class="table-header">Wali Kelas</th><th class="table-header">Hadir</th><th class="table-header">Terlambat</th><th class="table-header">Belum</th><th class="table-header"></th></tr></thead>
                <tbody>
                    <tr><td class="table-cell font-semibold">XII IPA 1</td><td class="table-cell text-sm">Budi Santoso</td><td class="table-cell"><span class="badge badge-green">28</span></td><td class="table-cell"><span class="badge badge-amber">2</span></td><td class="table-cell"><span class="badge badge-red">0</span></td><td class="table-cell"><button class="text-blue-600 hover:underline text-xs font-semibold">Detail</button></td></tr>
                    <tr><td class="table-cell font-semibold">XII IPA 2</td><td class="table-cell text-sm">Siti Nurhaliza</td><td class="table-cell"><span class="badge badge-green">27</span></td><td class="table-cell"><span class="badge badge-amber">3</span></td><td class="table-cell"><span class="badge badge-red">2</span></td><td class="table-cell"><button class="text-blue-600 hover:underline text-xs font-semibold">Detail</button></td></tr>
                    <tr><td class="table-cell font-semibold">XI IPA 1</td><td class="table-cell text-sm">Ahmad Fauzi</td><td class="table-cell"><span class="badge badge-green">30</span></td><td class="table-cell"><span class="badge badge-amber">0</span></td><td class="table-cell"><span class="badge badge-red">0</span></td><td class="table-cell"><button class="text-blue-600 hover:underline text-xs font-semibold">Detail</button></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Poin Pelanggaran -->
    <div class="card anim-up" style="animation-delay:.15s">
        <h3 class="font-bold text-slate-900 mb-4">Poin Pelanggaran</h3>
        <div class="space-y-3">
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl">
                <p class="text-xs font-bold text-red-600 uppercase">Poin Rendah &lt;50</p>
                <p class="text-2xl font-extrabold text-red-600 mt-1">5 <span class="text-xs font-normal">siswa</span></p>
            </div>
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                <p class="text-xs font-bold text-amber-600 uppercase">Poin Sedang 50-80</p>
                <p class="text-2xl font-extrabold text-amber-600 mt-1">12 <span class="text-xs font-normal">siswa</span></p>
            </div>
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                <p class="text-xs font-bold text-emerald-600 uppercase">Poin Baik &gt;80</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1">343 <span class="text-xs font-normal">siswa</span></p>
            </div>
            <button onclick="alert('UI Only: Form tambah pelanggaran!')" class="btn-danger w-full !py-2.5 text-sm">
                <i class="bi bi-plus-circle"></i> Tambah Pelanggaran
            </button>
        </div>
    </div>
</div>

<!-- Pelanggaran Terbaru -->
<div class="card anim-up" style="animation-delay:.2s">
    <h3 class="font-bold text-slate-900 mb-4">Pelanggaran Terbaru</h3>
    <div class="space-y-2">
        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center"><i class="bi bi-exclamation-triangle-fill text-red-500 text-sm"></i></div>
                <div><p class="text-sm font-semibold">Ahmad Fauzi - Bolos</p><p class="text-xs text-slate-500">5 Mei 2026 &middot; BK: Siti Nurhaliza</p></div>
            </div>
            <span class="badge badge-red">-10</span>
        </div>
        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center"><i class="bi bi-exclamation-circle-fill text-amber-500 text-sm"></i></div>
                <div><p class="text-sm font-semibold">Nurul Hidayah - Terlambat</p><p class="text-xs text-slate-500">4 Mei 2026 &middot; BK: Siti Nurhaliza</p></div>
            </div>
            <span class="badge badge-amber">-5</span>
        </div>
    </div>
</div>
@endsection