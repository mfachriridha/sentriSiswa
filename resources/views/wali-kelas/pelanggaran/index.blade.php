@extends('layouts.app')

@section('title', 'Riwayat Pelanggaran')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Riwayat Pelanggaran</h1>
    <p class="mt-1 text-sm text-gray-500">Riwayat pelanggaran siswa kelas {{ $class->nama }}</p>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    {{-- Filters --}}
    <form method="GET" action="{{ route('wali-kelas.pelanggaran') }}" class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div>
            <label class="block text-sm font-medium text-gray-700">Siswa</label>
            <select name="profil_siswa_id" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                <option value="">Semua Siswa</option>
                @foreach($students as $student)
                    <option value="{{ $student->nisn }}" {{ request('profil_siswa_id') == $student->nisn ? 'selected' : '' }}>
                        {{ $student->pengguna->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Kategori</label>
            <select name="kategori" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                <option value="">Semua Kategori</option>
                <option value="ringan" {{ request('kategori') === 'ringan' ? 'selected' : '' }}>Ringan</option>
                <option value="sedang" {{ request('kategori') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                <option value="berat" {{ request('kategori') === 'berat' ? 'selected' : '' }}>Berat</option>
                <option value="sangat_berat" {{ request('kategori') === 'sangat_berat' ? 'selected' : '' }}>Sangat Berat</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
            <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}"
                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
            <input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}"
                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
        </div>

        <div class="flex items-end">
            <button type="submit" class="w-full rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                Filter
            </button>
        </div>
    </form>

    @error('date_from')
        <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error('date_to')
        <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
    @enderror

    {{-- Table --}}
    @if($violations->isEmpty())
        <div class="py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-4 text-sm font-medium text-gray-900">Tidak ada data</h3>
            <p class="mt-1 text-sm text-gray-500">Belum ada riwayat pelanggaran untuk kelas ini.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Siswa</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Pelanggaran</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Kategori</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Poin</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Catatan</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($violations as $violation)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                {{ $violation->tanggal_pelanggaran->translatedFormat('d F Y') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                                {{ $violation->profilSiswa->pengguna->nama }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                {{ $violation->nama_pelanggaran }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">
                                @php
                                    $categoryConfig = [
                                        'light' => ['bg-green-100 text-green-800', 'Ringan'],
                                        'medium' => ['bg-yellow-100 text-yellow-800', 'Sedang'],
                                        'heavy' => ['bg-orange-100 text-orange-800', 'Berat'],
                                        'severe' => ['bg-red-100 text-red-800', 'Sangat Berat'],
                                    ];
                                    [$badgeClass, $categoryLabel] = $categoryConfig[$violation->kategori_pelanggaran] ?? ['bg-gray-100 text-gray-800', $violation->kategori_pelanggaran];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                    {{ $categoryLabel }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-red-600">
                                -{{ $violation->pengurangan_poin }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $violation->catatan ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                {{ $violation->dicatatOleh?->nama ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <x-pagination :paginator="$violations" />
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const startInput = document.getElementById('date_from');
        const endInput = document.getElementById('date_to');
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
