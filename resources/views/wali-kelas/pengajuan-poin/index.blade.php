@extends('layouts.app')

@section('title', 'Pengajuan Poin')

@section('content')
<div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <x-page-header title="Pengajuan Poin" description="Ajukan penambahan poin untuk siswa di kelas binaan Anda." />
    <a href="{{ route('wali-kelas.pengajuan-poin.create') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">Buat Pengajuan</a>
</div>

<x-alert type="success" :message="session('success')" />

<form method="GET" action="{{ route('wali-kelas.pengajuan-poin.index') }}" class="mb-4 flex flex-wrap gap-2 rounded-xl border border-gray-200 bg-white p-4">
    <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <option value="">Semua Status</option>
        @foreach ($statusLabels as $value => $label)
            <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Filter</button>
</form>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Siswa</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Jumlah Poin</th>
                    <th class="px-4 py-3">Sisa Poin</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($pengajuanPoin as $pengajuan)
                    <tr>
                        <td class="px-4 py-3">{{ $pengajuan->dibuat_pada->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-3">{{ $pengajuan->profilSiswa?->pengguna?->nama ?? '-' }}<div class="text-xs text-gray-500">{{ $pengajuan->profilSiswa?->kelas?->nama ?? '-' }}</div></td>
                        <td class="px-4 py-3">
                            @if($pengajuan->kategori)
                                <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 border border-blue-200">
                                    🏆 {{ $pengajuan->kategori->nama }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-900">{{ $pengajuan->alasan }}</td>
                        <td class="px-4 py-3 font-semibold text-green-600">{{ $pengajuan->jumlah_poin !== null ? '+'.$pengajuan->jumlah_poin : '-' }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $pengajuan->profilSiswa?->poin ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $statusLabels[$pengajuan->status] ?? $pengajuan->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-16 text-center text-sm text-gray-500">Belum ada pengajuan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($pengajuanPoin->hasPages())
        <div class="border-t border-gray-200 px-4 py-3"><x-pagination :paginator="$pengajuanPoin" /></div>
    @endif
</div>
@endsection
