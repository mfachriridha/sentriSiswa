@extends('layouts.app')

@section('title', 'Pelanggaran Siswa')

@section('content')
@php
    $categoryBadgeClasses = [
        'light' => 'bg-green-50 text-green-700',
        'medium' => 'bg-amber-50 text-amber-700',
        'heavy' => 'bg-orange-50 text-orange-700',
        'severe' => 'bg-red-50 text-red-700',
    ];

    $hasActiveFilters = filled($search) || filled($filterClass) || filled($filterCategory) || filled($filterViolationType) || filled($filterDate);
@endphp

<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Pelanggaran Siswa</h1>
        <p class="mt-2 text-sm text-gray-500">Catat dan kelola pelanggaran siswa berdasarkan jenis pelanggaran sekolah.</p>
    </div>
    <a href="{{ route('guru.pelanggaran-siswa.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
              hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Catat Pelanggaran
    </a>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<form method="GET" action="{{ route('guru.pelanggaran-siswa.index') }}" class="mb-4 rounded-xl border border-gray-200 bg-white p-4">
    <div class="grid grid-cols-1 gap-3 lg:grid-cols-6">
        <div class="relative lg:col-span-2">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari siswa, NIS, NISN, pelanggaran..."
                   class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400
                          focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
        </div>

        <select name="class_id"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
            <option value="">Semua Kelas</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" {{ (string) $filterClass === (string) $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
            @endforeach
        </select>

        <select name="category"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
            <option value="">Semua Kategori</option>
            @foreach ($categoryLabels as $category => $label)
                <option value="{{ $category }}" {{ $filterCategory === $category ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="violation_type_id"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
            <option value="">Semua Jenis</option>
            @foreach ($violationTypes as $violationType)
                <option value="{{ $violationType->id }}" {{ (string) $filterViolationType === (string) $violationType->id ? 'selected' : '' }}>
                    {{ $violationType->name }} ({{ $violationType->point_deduction }} poin)
                </option>
            @endforeach
        </select>

        <input type="date" name="violation_date" value="{{ $filterDate }}"
               class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-2">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">
        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark transition-colors">
            Terapkan
        </button>
        @if ($hasActiveFilters)
            <a href="{{ route('guru.pelanggaran-siswa.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Reset
            </a>
        @endif
    </div>
</form>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3"><x-sort-link label="Tanggal" column="violation_date" :sort="$sort" :direction="$direction" /></th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Siswa</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Kelas</th>
                    <th class="px-4 py-3"><x-sort-link label="Pelanggaran" column="violation_name" :sort="$sort" :direction="$direction" /></th>
                    <th class="px-4 py-3"><x-sort-link label="Kategori" column="violation_category" :sort="$sort" :direction="$direction" /></th>
                    <th class="px-4 py-3"><x-sort-link label="Poin" column="point_deduction" :sort="$sort" :direction="$direction" /></th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($studentViolations as $studentViolation)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700">{{ $studentViolation->violation_date?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $studentViolation->studentProfile?->user?->name ?? '-' }}</p>
                            <p class="mt-1 text-sm text-gray-500">NISN: {{ $studentViolation->studentProfile?->nisn ?? '-' }} · NIS: {{ $studentViolation->studentProfile?->nis ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $studentViolation->studentProfile?->class?->name ?? '-' }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $studentViolation->violation_name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $categoryBadgeClasses[$studentViolation->violation_category] ?? 'bg-gray-50 text-gray-700' }}">
                                {{ $categoryLabels[$studentViolation->violation_category] ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $studentViolation->point_deduction }} poin</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('guru.pelanggaran-siswa.show', $studentViolation) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                    Lihat
                                </a>
                                <a href="{{ route('guru.pelanggaran-siswa.edit', $studentViolation) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-primary/30 px-3 py-2 text-sm font-medium text-primary hover:bg-primary/5 transition-colors">
                                    Edit
                                </a>
                                <button type="button"
                                        onclick="window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                                            detail: {
                                                title: 'Hapus Pelanggaran Siswa',
                                                message: 'Yakin ingin menghapus catatan pelanggaran {{ $studentViolation->studentProfile?->user?->name ?? 'siswa ini' }}?',
                                                formId: 'delete-student-violation-{{ $studentViolation->id }}'
                                            }
                                        }))"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                    Hapus
                                </button>
                                <form id="delete-student-violation-{{ $studentViolation->id }}" method="POST" action="{{ route('guru.pelanggaran-siswa.destroy', $studentViolation) }}" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-sm text-gray-500">
                            {{ $hasActiveFilters ? 'Tidak ada pelanggaran siswa yang sesuai dengan filter.' : 'Belum ada catatan pelanggaran siswa.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($studentViolations->hasPages())
        <div class="border-t border-gray-200 px-4 py-3">
            <x-pagination :paginator="$studentViolations" />
        </div>
    @endif
</div>
@endsection
