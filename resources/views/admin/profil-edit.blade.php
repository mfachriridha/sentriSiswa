@extends('layouts.app')

@section('title', 'Edit Profil Admin')

@section('content')
<div class="rounded-xl border border-gray-200 bg-white p-6">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Edit Profil Admin</h1>

    <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data" class="space-y-6"
          x-data="{
              loading: false,
              photoPreview: @js($admin->photo ? asset('storage/'.$admin->photo) : ''),
              onPhotoChange(event) {
                  const file = event.target.files[0];
                  if (! file || ! file.type.startsWith('image/')) {
                      return;
                  }
                  this.photoPreview = URL.createObjectURL(file);
              },
          }"
          @submit="loading = true">
        @csrf
        @method('PUT')

        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
            <template x-if="photoPreview">
                <img :src="photoPreview" alt="{{ $admin->name }}" class="h-24 w-24 rounded-full border-4 border-gray-100 object-cover">
            </template>
            <template x-if="! photoPreview">
                <div class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-gray-100 bg-primary/10 text-2xl font-bold text-primary">
                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                </div>
            </template>
            <div>
                <label for="photo" class="inline-flex cursor-pointer items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Ubah Foto
                </label>
                <input id="photo" type="file" name="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="hidden" @change="onPhotoChange">
                <p class="mt-1.5 text-xs text-gray-500">JPG/PNG maksimal 2 MB.</p>
                @error('photo')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $admin->email) }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="whatsapp_number" class="block text-sm font-medium text-gray-700">Nomor WhatsApp Bantuan</label>
                <input id="whatsapp_number" type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $admin->whatsapp_number) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20"
                       placeholder="628xxxxxxxxxx">
                @error('whatsapp_number')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru <span class="text-sm font-normal text-gray-400">(kosongkan jika tidak diubah)</span></label>
                <input id="password" type="password" name="password"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                @error('password')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" :disabled="loading"
                    class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark disabled:opacity-60">
                Simpan
            </button>
            <a href="{{ route('admin.profil') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
