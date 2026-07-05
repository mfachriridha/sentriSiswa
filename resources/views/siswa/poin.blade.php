@extends('layouts.app')

@section('title', 'Poin Saya')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Poin Saya</h1>
    <p class="mt-2 text-sm text-gray-500">Pantau sisa poin disiplin dan riwayat pelanggaran Anda.</p>
</div>

{{-- Kartu Poin --}}
@php
    $levelClass = $totalPoints > 75 ? 'text-green-600 border-green-300 bg-green-50' : ($totalPoints > 50 ? 'text-amber-600 border-amber-300 bg-amber-50' : 'text-red-600 border-red-300 bg-red-50');
    $levelLabel = $totalPoints > 75 ? 'Baik' : ($totalPoints > 50 ? 'Cukup' : 'Perhatian');
    $levelBadgeClass = $totalPoints > 75 ? 'bg-green-100 text-green-700' : ($totalPoints > 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
@endphp

<div class="mb-6 overflow-hidden rounded-xl border-2 {{ $levelClass }}">
    <div class="p-6">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider opacity-75">Sisa Poin Disiplin</p>
                <p class="mt-2 text-5xl font-bold tracking-tight">{{ $totalPoints }}</p>
                <p class="mt-2 text-sm opacity-75">Dari 100 poin · {{ $totalDeductions }} poin terpakai</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold {{ $levelBadgeClass }}">
                    {{ $levelLabel }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Riwayat Pelanggaran --}}
<div class="rounded-xl border border-gray-200 bg-white">
    <div class="border-b border-gray-200 px-4 py-3">
        <h2 class="text-lg font-bold text-gray-900">Riwayat Pelanggaran</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Pelanggaran</th>
                    <th class="hidden px-4 py-3 font-semibold text-gray-600 md:table-cell">Kategori</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Poin</th>
                    <th class="hidden px-4 py-3 font-semibold text-gray-600 lg:table-cell">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($violations as $violation)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-700">
                            {{ Carbon\Carbon::parse($violation->tanggal_pelanggaran)->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $violation->nama_pelanggaran }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">
                            @php
                                $catLabels = ['ringan' => 'Ringan', 'sedang' => 'Sedang', 'berat' => 'Berat', 'sangat_berat' => 'Sangat Berat'];
                                $catBadges = ['ringan' => 'bg-green-50 text-green-700', 'sedang' => 'bg-amber-50 text-amber-700', 'berat' => 'bg-orange-50 text-orange-700', 'sangat_berat' => 'bg-red-50 text-red-700'];
                                $cat = $violation->kategori_pelanggaran;
                            @endphp
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $catBadges[$cat] ?? 'bg-gray-50 text-gray-700' }}">
                                {{ $catLabels[$cat] ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-red-600">-{{ $violation->pengurangan_poin }}</td>
                        <td class="hidden px-4 py-3 text-gray-600 lg:table-cell">{{ $violation->catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-sm text-gray-500">Belum ada catatan pelanggaran untuk Anda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
