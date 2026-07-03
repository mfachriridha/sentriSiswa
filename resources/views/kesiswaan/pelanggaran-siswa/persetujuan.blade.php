@extends('layouts.app')

@section('title', 'Persetujuan Pelanggaran')

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
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Antrean Persetujuan</h1>
        <p class="mt-2 text-sm text-gray-500">Terima atau tolak pengajuan pelanggaran dari guru BK.</p>
    </div>
    <a href="{{ route('kesiswaan.pelanggaran-siswa.index') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        ← Kembali ke Daftar
    </a>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Tanggal</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Siswa</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Kelas</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Pelanggaran</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Kategori</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Poin</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Diajukan Oleh</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($pending as $violation)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700">{{ $violation->tanggal_pelanggaran?->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $violation->profilSiswa?->pengguna?->nama ?? '-' }}</p>
                            <p class="mt-1 text-xs text-gray-500">NIS: {{ $violation->profilSiswa?->nis ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $violation->profilSiswa?->kelas?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $violation->nama_pelanggaran }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $categoryBadgeClasses[$violation->kategori_pelanggaran] ?? 'bg-gray-50 text-gray-700' }}">
                                {{ $categoryLabels[$violation->kategori_pelanggaran] ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">-{{ $violation->pengurangan_poin }} poin</td>
                        <td class="px-4 py-3 text-gray-500">{{ $violation->dicatatOleh?->nama ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('kesiswaan.pelanggaran-siswa.show', $violation) }}"
                                   class="inline-flex items-center rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">Detail</a>
                                <form method="POST" action="{{ route('kesiswaan.pelanggaran-siswa.approve', $violation) }}">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">
                                        Terima
                                    </button>
                                </form>
                                <button type="button"
                                        onclick="openTolakModal({{ $violation->id }}, '{{ addslashes($violation->profilSiswa?->pengguna?->nama ?? 'siswa ini') }}')"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-sm text-gray-500">
                            Tidak ada pelanggaran yang menunggu persetujuan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tolak --}}
<div id="tolakModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-xl">
        <div class="border-b border-gray-200 px-5 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Tolak Pelanggaran</h3>
            <p id="tolakStudentName" class="mt-1 text-sm text-gray-500"></p>
        </div>
        <form id="tolakForm" method="POST" class="px-5 py-4">
            @csrf
            @method('PUT')
            <label for="alasan_penolakan" class="block text-sm font-medium text-gray-700">
                Alasan Penolakan <span class="text-red-500">*</span>
            </label>
            <textarea id="alasan_penolakan" name="alasan_penolakan" rows="3" required
                      placeholder="Tuliskan alasan penolakan..."
                      class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20"></textarea>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" onclick="closeTolakModal()"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                    Tolak Pelanggaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const tolakRoutes = {
        @foreach ($pending as $violation)
        {{ $violation->id }}: "{{ route('kesiswaan.pelanggaran-siswa.reject', $violation) }}",
        @endforeach
    };

    function openTolakModal(id, studentName) {
        document.getElementById('tolakForm').action = tolakRoutes[id];
        document.getElementById('tolakStudentName').textContent = studentName;
        document.getElementById('alasan_penolakan').value = '';
        const modal = document.getElementById('tolakModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeTolakModal() {
        const modal = document.getElementById('tolakModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('tolakModal').addEventListener('click', function (e) {
        if (e.target === this) closeTolakModal();
    });
</script>
@endpush
