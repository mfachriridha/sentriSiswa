@extends('layouts.app')

@section('title', $title)

@section('content')
<x-page-header :title="$title" description="Rekap pelanggaran siswa dengan filter dan export laporan." />

<div class="mb-4 rounded-xl border border-gray-200 bg-white p-4">
    <form method="GET" action="{{ route($routeName.'.index') }}" class="grid gap-3 md:grid-cols-3 xl:grid-cols-6">
        <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <select name="class_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua Kelas</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" {{ ($filters['class_id'] ?? '') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
            @endforeach
        </select>
        <select name="category" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua Kategori</option>
            @foreach ($categoryLabels as $value => $label)
                <option value="{{ $value }}" {{ ($filters['category'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            @foreach ($statusLabels as $value => $label)
                <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Terapkan</button>
    </form>
    <div class="mt-3 flex flex-wrap gap-2">
        <a href="{{ route($routeName.'.export-excel', request()->query()) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Export Excel</a>
        <a href="{{ route($routeName.'.export-pdf', request()->query()) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Export PDF</a>
    </div>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Siswa</th>
                    <th class="px-4 py-3">Kelas</th>
                    <th class="px-4 py-3">Pelanggaran</th>
                    <th class="px-4 py-3">Poin</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($violations as $violation)
                    <tr>
                        <td class="px-4 py-3">{{ $violation->violation_date->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $violation->studentProfile?->user?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $violation->studentProfile?->class?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $violation->violation_name }}</td>
                        <td class="px-4 py-3 font-semibold text-red-600">-{{ $violation->point_deduction }}</td>
                        <td class="px-4 py-3">{{ $statusLabels[$violation->status] ?? $violation->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-16 text-center text-sm text-gray-500">Tidak ada data laporan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($violations->hasPages())
        <div class="border-t border-gray-200 px-4 py-3">{{ $violations->links() }}</div>
    @endif
</div>
@endsection
