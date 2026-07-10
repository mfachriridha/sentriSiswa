@extends('layouts.app')

@section('title', $title)

@section('content')
<x-page-header :title="$title" description="Rekap pelanggaran siswa dengan filter dan export laporan." />

<div class="mb-4 rounded-xl border border-gray-200 bg-white p-4">
    <form method="GET" action="{{ route($routeName.'.index') }}" class="grid gap-3 md:grid-cols-3 xl:grid-cols-6">
        <input id="start_date" type="date" name="mulai" value="{{ $filters['start_date'] ?? '' }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <input id="end_date" type="date" name="selesai" value="{{ $filters['end_date'] ?? '' }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <select name="kelas_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua Kelas</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" {{ ($filters['class_id'] ?? '') == $class->id ? 'selected' : '' }}>{{ $class->nama }}</option>
            @endforeach
        </select>
        <select name="kategori" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua Kategori</option>
            @foreach ($categoryLabels as $value => $label)
                <option value="{{ $value }}" {{ ($filters['kategori'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Terapkan</button>
    </form>
    <div class="mt-3 flex flex-wrap gap-2">
        <a href="{{ route($routeName.'.ekspor-excel', request()->query()) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Export Excel</a>
        <a href="{{ route($routeName.'.ekspor-pdf', request()->query()) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Export PDF</a>
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
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($violations as $violation)
                    <tr>
                        <td class="px-4 py-3">{{ $violation->tanggal_pelanggaran->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-3">{{ $violation->profilSiswa?->pengguna?->nama ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $violation->profilSiswa?->kelas?->nama ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $violation->nama_pelanggaran }}</td>
                        <td class="px-4 py-3 font-semibold text-red-600">-{{ $violation->pengurangan_poin }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-16 text-center text-sm text-gray-500">Tidak ada data laporan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($violations->hasPages())
        <div class="border-t border-gray-200 px-4 py-3">{{ $violations->links() }}</div>
    @endif
</div>

<div class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="border-b border-gray-200 p-4">
        <h2 class="text-base font-semibold text-gray-900">Penambahan Poin (Disetujui)</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">Tanggal Disetujui</th>
                    <th class="px-4 py-3">Siswa</th>
                    <th class="px-4 py-3">Kelas</th>
                    <th class="px-4 py-3">Alasan</th>
                    <th class="px-4 py-3">Poin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($pengajuanPoin as $pengajuan)
                    <tr>
                        <td class="px-4 py-3">{{ $pengajuan->disetujui_pada?->translatedFormat('d F Y') ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $pengajuan->profilSiswa?->pengguna?->nama ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $pengajuan->profilSiswa?->kelas?->nama ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $pengajuan->alasan }}</td>
                        <td class="px-4 py-3 font-semibold text-green-600">+{{ $pengajuan->jumlah_poin }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-16 text-center text-sm text-gray-500">Tidak ada penambahan poin disetujui pada rentang ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($pengajuanPoin->hasPages())
        <div class="border-t border-gray-200 px-4 py-3">{{ $pengajuanPoin->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');
        if (!startInput || !endInput) return;

        function syncBounds() {
            endInput.min = startInput.value || '';
            startInput.max = endInput.value || '';
        }

        startInput.addEventListener('change', syncBounds);
        endInput.addEventListener('change', syncBounds);
        syncBounds();
    })();
</script>
@endpush
