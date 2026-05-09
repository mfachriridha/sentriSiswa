@extends('layouts.app')

@section('title', 'Dashboard - SentriSiswa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Dashboard</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ now()->translatedFormat('l, d F Y') }} &middot; Kelas XII IPA 1</p>
    </div>
    <span class="badge badge-blue self-start">Ahmad Fauzi</span>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card anim-up">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Kehadiran Bulan Ini</p>
        <p class="stat-value text-emerald-600 mt-1">92%</p>
        <p class="text-xs text-slate-500 mt-0.5">23 dari 25 hari</p>
    </div>
    <div class="card anim-up">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Poin Pelanggaran</p>
        <p class="stat-value text-blue-600 mt-1">95</p>
        <p class="text-xs text-slate-500 mt-0.5">Dari 100 poin awal</p>
    </div>
    <div class="card anim-up">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Presensi Hari Ini</p>
        <p class="stat-value text-emerald-600 mt-1">Hadir</p>
        <p class="text-xs text-slate-500 mt-0.5">Masuk 07:15 WIB</p>
    </div>
    <div class="card anim-up">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Sisa Poin</p>
        <p class="stat-value text-amber-600 mt-1">5</p>
        <p class="text-xs text-slate-500 mt-0.5">Poin berkurang</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid gap-5 mb-6">
    <div class="card anim-up" style="animation-delay:.1s">
        <h3 class="font-bold text-slate-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('siswa.kehadiran') }}" class="flex flex-col items-center gap-2 p-4 border-2 border-slate-200 rounded-xl hover:border-emerald-300 hover:bg-emerald-50/50 transition-all group">
                <i class="bi bi-camera-fill text-2xl text-emerald-500 group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-bold text-slate-700">Presensi Masuk</span>
            </a>
            <a href="{{ route('siswa.kehadiran') }}" class="flex flex-col items-center gap-2 p-4 border-2 border-slate-200 rounded-xl hover:border-orange-300 hover:bg-orange-50/50 transition-all group">
                <i class="bi bi-box-arrow-left text-2xl text-orange-500 group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-bold text-slate-700">Presensi Pulang</span>
            </a>
            <a href="{{ route('siswa.riwayat') }}" class="flex flex-col items-center gap-2 p-4 border-2 border-slate-200 rounded-xl hover:border-blue-300 hover:bg-blue-50/50 transition-all group">
                <i class="bi bi-clock-history text-2xl text-blue-500 group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-bold text-slate-700">Riwayat</span>
            </a>
            <a href="{{ route('siswa.poin') }}" class="flex flex-col items-center gap-2 p-4 border-2 border-slate-200 rounded-xl hover:border-purple-300 hover:bg-purple-50/50 transition-all group">
                <i class="bi bi-star-fill text-2xl text-purple-500 group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-bold text-slate-700">Poin Saya</span>
            </a>
        </div>
    </div>
</div>

<!-- Status Hari Ini -->
<div class="card anim-up" style="animation-delay:.15s">
    <h3 class="font-bold text-slate-900 mb-4">Status Hari Ini</h3>
    <div class="flex items-center gap-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
        <div class="w-11 h-11 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
            <i class="bi bi-check-circle-fill text-emerald-600 text-xl"></i>
        </div>
        <div class="flex-1">
            <p class="font-bold text-emerald-800">Sudah Presensi Masuk</p>
            <p class="text-sm text-emerald-700">07:15 WIB &middot; Lokasi valid</p>
        </div>
        <a href="{{ route('siswa.kehadiran') }}" class="btn-outline !py-2 !px-3 text-xs">
            <i class="bi bi-arrow-right"></i> Detail
        </a>
    </div>
</div>
@endsection