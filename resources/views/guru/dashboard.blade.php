@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<x-page-header title="Dashboard Guru" :description="'Selamat datang, '.auth()->user()->nama.'!'" />

@if (! empty($summary['homeroom']))
    <div class="mb-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-gray-500">Kelas</p>
            <p class="mt-1 text-xl font-bold text-gray-900">{{ $summary['homeroom']['class_name'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-gray-500">Hadir</p>
            <p class="mt-1 text-xl font-bold text-green-600">{{ $summary['homeroom']['hadir'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-gray-500">Izin/Sakit/Disp</p>
            <p class="mt-1 text-xl font-bold text-blue-600">{{ $summary['homeroom']['izin_sakit'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-gray-500">Alpha</p>
            <p class="mt-1 text-xl font-bold text-red-600">{{ $summary['homeroom']['alpha'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-gray-500">Belum Absen</p>
            <p class="mt-1 text-xl font-bold text-gray-700">{{ $summary['homeroom']['belum_absen'] }}</p>
        </div>
    </div>
@endif

@if (! empty($summary['bk']))
    <div class="mb-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-gray-500">Tingkat BK</p>
            <p class="mt-1 text-xl font-bold text-gray-900">{{ $summary['bk']['grade'] ?? '-' }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-gray-500">Kelas</p>
            <p class="mt-1 text-xl font-bold text-gray-900">{{ $summary['bk']['classes'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-gray-500">Siswa</p>
            <p class="mt-1 text-xl font-bold text-gray-900">{{ $summary['bk']['students'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-gray-500">Pelanggaran</p>
            <p class="mt-1 text-xl font-bold text-gray-900">{{ $summary['bk']['pelanggaran'] }}</p>
        </div>
    </div>
@endif

@if (! empty($summary['kesiswaan']))
    <div class="mb-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-gray-500">Kelas</p>
            <p class="mt-1 text-xl font-bold text-gray-900">{{ $summary['kesiswaan']['classes'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-gray-500">Siswa</p>
            <p class="mt-1 text-xl font-bold text-gray-900">{{ $summary['kesiswaan']['students'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-gray-500">Pelanggaran Dicatat</p>
            <p class="mt-1 text-xl font-bold text-green-600">{{ $summary['kesiswaan']['approved'] }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs font-semibold uppercase text-amber-600">Pengajuan Poin Pending</p>
            <p class="mt-1 text-xl font-bold text-amber-700">{{ $summary['kesiswaan']['pengajuan_poin_pending'] }}</p>
        </div>
    </div>
@endif

@php
    $alphaList = collect();
    if (auth()->user()->isWaliKelas() && !empty($summary['homeroom']['alpha_students'])) {
        $alphaList = $summary['homeroom']['alpha_students'];
    } elseif (auth()->user()->isBk() && !empty($summary['bk']['alpha_students'])) {
        $alphaList = $summary['bk']['alpha_students'];
    } elseif (auth()->user()->isKesiswaan() && !empty($summary['kesiswaan']['alpha_students'])) {
        $alphaList = $summary['kesiswaan']['alpha_students'];
    }
@endphp

@if ($alphaList->isNotEmpty())
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-red-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Siswa Memiliki Catatan Alpha</h2>
                    <p class="text-xs text-gray-500">Daftar siswa yang memiliki ketidakhadiran tanpa keterangan (Alpha &ge; 1 kali)</p>
                </div>
            </div>
            <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 border border-red-100">
                {{ $alphaList->count() }} Siswa
            </span>
        </div>

        <div class="max-h-96 space-y-2.5 overflow-y-auto pr-1">
            @foreach ($alphaList as $student)
                @php
                    $warn = $student->status_alpha;
                    $badgeBg = match ($warn['kode']) {
                        'dikembalikan' => 'bg-red-100 text-red-800 border-red-200',
                        'sp2' => 'bg-rose-100 text-rose-800 border-rose-200',
                        'sp1' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'wali_kelas' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                    };
                @endphp
                <div class="flex flex-col gap-3 rounded-lg border border-gray-100 bg-gray-50/60 p-3 sm:flex-row sm:items-center sm:justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        @if ($student->foto)
                            <img src="{{ asset('storage/'.$student->foto) }}" alt="{{ $student->pengguna->nama }}" class="h-10 w-10 shrink-0 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">
                                {{ strtoupper(substr($student->pengguna->nama ?? 'S', 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-gray-900">{{ $student->pengguna->nama }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $student->kelas->nama ?? '-' }} &middot; NISN: {{ $student->nisn }}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $badgeBg }}">
                                {{ $warn['label'] }}
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-md bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700 border border-red-200">
                                {{ $student->alpha_count }}x Alpha
                            </span>
                        </div>
                        <a href="{{ $student->detail_url }}" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline">
                            Detail
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @if (auth()->user()->isWaliKelas())
        <x-shortcut-card :href="route('wali-kelas.kelas-saya')" title="Kelas Saya" description="Pantau status absensi harian siswa.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>

        <x-shortcut-card :href="route('wali-kelas.absensi.index')" title="Rekap Absensi" description="Filter dan ekspor laporan kehadiran kelas.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-6 9l2 2 4-4"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>

        <x-shortcut-card :href="route('wali-kelas.pengajuan-poin.index')" title="Pengajuan Poin" description="Ajukan penambahan poin untuk siswa di kelas Anda.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>
    @endif

    @if (auth()->user()->isKesiswaan())
        <x-shortcut-card :href="route('kesiswaan.monitoring.index')" title="Monitoring Siswa" description="Cari siswa dan periksa catatan disiplin.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>

        <x-shortcut-card :href="route('kesiswaan.pelanggaran-siswa.index')" title="Pelanggaran Siswa" description="Catat dan kelola pelanggaran siswa.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-2.99L13.74 4a2 2 0 00-3.48 0L3.33 16.01A2 2 0 005.07 19z"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>

        <x-shortcut-card :href="route('kesiswaan.pengajuan-poin.persetujuan')" title="Pengajuan Poin" description="Terima atau tolak pengajuan penambahan poin dari wali kelas.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>

        <x-shortcut-card :href="route('kesiswaan.laporan.index')" title="Laporan Kesiswaan" description="Filter dan export laporan pelanggaran.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h13M9 7h13M5 7h.01M5 17h.01"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>

        <x-shortcut-card :href="route('kesiswaan.tata-tertib.index')" title="Tata Tertib" description="Upload dan publikasikan PDF tata tertib.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7m0 8h.01M5 21h14a2 2 0 002-2V7l-6-6H5a2 2 0 00-2 2v16a2 2 0 002 2z"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>
    @endif

    @if (auth()->user()->isBk())
        <x-shortcut-card :href="route('bk.monitoring.index')" title="Monitoring BK" description="Pantau siswa berdasarkan tingkat BK.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>

        <x-shortcut-card :href="route('bk.laporan.index')" title="Laporan BK" description="Rekap pelanggaran tingkat yang ditugaskan.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h13M9 7h13M5 7h.01M5 17h.01"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>
    @endif

    <x-shortcut-card :href="route(auth()->user()->profilRouteName())" title="Profil Saya" description="Periksa dan perbarui informasi akun.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>
</div>
@endsection
