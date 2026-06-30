@extends('layouts.app')

@section('title', 'Edit Jenis Pelanggaran')

@section('content')
<div class="mb-6">
    <a href="{{ route('kesiswaan.jenis-pelanggaran.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Edit Jenis Pelanggaran</h1>

    <div class="mb-6 rounded-lg border border-gray-100 bg-gray-50 p-5">
        <p class="text-sm font-semibold text-gray-700">Rentang poin kategori</p>
        <div class="mt-3 grid grid-cols-1 gap-3 text-sm text-gray-600 md:grid-cols-2">
            @foreach ($categoryLabels as $category => $label)
                <p>{{ $label }}: {{ $categoryRanges[$category][0] }}-{{ $categoryRanges[$category][1] }} poin</p>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('kesiswaan.jenis-pelanggaran.update', $violationType) }}" class="space-y-6"
          x-data="{ loading: false }" @submit="loading = true">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Pelanggaran <span class="text-red-500">*</span></label>
                <input id="nama" type="text" name="nama" value="{{ old('nama', $violationType->nama) }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('nama')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                <select id="kategori" name="kategori" required
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                               focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih kategori</option>
                    @foreach ($categoryLabels as $category => $label)
                        <option value="{{ $category }}" {{ old('kategori', $violationType->kategori) === $category ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('kategori')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="pengurangan_poin" class="block text-sm font-medium text-gray-700">Poin Pelanggaran <span class="text-red-500">*</span></label>
                <input id="pengurangan_poin" type="number" name="pengurangan_poin" min="5" max="100" value="{{ old('pengurangan_poin', $violationType->pengurangan_poin) }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('pengurangan_poin')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan <span class="text-sm font-normal text-gray-400">(opsional)</span></label>
                <textarea id="keterangan" name="keterangan" rows="3"
                          class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                 placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('keterangan', $violationType->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <input type="hidden" name="aktif" value="0">
                <label class="inline-flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-700">
                    <input type="checkbox" name="aktif" value="1" @checked((string) old('aktif', $violationType->aktif ? '1' : '0') === '1')
                           class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary/30">
                    Aktif digunakan
                </label>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                           hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors disabled:opacity-60">
                <span x-cloak x-show="loading">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                Perbarui
            </button>
            <a href="{{ route('kesiswaan.jenis-pelanggaran.index') }}"
               class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
