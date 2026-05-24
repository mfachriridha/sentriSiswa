@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Absensi</h1>
    <p class="mt-1 text-base text-gray-500">Catat kehadiran harian Anda</p>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

{{-- Today's attendance card --}}
<div class="rounded-xl border border-gray-200 bg-white p-8">
    <h2 class="text-lg font-semibold text-gray-900 mb-6">Absen Hari Ini</h2>

    <div class="flex items-center justify-between">
        <div>
            <p class="text-base text-gray-600">{{ now()->translatedFormat('l, d F Y') }}</p>
            <p class="text-sm text-gray-400 mt-1">Jam absen: {{ $startTime }} — {{ $endTime }}</p>
        </div>

        @if($todayAttendance)
            <div class="text-right">
                @php
                    $statusConfig = [
                        'hadir' => ['bg-green-50 text-green-700', 'Hadir'],
                        'terlambat' => ['bg-amber-50 text-amber-700', 'Terlambat'],
                        'izin' => ['bg-blue-50 text-blue-700', 'Izin'],
                        'sakit' => ['bg-purple-50 text-purple-700', 'Sakit'],
                        'alpha' => ['bg-red-50 text-red-700', 'Alpha'],
                    ];
                    [$badgeClass, $statusLabel] = $statusConfig[$todayAttendance->status] ?? ['bg-gray-50 text-gray-700', $todayAttendance->status];
                @endphp
                <span class="inline-flex items-center rounded-full {{ $badgeClass }} px-4 py-2 text-base font-semibold">
                    {{ $statusLabel }}
                </span>
                @if($todayAttendance->check_in_time)
                    <p class="text-sm text-gray-500 mt-2">Absen pukul {{ $todayAttendance->check_in_time }}</p>
                @endif
            </div>
        @elseif($canCheckIn)
            <form method="POST" action="{{ route('siswa.absensi.store') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-base font-semibold text-white shadow-sm
                               hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Absen Sekarang
                </button>
            </form>
        @else
            <p class="text-base text-gray-400">
                @if(now()->format('H:i') < $startTime)
                    Belum waktunya absen. Absen dimulai pukul {{ $startTime }}.
                @else
                    Waktu absen sudah berakhir.
                @endif
            </p>
        @endif
    </div>
</div>

{{-- Monthly stats --}}
<div class="mt-6 rounded-xl border border-gray-200 bg-white p-8">
    <h2 class="text-lg font-semibold text-gray-900 mb-6">Ringkasan Bulan Ini</h2>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-center">
            <p class="text-2xl font-bold text-green-700">{{ $stats['hadir'] }}</p>
            <p class="text-sm font-medium text-green-600 mt-1">Hadir</p>
        </div>
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-center">
            <p class="text-2xl font-bold text-amber-700">{{ $stats['terlambat'] }}</p>
            <p class="text-sm font-medium text-amber-600 mt-1">Terlambat</p>
        </div>
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-center">
            <p class="text-2xl font-bold text-blue-700">{{ $stats['izin'] }}</p>
            <p class="text-sm font-medium text-blue-600 mt-1">Izin</p>
        </div>
        <div class="rounded-lg border border-purple-200 bg-purple-50 p-4 text-center">
            <p class="text-2xl font-bold text-purple-700">{{ $stats['sakit'] }}</p>
            <p class="text-sm font-medium text-purple-600 mt-1">Sakit</p>
        </div>
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-center">
            <p class="text-2xl font-bold text-red-700">{{ $stats['alpha'] }}</p>
            <p class="text-sm font-medium text-red-600 mt-1">Alpha</p>
        </div>
    </div>
</div>
@endsection