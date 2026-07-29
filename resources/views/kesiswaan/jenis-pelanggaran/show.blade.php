@extends('layouts.app')

@section('title', 'Detail Jenis Pelanggaran')

@section('content')
@php
    $categoryBadgeClasses = \App\Models\JenisPelanggaran::categoryBadgeClasses();
@endphp

<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <a href="{{ route('kesiswaan.jenis-pelanggaran.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
    <a href="{{ route('kesiswaan.jenis-pelanggaran.edit', $jenisPelanggaran) }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
              hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $jenisPelanggaran->nama }}</h1>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $categoryBadgeClasses[$jenisPelanggaran->kategori] ?? 'bg-gray-50 text-gray-700' }}">
                {{ $categoryLabels[$jenisPelanggaran->kategori] ?? '-' }}
            </span>
            @if ($jenisPelanggaran->aktif)
                <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">Aktif</span>
            @else
                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600">Nonaktif</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Kategori</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $categoryLabels[$jenisPelanggaran->kategori] ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Poin Pelanggaran</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $jenisPelanggaran->pengurangan_poin }} poin</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Status</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $jenisPelanggaran->aktif ? 'Aktif' : 'Nonaktif' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Terakhir Diubah</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $jenisPelanggaran->diperbarui_pada?->translatedFormat('d F Y H:i') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5 md:col-span-2">
            <p class="text-sm font-medium text-gray-500">Keterangan</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $jenisPelanggaran->keterangan ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
