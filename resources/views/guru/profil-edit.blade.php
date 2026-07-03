@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
@php
    $profile = $teacher->profilGuru;
    $teacherScope = match ($teacher->peran) {
        'wali_kelas' => $teacher->kelasWali?->nama,
        'bk' => $profile?->tingkat ? 'Tingkat '.$profile->tingkat : null,
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
              photoPreview: @js($profile?->foto ? asset('storage/'.$profile->foto) : ''),
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
                    <img :src="photoPreview" alt="{{ $teacher->nama }}"
                         class="h-24 w-24 rounded-full border-4 border-gray-100 object-cover">
                </template>
                <template x-if="!photoPreview">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-gray-100 bg-primary/10 text-2xl font-bold text-primary">
                        {{ strtoupper(substr($teacher->nama, 0, 1)) }}
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

                    @if ($profile?->foto)
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

        {{-- Input lain (nama, email, hp, password) --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                <input id="name" type="text" name="nama" value="{{ old('nama', $teacher->nama) }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('nama')
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
                <input id="phone" type="text" name="telepon" value="{{ old('telepon', $profile?->telepon) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('telepon')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Google account link --}}
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-700">Google</p>
                    @if ($teacher->id_google)
                        <p class="text-xs text-green-600 font-medium">Terhubung</p>
                    @else
                        <p class="text-xs text-gray-400">Belum terhubung</p>
                    @endif
                </div>
            </div>
            @if (! $teacher->id_google)
                <a href="{{ route('google.link') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Hubungkan
                </a>
            @else
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700">
                        ✓ Aktif
                    </span>
                    <form method="POST" action="{{ route('google.unlink') }}"
                          onsubmit="return confirm('Putuskan koneksi akun Google?');">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Putuskan
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-gray-100">
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
            <a href="{{ route(auth()->user()->profilRouteName('ganti-sandi')) }}"
               class="ml-auto inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Ganti Kata Sandi
            </a>
        </div>
    </form>
</div>

@endsection
