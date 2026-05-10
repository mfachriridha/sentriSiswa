@extends('layouts.app')

@section('title', 'Poin Saya - SentriSiswa')

@section('page-title', 'Poin Saya')

@section('content')
<!-- Poin Card -->
<div class="card !bg-gradient-to-br !from-blue-50 !to-indigo-50 !border-blue-100 mb-6 anim-up">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-blue-500 uppercase tracking-wide">Sisa Poin</p>
            <p class="stat-value text-blue-900 mt-1">95<span class="text-base font-medium text-blue-400 ml-1">/ 100</span></p>
            <div class="w-full max-w-xs bg-blue-200 rounded-full h-2.5 mt-2">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width:95%"></div>
            </div>
            <p class="text-xs text-blue-600 mt-2">Poin berkurang: <span class="font-bold">5 poin</span></p>
        </div>
        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center">
            <i class="bi bi-star-fill text-3xl text-blue-500"></i>
        </div>
    </div>
</div>

<!-- Riwayat Pelanggaran -->
<div class="card anim-up" style="animation-delay:.1s">
    <h3 class="font-bold text-slate-900 mb-4">Riwayat Pelanggaran</h3>
    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-header">Tanggal</th>
                    <th class="table-header">Pelanggaran</th>
                    <th class="table-header">Poin</th>
                    <th class="table-header">Sisa</th>
                    <th class="table-header">Oleh</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="table-cell font-semibold">4 Mei 2026</td>
                    <td class="table-cell">Terlambat presensi pagi</td>
                    <td class="table-cell"><span class="badge badge-amber">-5</span></td>
                    <td class="table-cell font-semibold">95</td>
                    <td class="table-cell text-sm text-slate-500">BK: Siti Nurhaliza</td>
                </tr>
                <tr>
                    <td class="table-cell font-semibold">20 Apr 2026</td>
                    <td class="table-cell">Tidak memakai seragam</td>
                    <td class="table-cell"><span class="badge badge-red">-5</span></td>
                    <td class="table-cell font-semibold">100</td>
                    <td class="table-cell text-sm text-slate-500">BK: Siti Nurhaliza</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700">
        <i class="bi bi-info-circle-fill mr-1"></i> Notifikasi pelanggaran dikirim ke orang tua setiap pekan.
    </div>
</div>
@endsection