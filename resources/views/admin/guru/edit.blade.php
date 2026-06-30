@extends('layouts.app')

@section('title', 'Edit Guru')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.guru.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Edit Guru</h1>

    <form method="POST" action="{{ route('admin.guru.update', $teacher) }}" class="space-y-6"
          x-data="{ loading: false }" @submit="loading = true">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                <input id="name" type="text" name="nama" value="{{ old('nama', $teacher->nama) }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                              transition-colors">
                @error('nama')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-sm font-normal text-gray-400">(opsional)</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $teacher->email) }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                              transition-colors">
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
                <input id="nip" type="text" name="nip" value="{{ old('nip', $teacher->profilGuru?->nip) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                              transition-colors">
                @error('nip')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ role: '{{ old('peran', $teacher->peran) }}' }">
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role <span class="text-red-500">*</span></label>
                    <select id="role" name="peran" required x-model="role"
                            class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                   focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                        <option value="">Pilih role</option>
                        <option value="wali_kelas">Wali Kelas</option>
                        <option value="bk">BK</option>
                        <option value="kesiswaan">Kesiswaan</option>
                    </select>
                    @error('peran')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="role === 'bk'" x-cloak class="mt-6">
                    <label for="grade" class="block text-sm font-medium text-gray-700">Tingkatan</label>
                    <select id="grade" name="tingkat"
                            class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                   focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                        <option value="">Pilih tingkatan</option>
                        <option value="10" {{ old('tingkat', $teacher->profilGuru?->tingkat) === '10' ? 'selected' : '' }}>10</option>
                        <option value="11" {{ old('tingkat', $teacher->profilGuru?->tingkat) === '11' ? 'selected' : '' }}>11</option>
                        <option value="12" {{ old('tingkat', $teacher->profilGuru?->tingkat) === '12' ? 'selected' : '' }}>12</option>
                    </select>
                    @error('tingkat')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Telepon</label>
                <input id="phone" type="text" name="telepon" value="{{ old('telepon', $teacher->profilGuru?->telepon) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                              transition-colors">
                @error('telepon')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi <span class="text-sm font-normal text-gray-400">(kosongkan jika tidak diubah)</span></label>
                <input id="password" type="password" name="password"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                              transition-colors">
                @error('password')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                           hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                           transition-colors disabled:opacity-60">
                <span x-cloak x-show="loading">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                Perbarui
            </button>
            <a href="{{ route('admin.guru.index') }}"
               class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700
                      hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
