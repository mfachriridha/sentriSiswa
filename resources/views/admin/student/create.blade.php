@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.siswa.index') }}"
       class="inline-flex items-center gap-2 text-base text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-8">
    <h1 class="mb-8 text-2xl font-bold text-gray-900">Tambah Siswa</h1>

    <form method="POST" action="{{ route('admin.siswa.store') }}" class="space-y-6"
          x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="name" class="block text-base font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('name')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-base font-medium text-gray-700">Email <span class="text-sm font-normal text-gray-400">(opsional)</span></label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nisn" class="block text-base font-medium text-gray-700">NISN <span class="text-red-500">*</span></label>
                <input id="nisn" type="text" name="nisn" value="{{ old('nisn') }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('nisn')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nis" class="block text-base font-medium text-gray-700">NIS <span class="text-red-500">*</span></label>
                <input id="nis" type="text" name="nis" value="{{ old('nis') }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('nis')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="class_id" class="block text-base font-medium text-gray-700">Kelas</label>
                <select id="class_id" name="class_id"
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                               focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih kelas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} (Tingkat {{ $class->grade }})
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-base font-medium text-gray-700">Telepon</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('phone')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="address" class="block text-base font-medium text-gray-700">Alamat</label>
                <textarea id="address" name="address" rows="3"
                          class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                                 placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-base font-medium text-gray-700">Kata Sandi <span class="text-sm font-normal text-gray-400">(opsional)</span></label>
                <input id="password" type="password" name="password"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('password')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-base font-semibold text-white shadow-sm
                           hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                           transition-colors disabled:opacity-60">
                <span x-show="loading">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                Simpan
            </button>
            <a href="{{ route('admin.siswa.index') }}"
               class="rounded-lg border border-gray-300 px-6 py-3 text-base font-medium text-gray-700
                      hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
