@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
@php
    $profile = $teacher->teacherProfile;
    $teacherTypeLabels = [
        'homeroom' => 'Wali Kelas',
        'counselor' => 'BK',
        'student_affairs' => 'Kesiswaan',
    ];

    $teacherScope = match ($profile?->teacher_type) {
        'homeroom' => $teacher->homeroomClass?->name,
        'counselor' => $profile?->grade ? 'Tingkat '.$profile->grade : null,
        'student_affairs' => 'Seluruh sekolah',
        default => null,
    };
@endphp

<div class="mb-6">
    <a href="{{ route('guru.profil') }}"
       class="inline-flex items-center gap-2 text-base text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-8">
    <h1 class="mb-8 text-2xl font-bold text-gray-900">Edit Profil</h1>

    <div class="mb-8 grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Nama</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $teacher->name }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NIP</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $profile?->nip ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Tipe Guru</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $teacherTypeLabels[$profile?->teacher_type] ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Tingkat/Kelas Binaan</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $teacherScope ?? '-' }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('guru.profil.update') }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="email" class="block text-base font-medium text-gray-700">Email <span class="text-sm font-normal text-gray-400">(opsional)</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $teacher->email) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-base font-medium text-gray-700">Nomor HP</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $profile?->phone) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('phone')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="password" class="block text-base font-medium text-gray-700">Kata Sandi Baru <span class="text-sm font-normal text-gray-400">(kosongkan jika tidak diubah)</span></label>
                <input id="password" type="password" name="password"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('password')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-base font-semibold text-white shadow-sm
                           hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors disabled:opacity-60">
                <span x-show="loading">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                Simpan
            </button>
            <a href="{{ route('guru.profil') }}"
               class="rounded-lg border border-gray-300 px-6 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
