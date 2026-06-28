@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
@php
    $profile = $teacher->teacherProfile;
    $teacherScope = match ($teacher->role) {
        'wali_kelas' => $teacher->homeroomClass?->name,
        'bk' => $profile?->grade ? 'Tingkat '.$profile->grade : null,
        'kesiswaan' => 'Seluruh sekolah',
        default => null,
    };
@endphp

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Edit Profil</h1>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route(auth()->user()->profilRouteName('update')) }}" class="space-y-10" enctype="multipart/form-data"
          x-data="{
              loading: false,
              photoPreview: @js($profile?->photo ? asset('storage/'.$profile->photo) : ''),
              deletePhoto: false,
              onPhotoChange(event) {
                  const file = event.target.files[0];
                  if (!file) {
                      return;
                  }
                  if (!file.type.startsWith('image/')) {
                      event.target.value = '';
                      return;
                  }
                  this.deletePhoto = false;
                  this.photoPreview = URL.createObjectURL(file);
              },
              removePhoto() {
                  this.deletePhoto = true;
                  this.photoPreview = '';
                  document.getElementById('photo').value = '';
              },
          }"
          @submit="loading = true">
        @csrf
        @method('PUT')

        {{-- Foto dengan preview dan tombol hapus --}}
        <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-center sm:gap-10">
            <div class="relative">
                <template x-if="photoPreview">
                    <img :src="photoPreview" alt="{{ $teacher->name }}"
                         class="h-24 w-24 rounded-full border-4 border-gray-100 object-cover">
                </template>
                <template x-if="!photoPreview">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-gray-100 bg-primary/10 text-2xl font-bold text-primary">
                        {{ strtoupper(substr($teacher->name, 0, 1)) }}
                    </div>
                </template>
            </div>

            <div class="flex flex-col gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <label for="photo" class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Ubah Foto
                    </label>
                    <input id="photo" type="file" name="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="hidden" @change="onPhotoChange">

                    @if ($profile?->photo)
                        <button type="button" @click="removePhoto" class="text-sm text-red-600 hover:text-red-800 transition-colors">
                            Hapus Foto
                        </button>
                    @endif
                </div>
                @error('photo')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <input type="hidden" name="delete_photo" :value="deletePhoto ? '1' : '0'">

        {{-- Info singkat guru (read‑only) --}}
        <div class="mb-10 grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">NIP</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $profile?->nip ?? '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Role</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $teacher->roleLabel() }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Tingkat/Kelas Binaan</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $teacherScope ?? '-' }}</p>
            </div>
        </div>

        <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 mb-6">
            <p class="text-sm text-blue-700">
                💡 <strong>Informasi Keamanan:</strong> Mengubah email atau kata sandi memerlukan verifikasi kode OTP yang dikirimkan ke email Anda saat ini.
            </p>
        </div>

        {{-- Input lain (nama, email, hp, password) --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name', $teacher->name) }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('name')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-sm font-normal text-gray-400">(opsional)</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $teacher->email) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Nomor HP</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $profile?->phone) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('phone')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                @if(!$teacher->hasPassword())
                    <label for="password" class="block text-sm font-medium text-gray-700">Tambah Kata Sandi Baru</label>
                @else
                    <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru <span class="text-sm font-normal text-gray-400">(kosongkan jika tidak diubah)</span></label>
                @endif
                <input id="password" type="password" name="password"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('password')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-6 pt-4">
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
            <a href="{{ route(auth()->user()->profilRouteName()) }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection
