@extends('layouts.app')

@section('title', 'Riwayat Kehadiran - SentriSiswa')

@section('page-title', 'Riwayat Kehadiran')

@section('content')
<!-- Stats -->
<div class="grid grid-cols-4 gap-3 mb-6">
    <div class="card text-center anim-up !p-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Hadir</p>
        <p class="text-xl font-extrabold text-emerald-600 mt-1">{{ $stats['hadir'] }}</p>
    </div>
    <div class="card text-center anim-up !p-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Izin</p>
        <p class="text-xl font-extrabold text-amber-600 mt-1">{{ $stats['izin'] }}</p>
    </div>
    <div class="card text-center anim-up !p-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Sakit</p>
        <p class="text-xl font-extrabold text-blue-600 mt-1">{{ $stats['sakit'] }}</p>
    </div>
    <div class="card text-center anim-up !p-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase">Alfa</p>
        <p class="text-xl font-extrabold text-red-600 mt-1">{{ $stats['alfa'] }}</p>
    </div>
</div>

<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-4">Riwayat Presensi</h3>
    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-header">Tanggal</th>
                    <th class="table-header">Jam</th>
                    <th class="table-header">Status</th>
                    <th class="table-header">Lokasi</th>
                    <th class="table-header">Selfie</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $a)
                <tr>
                    <td class="table-cell font-semibold text-xs">{{ $a->date->translatedFormat('l, d M Y') }}</td>
                    <td class="table-cell text-xs">{{ $a->created_at->format('H:i') }}</td>
                    <td class="table-cell">
                        @if($a->status === 'hadir')<span class="badge badge-green">Hadir</span>
                        @elseif($a->status === 'izin')<span class="badge badge-amber">Izin</span>
                        @elseif($a->status === 'sakit')<span class="badge badge-blue">Sakit</span>
                        @else<span class="badge badge-red">Alfa</span>@endif
                    </td>
                    <td class="table-cell text-xs">
                        @if($a->is_inside_geofence)<span class="text-emerald-600"><i class="bi bi-check-circle mr-1"></i>Area Sekolah</span>
                        @else<span class="text-amber-600"><i class="bi bi-exclamation-triangle mr-1"></i>Luar Area</span>@endif
                    </td>
                    <td class="table-cell">
                        @if($a->selfie_path)
                        <a href="{{ asset($a->selfie_path) }}" target="_blank" class="text-blue-600 hover:underline text-xs font-semibold"><i class="bi bi-camera-fill mr-1"></i>Lihat</a>
                        @else<span class="text-xs text-muted">—</span>@endif
                    </td>
                </tr>
                @empty
                <tr><td class="table-cell text-center text-muted py-8" colspan="5">Belum ada riwayat presensi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('components.pagination', ['data' => $attendances])
</div>
@endsection
