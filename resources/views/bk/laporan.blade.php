@extends('layouts.app')

@section('title', 'Laporan - BK')

@section('page-title', 'Laporan Kehadiran & Poin')

@section('content')
<form method="GET" action="{{ route('bk.laporan') }}" class="flex flex-wrap items-end gap-3 mb-6 card !p-4 anim-up">
    <div>
        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Pilih Kelas</label>
        <select name="class_id" class="input-field !py-1.5 !text-xs" onchange="this.form.submit()">
            <option value="">— Pilih Kelas —</option>
            @foreach($bkClasses as $bc)
            <option value="{{ $bc->school_class_id }}" {{ $classId == $bc->school_class_id ? 'selected' : '' }}>{{ $bc->schoolClass->name }}</option>
            @endforeach
        </select>
    </div>
    @if($classId)
    <div>
        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Dari</label>
        <input type="date" name="start" value="{{ $start->toDateString() }}" class="input-field !py-1.5 !text-xs">
    </div>
    <div>
        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Sampai</label>
        <input type="date" name="end" value="{{ $end->toDateString() }}" class="input-field !py-1.5 !text-xs">
    </div>
    <button type="submit" class="btn-brand !py-1.5 !text-xs"><i class="bi bi-search"></i> Tampilkan</button>
    <a href="{{ route('bk.laporan') }}" class="btn-outline !py-1.5 !text-xs"><i class="bi bi-x-circle"></i> Reset</a>
    @endif
</form>

@if($classId)
<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-4">{{ $selectedClass->schoolClass->name }} &middot; {{ $start->translatedFormat('d M Y') }} – {{ $end->translatedFormat('d M Y') }}</h3>
    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-header">NIS</th>
                    <th class="table-header">Nama</th>
                    <th class="table-header">Hadir</th>
                    <th class="table-header">Izin</th>
                    <th class="table-header">Sakit</th>
                    <th class="table-header">Alfa</th>
                    <th class="table-header">Total</th>
                    <th class="table-header">Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                <tr>
                    <td class="table-cell text-xs font-mono">{{ $r['nis'] }}</td>
                    <td class="table-cell font-semibold text-xs">{{ $r['name'] }}</td>
                    <td class="table-cell"><span class="badge badge-green">{{ $r['hadir'] }}</span></td>
                    <td class="table-cell"><span class="badge badge-amber">{{ $r['izin'] }}</span></td>
                    <td class="table-cell"><span class="badge badge-blue">{{ $r['sakit'] }}</span></td>
                    <td class="table-cell"><span class="badge badge-red">{{ $r['alfa'] }}</span></td>
                    <td class="table-cell font-semibold text-xs">{{ $r['total'] }}</td>
                    <td class="table-cell text-xs">
                        <span class="{{ $r['poin'] >= 80 ? 'text-emerald-600' : ($r['poin'] >= 50 ? 'text-amber-600' : 'text-red-600') }} font-bold">{{ $r['poin'] }}</span>
                        @if($r['poin_dipotong'] > 0)<span class="text-red-500 text-[10px]">-{{ $r['poin_dipotong'] }}</span>@endif
                    </td>
                </tr>
                @empty
                <tr><td class="table-cell text-center text-muted py-8" colspan="8">Tidak ada data untuk periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card text-center py-12 anim-up">
    <i class="bi bi-bar-chart text-4xl text-slate-300 mb-3 block"></i>
    <p class="font-bold text-slate-700">Pilih kelas untuk melihat laporan</p>
    <p class="text-sm text-slate-500">Anda dapat memonitor kehadiran dan poin per kelas.</p>
</div>
@endif
@endsection
