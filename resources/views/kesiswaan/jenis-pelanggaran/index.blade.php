@extends('layouts.app')

@section('title', 'Jenis Pelanggaran')

@section('content')
@php
    $categoryBadgeClasses = \App\Models\JenisPelanggaran::categoryBadgeClasses();
@endphp

<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Jenis Pelanggaran</h1>
        <p class="mt-2 text-sm text-gray-500">Kelola daftar poin pelanggaran siswa berdasarkan aturan sekolah.</p>
    </div>
    <a href="{{ route('kesiswaan.jenis-pelanggaran.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
              hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Jenis
    </a>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<x-search-filter-form
    :action="route('kesiswaan.jenis-pelanggaran.index')"
    :search="$search"
    placeholder="Cari nama pelanggaran..."
    :filters="[
        ['name' => 'category', 'label' => 'Kategori', 'value' => $filterCategory, 'options' => ['' => 'Semua Kategori'] + $categoryLabels],
        ['name' => 'status', 'label' => 'Status', 'value' => $filterStatus, 'options' => ['' => 'Semua Status', 'active' => 'Aktif', 'inactive' => 'Nonaktif']],
    ]"
    :sort="$sort"
    :direction="$direction"
/>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3"><x-sort-link label="Nama" column="nama" :sort="$sort" :direction="$direction" /></th>
                    <th class="px-4 py-3"><x-sort-link label="Kategori" column="kategori" :sort="$sort" :direction="$direction" /></th>
                    <th class="px-4 py-3"><x-sort-link label="Poin" column="pengurangan_poin" :sort="$sort" :direction="$direction" /></th>
                    <th class="px-4 py-3"><x-sort-link label="Status" column="aktif" :sort="$sort" :direction="$direction" /></th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($violationTypes as $violationType)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $violationType->nama }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $categoryBadgeClasses[$violationType->kategori] ?? 'bg-gray-50 text-gray-700' }}">
                                {{ $categoryLabels[$violationType->kategori] ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $violationType->pengurangan_poin }} poin</td>
                        <td class="px-4 py-3">
                            @if ($violationType->aktif)
                                <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('kesiswaan.jenis-pelanggaran.show', $violationType) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat
                                </a>
                                <a href="{{ route('kesiswaan.jenis-pelanggaran.edit', $violationType) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-primary/30 px-3 py-2 text-sm font-medium text-primary hover:bg-primary/5 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <button type="button"
                                        onclick="window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                                            detail: {
                                                title: 'Hapus Jenis Pelanggaran',
                                                message: 'Yakin ingin menghapus {{ $violationType->nama }}?',
                                                formId: 'delete-violation-type-{{ $violationType->id }}'
                                            }
                                        }))"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                                <form id="delete-violation-type-{{ $violationType->id }}" method="POST" action="{{ route('kesiswaan.jenis-pelanggaran.destroy', $violationType) }}" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-sm text-gray-500">
                            {{ $search || $filterCategory || $filterStatus ? 'Tidak ada jenis pelanggaran yang sesuai dengan filter.' : 'Belum ada data jenis pelanggaran.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($violationTypes->hasPages())
        <div class="border-t border-gray-200 px-4 py-3">
            <x-pagination :paginator="$violationTypes" />
        </div>
    @endif
</div>
@endsection
