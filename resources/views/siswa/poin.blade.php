@extends('layouts.app')

@section('title', 'Poin Saya - SentriSiswa')

@section('page-title', 'Poin Saya')

@section('content')
<!-- Poin Card -->
<div class="card !bg-gradient-to-br !from-blue-50 !to-indigo-50 !border-blue-100 mb-6 anim-up">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-blue-500 uppercase tracking-wide">Sisa Poin</p>
            <p class="stat-value text-blue-900 mt-1">{{ $student->poin }}<span class="text-base font-medium text-blue-400 ml-1">/ 100</span></p>
            <div class="w-full max-w-xs bg-blue-200 rounded-full h-2.5 mt-2">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width:{{ $student->poin }}%"></div>
            </div>
            <p class="text-xs text-blue-600 mt-2">Poin berkurang: <span class="font-bold">{{ 100 - $student->poin }} poin</span></p>
        </div>
        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center">
            <i class="bi bi-star-fill text-3xl text-blue-500"></i>
        </div>
    </div>
</div>

<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-4">Riwayat Pelanggaran</h3>
    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-header">Tanggal</th>
                    <th class="table-header">Pelanggaran</th>
                    <th class="table-header">Kategori</th>
                    <th class="table-header">Poin</th>
                    <th class="table-header">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($violations as $sv)
                <tr>
                    <td class="table-cell text-xs font-semibold">{{ $sv->created_at->translatedFormat('d M Y') }}</td>
                    <td class="table-cell text-xs">{{ $sv->violation->name }}</td>
                    <td class="table-cell"><span class="badge badge-red">{{ $sv->violation->poin }}</span></td>
                    <td class="table-cell text-xs uppercase">{{ str_replace('_', ' ', $sv->violation->category) }}</td>
                    <td class="table-cell text-xs text-muted">{{ $sv->note ?: '—' }}</td>
                </tr>
                @empty
                <tr><td class="table-cell text-center text-muted py-8" colspan="5">Tidak ada riwayat pelanggaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('components.pagination', ['data' => $violations])
    <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700">
        <i class="bi bi-info-circle-fill mr-1"></i> Notifikasi pelanggaran dikirim ke orang tua setiap pekan.
    </div>
</div>
@endsection
