@extends('layouts.app')

@section('title', 'Riwayat Presensi Wali Kelas')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Riwayat Presensi</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kelas XII IPA 1 &middot; Wali Kelas: Budi Santoso, S.Pd</p>
    </div>
    <div class="flex gap-2">
        <select class="input-field !w-auto !py-1.5 !text-xs"><option>Minggu Ini</option><option>Bulan Ini</option><option>Semester Ini</option></select>
    </div>
</div>

<!-- Ringkasan Mingguan -->
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
    <div class="card !p-4 text-center anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Sen</p><p class="stat-value text-emerald-600 mt-1">28</p><p class="text-[10px] text-slate-400">Hadir</p></div>
    <div class="card !p-4 text-center anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Sel</p><p class="stat-value text-emerald-600 mt-1">29</p><p class="text-[10px] text-slate-400">Hadir</p></div>
    <div class="card !p-4 text-center anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Rab</p><p class="stat-value text-emerald-600 mt-1">27</p><p class="text-[10px] text-slate-400">Hadir</p></div>
    <div class="card !p-4 text-center anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Kam</p><p class="stat-value text-amber-600 mt-1">26</p><p class="text-[10px] text-slate-400">Hadir</p></div>
    <div class="card !p-4 text-center anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Jum</p><p class="stat-value text-emerald-600 mt-1">30</p><p class="text-[10px] text-slate-400">Hadir</p></div>
</div>

<!-- Tabel Riwayat -->
<div class="card anim-up" style="animation-delay:.1s">
    <h3 class="font-bold text-slate-900 mb-4">Detail Presensi Harian</h3>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">Tanggal</th><th class="table-header">Hadir</th><th class="table-header">Terlambat</th><th class="table-header">Izin</th><th class="table-header">Sakit</th><th class="table-header">Alpha</th><th class="table-header">Status</th></tr></thead>
            <tbody>
                <tr><td class="table-cell font-semibold">5 Mei 2026</td><td class="table-cell">28</td><td class="table-cell">2</td><td class="table-cell">0</td><td class="table-cell">0</td><td class="table-cell">0</td><td class="table-cell"><span class="badge badge-green">Lengkap</span></td></tr>
                <tr><td class="table-cell font-semibold">4 Mei 2026</td><td class="table-cell">27</td><td class="table-cell">1</td><td class="table-cell">1</td><td class="table-cell">1</td><td class="table-cell">0</td><td class="table-cell"><span class="badge badge-green">Lengkap</span></td></tr>
                <tr><td class="table-cell font-semibold">3 Mei 2026</td><td class="table-cell">26</td><td class="table-cell">2</td><td class="table-cell">1</td><td class="table-cell">0</td><td class="table-cell">1</td><td class="table-cell"><span class="badge badge-amber">Ada Alpha</span></td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection