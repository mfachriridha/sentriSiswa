@extends('layouts.app')

@section('title', 'Rekap Presensi BK')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Rekap Presensi</h1>
        <p class="text-sm text-slate-500 mt-0.5">Rekap kehadiran seluruh siswa</p>
    </div>
    <div class="flex gap-2">
        <select class="input-field !w-auto !py-1.5 !text-xs"><option>Bulan Ini</option><option>Minggu Ini</option><option>Semester Ini</option></select>
        <button onclick="alert('UI Only: Export PDF!')" class="btn-outline !text-xs"><i class="bi bi-file-earmark-pdf"></i> Export</button>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-5 mb-6">
    <div class="card lg:col-span-2 anim-up">
        <h3 class="font-bold text-slate-900 mb-4">Grafik Kehadiran Bulanan</h3>
        <div class="h-48 bg-slate-50 rounded-xl flex items-end justify-around p-4 gap-2">
            <div class="w-8 bg-blue-400 rounded-t-lg" style="height:60%"><p class="text-[10px] text-center -mt-4 text-slate-500">M</p></div>
            <div class="w-8 bg-blue-400 rounded-t-lg" style="height:75%"><p class="text-[10px] text-center -mt-4 text-slate-500">S</p></div>
            <div class="w-8 bg-blue-400 rounded-t-lg" style="height:50%"><p class="text-[10px] text-center -mt-4 text-slate-500">S</p></div>
            <div class="w-8 bg-blue-400 rounded-t-lg" style="height:85%"><p class="text-[10px] text-center -mt-4 text-slate-500">R</p></div>
            <div class="w-8 bg-blue-400 rounded-t-lg" style="height:70%"><p class="text-[10px] text-center -mt-4 text-slate-500">K</p></div>
            <div class="w-8 bg-blue-400 rounded-t-lg" style="height:90%"><p class="text-[10px] text-center -mt-4 text-slate-500">J</p></div>
            <div class="w-8 bg-blue-400 rounded-t-lg" style="height:65%"><p class="text-[10px] text-center -mt-4 text-slate-500">S</p></div>
        </div>
        <p class="text-xs text-slate-400 text-center mt-2">Minggu 1 - 5 Mei 2026</p>
    </div>
    <div class="card anim-up" style="animation-delay:.1s">
        <h3 class="font-bold text-slate-900 mb-4">Ringkasan</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center p-3 bg-emerald-50 rounded-xl"><span class="text-sm text-emerald-700">Hadir</span><span class="font-bold text-emerald-700">92%</span></div>
            <div class="flex justify-between items-center p-3 bg-amber-50 rounded-xl"><span class="text-sm text-amber-700">Izin</span><span class="font-bold text-amber-700">5%</span></div>
            <div class="flex justify-between items-center p-3 bg-red-50 rounded-xl"><span class="text-sm text-red-700">Alpha</span><span class="font-bold text-red-700">3%</span></div>
        </div>
    </div>
</div>

<div class="card anim-up" style="animation-delay:.15s">
    <h3 class="font-bold text-slate-900 mb-4">Rekap per Siswa</h3>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">NIS</th><th class="table-header">Nama</th><th class="table-header">Kelas</th><th class="table-header">Hadir</th><th class="table-header">Izin</th><th class="table-header">Sakit</th><th class="table-header">Alpha</th><th class="table-header">%</th></tr></thead>
            <tbody>
                <tr><td class="table-cell">S001</td><td class="table-cell font-semibold">Ahmad Fauzi</td><td class="table-cell">XII IPA 1</td><td class="table-cell">18</td><td class="table-cell">1</td><td class="table-cell">0</td><td class="table-cell">1</td><td class="table-cell font-bold">90%</td></tr>
                <tr><td class="table-cell">S002</td><td class="table-cell font-semibold">Nurul Hidayah</td><td class="table-cell">XII IPA 1</td><td class="table-cell">19</td><td class="table-cell">0</td><td class="table-cell">1</td><td class="table-cell">0</td><td class="table-cell font-bold">95%</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection