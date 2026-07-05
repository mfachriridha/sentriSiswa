@extends('layouts.app')

@section('title', 'Detail Pelanggaran Siswa')

@section('content')
@php
    $categoryBadgeClasses = [
        'ringan' => 'bg-green-50 text-green-700',
        'sedang' => 'bg-amber-50 text-amber-700',
        'berat' => 'bg-orange-50 text-orange-700',
        'sangat_berat' => 'bg-red-50 text-red-700',
    ];
@endphp

<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <a href="{{ route('kesiswaan.pelanggaran-siswa.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('kesiswaan.pelanggaran-siswa.edit', $pelanggaranSiswa) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            Edit
        </a>
    </div>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $pelanggaranSiswa->profilSiswa?->pengguna?->nama ?? 'Siswa' }}</h1>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $categoryBadgeClasses[$pelanggaranSiswa->kategori_pelanggaran] ?? 'bg-gray-50 text-gray-700' }}">
                {{ $categoryLabels[$pelanggaranSiswa->kategori_pelanggaran] ?? '-' }}
            </span>
            <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700">
                {{ $pelanggaranSiswa->pengurangan_poin }} poin
            </span>
            <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700">
                {{ $statusLabels[$pelanggaranSiswa->status] ?? $pelanggaranSiswa->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Nama Siswa</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $pelanggaranSiswa->profilSiswa?->pengguna?->nama ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Kelas</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $pelanggaranSiswa->profilSiswa?->kelas?->nama ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NISN</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $pelanggaranSiswa->profilSiswa?->nisn ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NIS</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $pelanggaranSiswa->profilSiswa?->nis ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Tanggal Pelanggaran</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $pelanggaranSiswa->tanggal_pelanggaran?->translatedFormat('d F Y') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Dicatat oleh</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $pelanggaranSiswa->dicatatOleh?->nama ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5 md:col-span-2">
            <p class="text-sm font-medium text-gray-500">Jenis Pelanggaran</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $pelanggaranSiswa->nama_pelanggaran }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Kategori</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $categoryLabels[$pelanggaranSiswa->kategori_pelanggaran] ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Poin Pelanggaran</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $pelanggaranSiswa->pengurangan_poin }} poin</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5 md:col-span-2">
            <p class="text-sm font-medium text-gray-500">Catatan</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $pelanggaranSiswa->catatan ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Disetujui/Diproses oleh</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $pelanggaranSiswa->disetujuiOleh?->nama ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Waktu Proses</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $pelanggaranSiswa->disetujui_pada?->translatedFormat('d F Y H:i') ?? '-' }}</p>
        </div>
        @if ($pelanggaranSiswa->alasan_penolakan)
            <div class="rounded-lg border border-red-100 bg-red-50 p-5 md:col-span-2">
                <p class="text-sm font-medium text-red-700">Alasan Penolakan</p>
                <p class="mt-1.5 text-sm text-red-700">{{ $pelanggaranSiswa->alasan_penolakan }}</p>
            </div>
        @endif
    </div>
</div>

@endsection
