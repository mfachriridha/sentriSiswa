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

<!-- Chart -->
<div class="card anim-up mb-6" style="animation-delay:.1s">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-slate-900">Grafik Kehadiran</h3>
        <div class="flex gap-1 p-1 bg-[#edeeef] rounded-lg">
            <button onclick="switchChart('bar')" id="chart-bar" class="px-3 py-1.5 text-xs font-bold rounded-md bg-white shadow-sm text-[#001e40] transition">Bar</button>
            <button onclick="switchChart('line')" id="chart-line" class="px-3 py-1.5 text-xs font-bold rounded-md text-muted hover:text-[#191c1d] transition">Line</button>
            <button onclick="switchChart('pie')" id="chart-pie" class="px-3 py-1.5 text-xs font-bold rounded-md text-muted hover:text-[#191c1d] transition">Pie</button>
        </div>
    </div>

    <!-- Bar Chart -->
    <div id="chart-bar-view" class="h-52 flex items-end justify-around gap-2 px-4 pb-2 border-b border-slate-200">
        <div class="flex flex-col items-center gap-1 flex-1"><div class="w-full bg-blue-500 rounded-t" style="height:60%"></div><span class="text-[10px] text-muted">Sen</span></div>
        <div class="flex flex-col items-center gap-1 flex-1"><div class="w-full bg-blue-500 rounded-t" style="height:75%"></div><span class="text-[10px] text-muted">Sel</span></div>
        <div class="flex flex-col items-center gap-1 flex-1"><div class="w-full bg-emerald-500 rounded-t" style="height:90%"></div><span class="text-[10px] text-muted">Rab</span></div>
        <div class="flex flex-col items-center gap-1 flex-1"><div class="w-full bg-blue-500 rounded-t" style="height:65%"></div><span class="text-[10px] text-muted">Kam</span></div>
        <div class="flex flex-col items-center gap-1 flex-1"><div class="w-full bg-amber-500 rounded-t" style="height:45%"></div><span class="text-[10px] text-muted">Jum</span></div>
        <div class="flex flex-col items-center gap-1 flex-1"><div class="w-full bg-blue-500 rounded-t" style="height:80%"></div><span class="text-[10px] text-muted">Sab</span></div>
    </div>

    <!-- Line Chart -->
    <div id="chart-line-view" class="hidden h-52 flex items-center justify-center px-4">
        <svg viewBox="0 0 300 160" class="w-full h-full">
            <polyline points="20,120 70,80 120,30 170,90 220,50 270,70" fill="none" stroke="#0062a0" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="20" cy="120" r="4" fill="#0062a0"/><circle cx="70" cy="80" r="4" fill="#0062a0"/>
            <circle cx="120" cy="30" r="4" fill="#0062a0"/><circle cx="170" cy="90" r="4" fill="#0062a0"/>
            <circle cx="220" cy="50" r="4" fill="#0062a0"/><circle cx="270" cy="70" r="4" fill="#0062a0"/>
            <text x="20" y="140" font-size="9" fill="#43474f" text-anchor="middle">Sen</text>
            <text x="70" y="140" font-size="9" fill="#43474f" text-anchor="middle">Sel</text>
            <text x="120" y="140" font-size="9" fill="#43474f" text-anchor="middle">Rab</text>
            <text x="170" y="140" font-size="9" fill="#43474f" text-anchor="middle">Kam</text>
            <text x="220" y="140" font-size="9" fill="#43474f" text-anchor="middle">Jum</text>
            <text x="270" y="140" font-size="9" fill="#43474f" text-anchor="middle">Sab</text>
        </svg>
    </div>

    <!-- Pie Chart -->
    <div id="chart-pie-view" class="hidden h-52 flex items-center justify-center gap-8">
        <div class="relative w-36 h-36">
            <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#dcfce7" stroke-width="3.5"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#15803d" stroke-width="3.5" stroke-dasharray="85 15" stroke-dashoffset="0"/>
                <circle cx="18" cy="18" r="12.5" fill="none" stroke="#fef3c7" stroke-width="3.5"/>
                <circle cx="18" cy="18" r="12.5" fill="none" stroke="#a16207" stroke-width="3.5" stroke-dasharray="12 88" stroke-dashoffset="0"/>
                <circle cx="18" cy="18" r="9" fill="none" stroke="#fee2e2" stroke-width="3.5"/>
                <circle cx="18" cy="18" r="9" fill="none" stroke="#ba1a1a" stroke-width="3.5" stroke-dasharray="3 97" stroke-dashoffset="0"/>
            </svg>
        </div>
        <div class="space-y-1.5 text-xs">
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-emerald-600"></span> Hadir <strong>85%</strong></div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-amber-600"></span> Izin <strong>12%</strong></div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-red-600"></span> Alpha <strong>3%</strong></div>
        </div>
    </div>
</div>

<div class="card anim-up" style="animation-delay:.15s">
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

<script>
function switchChart(t) {
    ['bar','line','pie'].forEach(c => {
        document.getElementById('chart-'+c+'-view').classList.add('hidden');
        const btn = document.getElementById('chart-'+c);
        btn.classList.remove('bg-white','shadow-sm','text-[#001e40]');
        btn.classList.add('text-muted');
    });
    document.getElementById('chart-'+t+'-view').classList.remove('hidden');
    const b = document.getElementById('chart-'+t);
    b.classList.add('bg-white','shadow-sm','text-[#001e40]');
    b.classList.remove('text-muted');
}
</script>
@endsection