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

<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <a href="{{ route('kesiswaan.pelanggaran-siswa.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
    <div class="flex flex-wrap gap-2">
        @if ($studentViolation->status === 'pending')
            <form method="POST" action="{{ route('kesiswaan.pelanggaran-siswa.approve', $studentViolation) }}">
                @csrf
                @method('PUT')
                <button class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition-colors">
                    ACC
                </button>
            </form>
        @endif
        <a href="{{ route('kesiswaan.pelanggaran-siswa.edit', $studentViolation) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            Edit
        </a>
    </div>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $studentViolation->studentProfile?->user?->name ?? 'Siswa' }}</h1>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $categoryBadgeClasses[$studentViolation->violation_category] ?? 'bg-gray-50 text-gray-700' }}">
                {{ $categoryLabels[$studentViolation->violation_category] ?? '-' }}
            </span>
            <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700">
                {{ $studentViolation->point_deduction }} poin
            </span>
            <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700">
                {{ $statusLabels[$studentViolation->status] ?? $studentViolation->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Nama Siswa</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $studentViolation->studentProfile?->user?->name ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Kelas</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $studentViolation->studentProfile?->class?->name ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NISN</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $studentViolation->studentProfile?->nisn ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NIS</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $studentViolation->studentProfile?->nis ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Tanggal Pelanggaran</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $studentViolation->violation_date?->translatedFormat('d F Y') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Dicatat oleh</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $studentViolation->recordedBy?->name ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5 md:col-span-2">
            <p class="text-sm font-medium text-gray-500">Jenis Pelanggaran</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $studentViolation->violation_name }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Kategori</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $categoryLabels[$studentViolation->violation_category] ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Poin Pelanggaran</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $studentViolation->point_deduction }} poin</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5 md:col-span-2">
            <p class="text-sm font-medium text-gray-500">Catatan</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $studentViolation->notes ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Disetujui/Diproses oleh</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $studentViolation->approvedBy?->name ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Waktu Proses</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $studentViolation->approved_at?->translatedFormat('d F Y H:i') ?? '-' }}</p>
        </div>
        @if ($studentViolation->rejection_reason)
            <div class="rounded-lg border border-red-100 bg-red-50 p-5 md:col-span-2">
                <p class="text-sm font-medium text-red-700">Alasan Penolakan</p>
                <p class="mt-1.5 text-sm text-red-700">{{ $studentViolation->rejection_reason }}</p>
            </div>
        @endif
    </div>
</div>

@if ($studentViolation->status === 'pending')
    <div class="mt-5 rounded-xl border border-red-200 bg-white p-6">
        <h2 class="text-lg font-semibold text-gray-900">Tolak Pengajuan</h2>
        <form method="POST" action="{{ route('kesiswaan.pelanggaran-siswa.reject', $studentViolation) }}" class="mt-4 space-y-3">
            @csrf
            @method('PUT')
            <textarea name="rejection_reason" rows="3" placeholder="Tuliskan alasan penolakan"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">{{ old('rejection_reason') }}</textarea>
            @error('rejection_reason') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Tolak Pengajuan</button>
        </form>
    </div>
@endif
@endsection
