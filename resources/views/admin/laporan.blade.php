@extends('layouts.app')

@section('title', 'Laporan - Admin')

@section('page-title', 'Laporan Kehadiran')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="flex gap-2">
        <select class="input-field !w-auto !py-1.5 !text-xs"><option>Semua Kelas</option><option>XII IPA 1</option><option>XII IPA 2</option></select>
        <select class="input-field !w-auto !py-1.5 !text-xs"><option>Bulan Ini</option><option>Bulan Lalu</option><option>Semester Ini</option></select>
    </div>
    <button onclick="alert('UI Only: Download Excel!')" class="btn-brand !text-xs"><i class="bi bi-file-earmark-excel"></i> Download Excel</button>
</div>

<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-4">Rekap Kehadiran</h3>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">NIS</th><th class="table-header">Nama</th><th class="table-header">Kelas</th><th class="table-header">Hadir</th><th class="table-header">Izin</th><th class="table-header">Sakit</th><th class="table-header">Alpha</th><th class="table-header">%</th></tr></thead>
            <tbody>
                <tr><td class="table-cell">S001</td><td class="table-cell font-semibold">Ahmad Fauzi</td><td class="table-cell">XII IPA 1</td><td class="table-cell">18</td><td class="table-cell">1</td><td class="table-cell">0</td><td class="table-cell">1</td><td class="table-cell font-bold">90%</td></tr>
                <tr><td class="table-cell">S002</td><td class="table-cell font-semibold">Nurul Hidayah</td><td class="table-cell">XII IPA 1</td><td class="table-cell">19</td><td class="table-cell">0</td><td class="table-cell">1</td><td class="table-cell">0</td><td class="table-cell font-bold">95%</td></tr>
                <tr><td class="table-cell">S003</td><td class="table-cell font-semibold">Budi Darmawan</td><td class="table-cell">XII IPA 1</td><td class="table-cell">17</td><td class="table-cell">1</td><td class="table-cell">1</td><td class="table-cell">1</td><td class="table-cell font-bold">85%</td></tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex items-center gap-2 text-xs text-slate-400">
        <i class="bi bi-info-circle"></i> Data semester genap 2025/2026.
    </div>
</div>
@endsection