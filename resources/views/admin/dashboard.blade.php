@extends('layouts.app')

@section('title', 'Dashboard - Admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card anim-up"><p class="text-xs font-bold text-[#43474f] uppercase tracking-wide">Total Guru</p><p class="stat-value mt-1">42</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-[#43474f] uppercase tracking-wide">Total Siswa</p><p class="stat-value mt-1">360</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-[#43474f] uppercase tracking-wide">Total Kelas</p><p class="stat-value mt-1">12</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-[#43474f] uppercase tracking-wide">Hadir Hari Ini</p><p class="stat-value text-emerald-600 mt-1">342</p></div>
</div>

<div class="card anim-up" style="animation-delay:.1s">
    <h3 class="font-bold text-slate-900 mb-4">Aksi Cepat</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('admin.guru') }}" class="flex flex-col items-center gap-2 p-4 border-2 border-slate-200 rounded-xl hover:border-blue-300 hover:bg-blue-50/50 transition-all group">
            <i class="bi bi-people-fill text-2xl text-blue-500 group-hover:scale-110 transition-transform"></i>
            <span class="text-xs font-bold text-slate-700">Manajemen Guru</span>
        </a>
        <a href="{{ route('admin.siswa') }}" class="flex flex-col items-center gap-2 p-4 border-2 border-slate-200 rounded-xl hover:border-emerald-300 hover:bg-emerald-50/50 transition-all group">
            <i class="bi bi-mortarboard-fill text-2xl text-emerald-500 group-hover:scale-110 transition-transform"></i>
            <span class="text-xs font-bold text-slate-700">Manajemen Siswa</span>
        </a>
        <a href="{{ route('admin.kelas') }}" class="flex flex-col items-center gap-2 p-4 border-2 border-slate-200 rounded-xl hover:border-purple-300 hover:bg-purple-50/50 transition-all group">
            <i class="bi bi-diagram-3-fill text-2xl text-purple-500 group-hover:scale-110 transition-transform"></i>
            <span class="text-xs font-bold text-slate-700">Manajemen Kelas</span>
        </a>
        <a href="{{ route('admin.laporan') }}" class="flex flex-col items-center gap-2 p-4 border-2 border-slate-200 rounded-xl hover:border-amber-300 hover:bg-amber-50/50 transition-all group">
            <i class="bi bi-bar-chart-fill text-2xl text-amber-500 group-hover:scale-110 transition-transform"></i>
            <span class="text-xs font-bold text-slate-700">Laporan</span>
        </a>
    </div>
</div>
@endsection