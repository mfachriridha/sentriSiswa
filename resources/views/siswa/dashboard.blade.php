@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<x-page-header title="Dashboard Siswa" :description="'Selamat datang, '.auth()->user()->nama.'!'" />

@php
    $pointsColor = $stats['points'] > 75 ? 'text-green-600' : ($stats['points'] > 50 ? 'text-amber-600' : 'text-red-600');
@endphp

{{-- Siswa perlu tahu ia terdaftar di kelas mana: kalau kelasnya keliru, semua yang
     lain ikut keliru - wali kelasnya, rekapnya, sampai laporan ke orang tuanya. --}}
<div class="mb-5 rounded-xl border border-gray-200 bg-white p-5">
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500">Kelas</p>
            <p class="mt-1 text-lg font-bold text-gray-900">{{ $identitas['kelas'] ?? 'Belum ada kelas' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500">NISN</p>
            <p class="mt-1 text-lg font-bold text-gray-900">{{ $identitas['nisn'] ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500">NIS</p>
            <p class="mt-1 text-lg font-bold text-gray-900">{{ $identitas['nis'] ?? '-' }}</p>
        </div>
    </div>
</div>

<div class="mb-5 rounded-xl border-2 border-primary/20 bg-primary/5 p-5">
    <p class="text-sm font-medium text-gray-600">Sisa Poin</p>
    <p class="mt-1 text-3xl font-bold {{ $pointsColor }}">{{ $stats['points'] }}<span class="text-base font-normal text-gray-500">/100</span></p>
</div>

<div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Hadir</p>
        <p class="mt-1 text-xl font-bold text-green-600">{{ $stats['hadir'] }}</p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Izin/Sakit/Disp</p>
        <p class="mt-1 text-xl font-bold text-blue-600">{{ $stats['izin_sakit'] }}</p>
    </div>
    <div class="col-span-2 rounded-xl border border-red-200 bg-red-50/70 p-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Akumulasi Alpha</p>
        <div class="mt-1 flex items-baseline gap-1.5">
            <p class="text-2xl font-bold text-red-600">{{ $stats['alpha'] }}</p>
            <span class="text-xs font-medium text-red-500">kali</span>
        </div>
    </div>
</div>

@if($stats['status_alpha']['kode'] !== 'normal')
    <div class="mb-5 rounded-2xl border p-5 sm:p-6 text-sm shadow-sm transition-all {{ match($stats['status_alpha']['kode']) {
        'wakasis' => 'border-red-200 bg-red-50/80 text-red-900',
        'sp2' => 'border-rose-200 bg-rose-50/80 text-rose-900',
        'sp1' => 'border-amber-200 bg-amber-50/80 text-amber-900',
        default => 'border-yellow-200 bg-yellow-50/80 text-yellow-900',
    } }}">
        <div class="flex items-center gap-2 mb-2">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-2.99L13.74 4a2 2 0 00-3.48 0L3.33 16.01A2 2 0 005.07 19z"/>
            </svg>
            <p class="text-base font-bold">Peringatan Presensi Siswa</p>
        </div>
        <p class="leading-relaxed">
            @if($stats['status_alpha']['kode'] === 'wakasis')
                Kamu telah mencatat {{ $stats['alpha'] }}x Alpha. Diharapkan untuk segera menghubungi Wali Kelas atau pihak Sekolah untuk tindak lanjut presensi.
            @elseif($stats['status_alpha']['kode'] === 'sp2' || $stats['status_alpha']['kode'] === 'sp1')
                Akumulasi Alpha kamu telah mencapai {{ $stats['alpha'] }}x. Diharapkan untuk berkoordinasi dengan Wali Kelas terkait presensimu.
            @else
                Kamu saat ini tercatat {{ $stats['alpha'] }}x Alpha. Mohon untuk selalu hadir tepat waktu dan menjaga konsistensi presensimu.
            @endif
        </p>
    </div>
@endif

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <x-shortcut-card :href="route('siswa.absensi')" title="Absensi" description="Catat kehadiran harian dan lihat status hari ini.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-6 9l2 2 4-4"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>

    <x-shortcut-card :href="route('siswa.poin')" title="Poin Saya" description="Pantau poin disiplin dan riwayat pelanggaran.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888 1.518-4.674a1 1 0 00-.363-1.118L3.665 8.291H8.58a1 1 0 00.95-.69l1.519-4.674z"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>

    <x-shortcut-card :href="route('siswa.profil')" title="Profil Saya" description="Lihat biodata dan perbarui kontak akun.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>

    <x-shortcut-card :href="route('siswa.tata-tertib.index')" title="Tata Tertib" description="Lihat PDF tata tertib sekolah yang berlaku.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7m0 8h.01M5 21h14a2 2 0 002-2V7l-6-6H5a2 2 0 00-2 2v16a2 2 0 002 2z"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>
</div>
@endsection
