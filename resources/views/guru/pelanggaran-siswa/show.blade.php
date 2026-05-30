@extends('layouts.app')

@section('title', 'Detail Pelanggaran Siswa')

@section('content')
@php
    $categoryBadgeClasses = [
        'light' => 'bg-green-50 text-green-700',
        'medium' => 'bg-amber-50 text-amber-700',
        'heavy' => 'bg-orange-50 text-orange-700',
        'severe' => 'bg-red-50 text-red-700',
    ];
@endphp

<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('guru.pelanggaran-siswa.index') }}"
       class="inline-flex items-center gap-2 text-base text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
    <a href="{{ route('guru.pelanggaran-siswa.edit', $studentViolation) }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-3 text-base font-semibold text-white shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
        Edit
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ $studentViolation->studentProfile?->user?->name ?? 'Siswa' }}</h1>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $categoryBadgeClasses[$studentViolation->violation_category] ?? 'bg-gray-50 text-gray-700' }}">
                {{ $categoryLabels[$studentViolation->violation_category] ?? '-' }}
            </span>
            <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700">
                {{ $studentViolation->point_deduction }} poin
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Nama Siswa</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $studentViolation->studentProfile?->user?->name ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Kelas</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $studentViolation->studentProfile?->class?->name ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NISN</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $studentViolation->studentProfile?->nisn ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NIS</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $studentViolation->studentProfile?->nis ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Tanggal Pelanggaran</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $studentViolation->violation_date?->format('d/m/Y') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Dicatat oleh</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $studentViolation->recordedBy?->name ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5 md:col-span-2">
            <p class="text-sm font-medium text-gray-500">Jenis Pelanggaran</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $studentViolation->violation_name }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Kategori</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $categoryLabels[$studentViolation->violation_category] ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Poin Pelanggaran</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $studentViolation->point_deduction }} poin</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5 md:col-span-2">
            <p class="text-sm font-medium text-gray-500">Catatan</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $studentViolation->notes ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
