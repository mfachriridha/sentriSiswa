@extends('layouts.app')

@section('title', 'Tambah Guru')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.teachers.index') }}"
       class="inline-flex items-center gap-2 text-base text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-8">
    <h1 class="mb-8 text-2xl font-bold text-gray-900">Tambah Guru</h1>

    <form method="POST" action="{{ route('admin.teachers.store') }}" class="space-y-6"
          x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="name" class="block text-base font-medium text-gray-700">Nama</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                              transition-colors">
                @error('name')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-base font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                              transition-colors">
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nip" class="block text-base font-medium text-gray-700">NIP</label>
                <input id="nip" type="text" name="nip" value="{{ old('nip') }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                              transition-colors">
                @error('nip')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ type: '{{ old('teacher_type') }}' }">
                <div>
                    <label for="teacher_type" class="block text-base font-medium text-gray-700">Tipe</label>
                    <select id="teacher_type" name="teacher_type" required x-model="type"
                            class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                                   focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                        <option value="">Pilih tipe</option>
                        <option value="homeroom">Wali Kelas</option>
                        <option value="counselor">BK</option>
                    </select>
                    @error('teacher_type')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="type === 'counselor'" x-cloak class="mt-6">
                    <label for="grade" class="block text-base font-medium text-gray-700">Tingkatan</label>
                    <select id="grade" name="grade"
                            class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                                   focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                        <option value="">Pilih tingkatan</option>
                        <option value="10" {{ old('grade') === '10' ? 'selected' : '' }}>10</option>
                        <option value="11" {{ old('grade') === '11' ? 'selected' : '' }}>11</option>
                        <option value="12" {{ old('grade') === '12' ? 'selected' : '' }}>12</option>
                    </select>
                    @error('grade')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="phone" class="block text-base font-medium text-gray-700">Telepon</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                              transition-colors">
                @error('phone')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-base font-medium text-gray-700">Kata Sandi</label>
                <input id="password" type="password" name="password" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                              transition-colors">
                @error('password')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
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
            <a href="{{ route('admin.teachers.index') }}"
               class="rounded-lg border border-gray-300 px-6 py-3 text-base font-medium text-gray-700
                      hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
