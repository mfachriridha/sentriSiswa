@extends('layouts.app')

@section('title', 'Preview Import')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.teachers.import') }}"
       class="inline-flex items-center gap-2 text-base text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-8">
    <h1 class="mb-2 text-2xl font-bold text-gray-900">Preview Import</h1>
    <p class="mb-8 text-base text-gray-500">Total {{ $totalRows }} baris akan diproses</p>

    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full text-left text-base">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-6 py-4 font-semibold text-gray-600">#</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Kelas</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Nama Guru</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">NIP</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($previewRows as $index => $row)
                    <tr>
                        <td class="px-6 py-4 text-gray-700">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $row['kelas'] ?? '-' }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $row['walas'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $row['nip'] ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if (empty(trim($row['walas'] ?? '')))
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700">
                                    ✗ Nama kosong
                                </span>
                            @elseif (empty(trim($row['nip'] ?? '')) || $row['nip'] === '-')
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700">
                                    ⚠ NIP kosong
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">
                                    ✓ Valid
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex items-center justify-between">
        <p class="text-base text-gray-500">Total {{ $totalRows }} baris akan diproses</p>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.teachers.index') }}"
               class="rounded-lg border border-gray-300 px-6 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <form method="POST" action="{{ route('admin.teachers.import.store') }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf
                <input type="hidden" name="file_path" value="{{ $filePath }}">
                <button type="submit" :disabled="loading"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-base font-semibold text-white shadow-sm
                               hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                               transition-colors disabled:opacity-60">
                    <span x-show="loading">
                        <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </span>
                    Konfirmasi Import {{ $totalRows }} Baris
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
