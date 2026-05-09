@extends('layouts.app')

@section('title', 'Monitoring BK')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Monitoring Semua Kelas</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <div class="flex gap-2">
        <select class="input-field !w-auto !py-1.5 !text-xs"><option>Semua Kelas</option><option>XII IPA 1</option><option>XII IPA 2</option><option>XI IPA 1</option></select>
        <select class="input-field !w-auto !py-1.5 !text-xs"><option>Hari Ini</option><option>Kemarin</option><option>Minggu Ini</option></select>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Total Siswa</p><p class="stat-value mt-1">360</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Hadir</p><p class="stat-value text-emerald-600 mt-1">342</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Izin/Sakit</p><p class="stat-value text-amber-600 mt-1">12</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Alpha</p><p class="stat-value text-red-500 mt-1">6</p></div>
</div>

<!-- Tabel per Kelas -->
<div class="card anim-up" style="animation-delay:.1s">
    <h3 class="font-bold text-slate-900 mb-4">Detail per Kelas</h3>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">Kelas</th><th class="table-header">Wali Kelas</th><th class="table-header">Total</th><th class="table-header">Hadir</th><th class="table-header">Izin</th><th class="table-header">Sakit</th><th class="table-header">Alpha</th><th class="table-header">%</th></tr></thead>
            <tbody>
                <tr><td class="table-cell font-semibold">XII IPA 1</td><td class="table-cell text-sm">Budi Santoso</td><td class="table-cell">30</td><td class="table-cell"><span class="badge badge-green">28</span></td><td class="table-cell">1</td><td class="table-cell">1</td><td class="table-cell"><span class="badge badge-red">0</span></td><td class="table-cell font-bold">100%</td></tr>
                <tr><td class="table-cell font-semibold">XII IPA 2</td><td class="table-cell text-sm">Siti Nurhaliza</td><td class="table-cell">32</td><td class="table-cell"><span class="badge badge-green">27</span></td><td class="table-cell">2</td><td class="table-cell">1</td><td class="table-cell"><span class="badge badge-red">2</span></td><td class="table-cell font-bold">94%</td></tr>
                <tr><td class="table-cell font-semibold">XI IPA 1</td><td class="table-cell text-sm">Ahmad Fauzi</td><td class="table-cell">30</td><td class="table-cell"><span class="badge badge-green">30</span></td><td class="table-cell">0</td><td class="table-cell">0</td><td class="table-cell"><span class="badge badge-red">0</span></td><td class="table-cell font-bold">100%</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection