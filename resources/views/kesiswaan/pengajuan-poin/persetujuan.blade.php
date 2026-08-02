@extends('layouts.app')

@section('title', 'Persetujuan Pengajuan Poin')

@section('content')
<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Antrean Persetujuan Poin</h1>
        <p class="mt-2 text-sm text-gray-500">Terima atau tolak pengajuan penambahan poin dari wali kelas.</p>
    </div>
    <a href="{{ route('kesiswaan.pengajuan-poin.riwayat') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
        Riwayat
    </a>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

@error('jumlah_poin')
    <x-alert type="error" :message="$message" />
@enderror
@error('alasan_penolakan')
    <x-alert type="error" :message="$message" />
@enderror

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Tanggal</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Siswa</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Kelas</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Sisa Poin</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Kategori</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Keterangan</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Diajukan Oleh</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($pending as $pengajuan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700">{{ $pengajuan->dibuat_pada->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $pengajuan->profilSiswa?->pengguna?->nama ?? '-' }}</p>
                            <p class="mt-1 text-xs text-gray-500">NIS: {{ $pengajuan->profilSiswa?->nis ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $pengajuan->profilSiswa?->kelas?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $pengajuan->profilSiswa?->poin ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($pengajuan->kategori)
                                <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 border border-blue-200">
                                    🏆 {{ $pengajuan->kategori->nama }} (+{{ $pengajuan->jumlah_poin ?? $pengajuan->kategori->poin }} Poin)
                                </span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-900">{{ $pengajuan->alasan }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $pengajuan->diajukanOleh?->nama ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        onclick="openTerimaModal({{ $pengajuan->id }}, '{{ addslashes($pengajuan->profilSiswa?->pengguna?->nama ?? 'siswa ini') }}', '{{ addslashes($pengajuan->kategori?->nama ?? 'Prestasi') }}', {{ $pengajuan->jumlah_poin ?? $pengajuan->kategori?->poin ?? 10 }}, {{ $pengajuan->profilSiswa?->poin ?? 100 }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">
                                    Terima
                                </button>
                                <button type="button"
                                        onclick="openTolakModal({{ $pengajuan->id }}, '{{ addslashes($pengajuan->profilSiswa?->pengguna?->nama ?? 'siswa ini') }}')"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-sm text-gray-500">
                            Tidak ada pengajuan poin yang menunggu persetujuan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Terima --}}
<div id="terimaModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-xl">
        <div class="border-b border-gray-200 px-5 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Terima Pengajuan Poin</h3>
            <p id="terimaStudentName" class="mt-1 text-sm font-medium text-gray-700"></p>
            <p id="terimaCurrentPoints" class="mt-0.5 text-xs font-semibold text-amber-700"></p>
            <p id="terimaCategoryName" class="mt-0.5 text-xs text-blue-600 font-semibold"></p>
        </div>
        <form id="terimaForm" method="POST" class="px-5 py-4">
            @csrf
            @method('PUT')
            <div class="rounded-xl border border-green-200 bg-green-50/80 p-4 text-center">
                <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Tambahan Poin</p>
                <p id="terimaPoinBadge" class="mt-1 text-3xl font-bold text-green-700">+0 Poin</p>
            </div>
            <input type="hidden" id="jumlah_poin" name="jumlah_poin" value="">
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" onclick="closeTerimaModal()"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                    Setujui & Tambah Poin
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tolak --}}
<div id="tolakModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-xl">
        <div class="border-b border-gray-200 px-5 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Tolak Pengajuan Poin</h3>
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
                    Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const terimaRoutes = {
        @foreach ($pending as $pengajuan)
        {{ $pengajuan->id }}: "{{ route('kesiswaan.pengajuan-poin.approve', $pengajuan) }}",
        @endforeach
    };
    const tolakRoutes = {
        @foreach ($pending as $pengajuan)
        {{ $pengajuan->id }}: "{{ route('kesiswaan.pengajuan-poin.reject', $pengajuan) }}",
        @endforeach
    };

    function openTerimaModal(id, studentName, categoryName, pointAmount, currentPoints) {
        document.getElementById('terimaForm').action = terimaRoutes[id];
        document.getElementById('terimaStudentName').textContent = 'Siswa: ' + studentName;
        document.getElementById('terimaCurrentPoints').textContent = 'Sisa Poin Saat Ini: ' + currentPoints + ' Poin';
        document.getElementById('terimaCategoryName').textContent = 'Kategori: 🏆 ' + categoryName;
        document.getElementById('terimaPoinBadge').textContent = '+' + pointAmount + ' Poin';
        document.getElementById('jumlah_poin').value = pointAmount;
        const modal = document.getElementById('terimaModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeTerimaModal() {
        const modal = document.getElementById('terimaModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

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

    document.getElementById('terimaModal').addEventListener('click', function (e) {
        if (e.target === this) closeTerimaModal();
    });

    document.getElementById('tolakModal').addEventListener('click', function (e) {
        if (e.target === this) closeTolakModal();
    });
</script>
@endpush
