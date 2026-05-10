@extends('layouts.app')

@section('title', 'Riwayat Kehadiran - SentriSiswa')

@section('page-title', 'Riwayat Kehadiran')

@section('content')
<div class="flex justify-end mb-6">
        <select class="input-field !w-auto !py-1.5 !text-xs">
            <option>Bulan Ini</option><option>Bulan Lalu</option><option>Semester Ini</option>
        </select>
</div>

<!-- Ringkasan Bulanan -->
<div class="grid grid-cols-5 gap-3 mb-6">
    <div class="bg-white rounded-lg p-3 border border-slate-200 text-center anim-up">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Sen</p>
        <p class="text-base font-extrabold text-emerald-600 mt-1">28</p>
    </div>
    <div class="bg-white rounded-lg p-3 border border-slate-200 text-center anim-up">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Sel</p>
        <p class="text-base font-extrabold text-emerald-600 mt-1">29</p>
    </div>
    <div class="bg-white rounded-lg p-3 border border-slate-200 text-center anim-up">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Rab</p>
        <p class="text-base font-extrabold text-emerald-600 mt-1">30</p>
    </div>
    <div class="bg-white rounded-lg p-3 border border-slate-200 text-center anim-up">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Kam</p>
        <p class="text-base font-extrabold text-amber-600 mt-1">26</p>
    </div>
    <div class="bg-white rounded-lg p-3 border border-slate-200 text-center anim-up">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Jum</p>
        <p class="text-base font-extrabold text-emerald-600 mt-1">30</p>
    </div>
</div>

<!-- Tabel Riwayat -->
<div class="card anim-up" style="animation-delay:.1s">
    <h3 class="font-bold text-slate-900 mb-4">Riwayat Presensi Harian</h3>
    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-header">Tanggal</th>
                    <th class="table-header">Masuk</th>
                    <th class="table-header">Pulang</th>
                    <th class="table-header">Status</th>
                    <th class="table-header">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="table-cell font-semibold">Sel, 5 Mei 2026</td>
                    <td class="table-cell">07:15</td>
                    <td class="table-cell">15:45</td>
                    <td class="table-cell"><span class="badge badge-green">Hadir</span></td>
                    <td class="table-cell text-xs text-slate-500">-</td>
                </tr>
                <tr>
                    <td class="table-cell font-semibold">Sen, 4 Mei 2026</td>
                    <td class="table-cell">07:40</td>
                    <td class="table-cell">15:50</td>
                    <td class="table-cell"><span class="badge badge-amber">Terlambat</span></td>
                    <td class="table-cell text-xs text-slate-500">Terlambat 10 menit</td>
                </tr>
                <tr>
                    <td class="table-cell font-semibold">Min, 3 Mei 2026</td>
                    <td class="table-cell">-</td>
                    <td class="table-cell">-</td>
                    <td class="table-cell"><span class="badge badge-red">Alpha</span></td>
                    <td class="table-cell text-xs text-slate-500">Libur</td>
                </tr>
                <tr>
                    <td class="table-cell font-semibold">Sab, 2 Mei 2026</td>
                    <td class="table-cell">07:20</td>
                    <td class="table-cell">15:30</td>
                    <td class="table-cell"><span class="badge badge-green">Hadir</span></td>
                    <td class="table-cell text-xs text-slate-500">-</td>
                </tr>
                <tr>
                    <td class="table-cell font-semibold">Jum, 1 Mei 2026</td>
                    <td class="table-cell">07:18</td>
                    <td class="table-cell">15:40</td>
                    <td class="table-cell"><span class="badge badge-green">Hadir</span></td>
                    <td class="table-cell text-xs text-slate-500">-</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection