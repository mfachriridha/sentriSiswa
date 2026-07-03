@extends('layouts.app')

@section('title', 'Catat Pelanggaran Siswa')

@section('content')
<div class="mb-6">
    <a href="{{ route('kesiswaan.pelanggaran-siswa.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Catat Pelanggaran Siswa</h1>

    <form method="POST" action="{{ route('kesiswaan.pelanggaran-siswa.store') }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="profil_siswa_id" class="block text-sm font-medium text-gray-700">Siswa <span class="text-red-500">*</span></label>
                <select id="profil_siswa_id" name="profil_siswa_id" required
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih siswa</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->nisn }}" {{ (string) old('profil_siswa_id', request('profil_siswa_id')) === (string) $student->nisn ? 'selected' : '' }}>
                            {{ $student->pengguna?->nama }} - {{ $student->kelas?->nama ?? 'Tanpa kelas' }} - NISN {{ $student->nisn ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('profil_siswa_id')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tanggal_pelanggaran" class="block text-sm font-medium text-gray-700">Tanggal Pelanggaran <span class="text-red-500">*</span></label>
                <input id="tanggal_pelanggaran" type="date" name="tanggal_pelanggaran" value="{{ old('tanggal_pelanggaran', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('tanggal_pelanggaran')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="jenis_pelanggaran_id" class="block text-sm font-medium text-gray-700">Jenis Pelanggaran <span class="text-red-500">*</span></label>
                <select id="jenis_pelanggaran_id" name="jenis_pelanggaran_id" required
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih jenis pelanggaran</option>
                    @foreach ($violationTypes->groupBy('kategori') as $category => $groupedViolationTypes)
                        <optgroup label="{{ $categoryLabels[$category] ?? 'Kategori' }}">
                            @foreach ($groupedViolationTypes as $violationType)
                                <option value="{{ $violationType->id }}" {{ (string) old('jenis_pelanggaran_id') === (string) $violationType->id ? 'selected' : '' }}>
                                    {{ $violationType->nama }} ({{ $violationType->pengurangan_poin }} poin)
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('jenis_pelanggaran_id')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan <span class="text-sm font-normal text-gray-400">(opsional)</span></label>
                <textarea id="catatan" name="catatan" rows="4"
                          class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors disabled:opacity-60">
                <span x-cloak x-show="loading">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                Simpan
            </button>
            <a href="{{ route('kesiswaan.pelanggaran-siswa.index') }}"
               class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
