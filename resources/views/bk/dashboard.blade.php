@extends('layouts.app')

@section('title', 'Dashboard BK')

@section('page-title', 'Dashboard BK')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card text-center anim-up !p-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Kelas Dimonitor</p>
        <p class="text-xl font-extrabold text-[#001e40] mt-1">{{ $classStats->count() }}</p>
    </div>
    <div class="card text-center anim-up !p-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Total Siswa</p>
        <p class="text-xl font-extrabold text-[#001e40] mt-1">{{ $classStats->sum('total') }}</p>
    </div>
    <div class="card text-center anim-up !p-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Hadir Hari Ini</p>
        <p class="text-xl font-extrabold text-emerald-600 mt-1">{{ $classStats->sum('hadir') }}</p>
    </div>
    <div class="card text-center anim-up !p-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Kehadiran</p>
        <p class="text-xl font-extrabold text-blue-600 mt-1">{{ $classStats->sum('total') > 0 ? round($classStats->sum('hadir') / $classStats->sum('total') * 100) : 0 }}%</p>
    </div>
</div>

<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-4">Presensi Hari Ini per Kelas</h3>
    @if($classStats->isEmpty())
    <p class="text-sm text-muted text-center py-8">Anda belum di-assign ke kelas manapun. Hubungi admin.</p>
    @else
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">Kelas</th><th class="table-header">Hadir</th><th class="table-header">Total</th><th class="table-header">%</th><th class="table-header text-center">Aksi</th></tr></thead>
            <tbody>
                @foreach($classStats as $cs)
                <tr>
                    <td class="table-cell font-semibold">{{ $cs['name'] }}</td>
                    <td class="table-cell"><span class="badge badge-green">{{ $cs['hadir'] }}</span></td>
                    <td class="table-cell">{{ $cs['total'] }}</td>
                    <td class="table-cell font-bold text-xs">{{ $cs['total'] > 0 ? round($cs['hadir'] / $cs['total'] * 100) : 0 }}%</td>
                    <td class="table-cell-aksi">
                        <a href="{{ route('bk.laporan', ['class_id' => $cs['class_id']]) }}" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold border border-[#c3c6d1] text-[#43474f] hover:bg-[#edeeef] transition"><i class="bi bi-bar-chart"></i> Lihat Laporan</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
