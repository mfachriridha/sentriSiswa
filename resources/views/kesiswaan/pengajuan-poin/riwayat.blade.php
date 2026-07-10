@extends('layouts.app')

@section('title', 'Riwayat Pengajuan Poin')

@section('content')
<div x-data="{ detailAlasan: '', detailPenolakan: '', detailNama: '' }">
    <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Pengajuan Poin</h1>
            <p class="mt-2 text-sm text-gray-500">Semua pengajuan penambahan poin yang sudah diproses maupun masih menunggu.</p>
        </div>
        <a href="{{ route('kesiswaan.pengajuan-poin.persetujuan') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
            Antrean Persetujuan
        </a>
    </div>

    <form method="GET" action="{{ route('kesiswaan.pengajuan-poin.riwayat') }}" class="mb-4 flex flex-wrap items-center gap-2 rounded-xl border border-gray-200 bg-white p-4">
        <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            @foreach ($statusLabels as $value => $label)
                <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">Filter</button>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-gray-600 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-gray-600 font-semibold">Siswa</th>
                        <th class="px-4 py-3 text-gray-600 font-semibold">Kelas</th>
                        <th class="px-4 py-3 text-gray-600 font-semibold">Jumlah Poin</th>
                        <th class="px-4 py-3 text-gray-600 font-semibold">Status</th>
                        <th class="px-4 py-3 text-gray-600 font-semibold">Diajukan Oleh</th>
                        <th class="px-4 py-3 text-gray-600 font-semibold">Diproses Oleh</th>
                        <th class="px-4 py-3 text-gray-600 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pengajuanPoin as $pengajuan)
                        @php
                            $badge = match ($pengajuan->status) {
                                'approved' => 'bg-green-50 text-green-700',
                                'rejected' => 'bg-red-50 text-red-700',
                                default => 'bg-amber-50 text-amber-700',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $pengajuan->dibuat_pada->translatedFormat('d F Y') }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $pengajuan->profilSiswa?->pengguna?->nama ?? '-' }}</p>
                                <p class="mt-1 text-xs text-gray-500">NIS: {{ $pengajuan->profilSiswa?->nis ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $pengajuan->profilSiswa?->kelas?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold text-green-600">{{ $pengajuan->jumlah_poin !== null ? '+'.$pengajuan->jumlah_poin : '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $badge }}">
                                    {{ $statusLabels[$pengajuan->status] ?? $pengajuan->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $pengajuan->diajukanOleh?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $pengajuan->disetujuiOleh?->nama ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        @click="detailAlasan = @js($pengajuan->alasan); detailPenolakan = @js($pengajuan->alasan_penolakan); detailNama = @js($pengajuan->profilSiswa?->pengguna?->nama ?? 'siswa ini'); $dispatch('open-modal', 'detail-pengajuan-poin')"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-sm text-gray-500">Belum ada riwayat pengajuan poin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengajuanPoin->hasPages())
            <div class="border-t border-gray-200 px-4 py-3">
                <x-pagination :paginator="$pengajuanPoin" />
            </div>
        @endif
    </div>

    <x-modal name="detail-pengajuan-poin" title="Detail Pengajuan Poin">
        <p class="text-sm font-medium text-gray-900" x-text="detailNama"></p>
        <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Alasan Pengajuan</p>
        <p class="mt-1 text-sm text-gray-700 whitespace-pre-wrap" x-text="detailAlasan"></p>
        <template x-if="detailPenolakan">
            <div>
                <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-red-500">Alasan Penolakan</p>
                <p class="mt-1 text-sm text-red-700 whitespace-pre-wrap" x-text="detailPenolakan"></p>
            </div>
        </template>
        <div class="mt-5 flex justify-end">
            <button type="button" @click="$dispatch('close-modal')"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Tutup
            </button>
        </div>
    </x-modal>
</div>
@endsection
