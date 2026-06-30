@extends('layouts.app')

@section('title', 'Edit Profil Admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.profil') }}"
       class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors uppercase tracking-wider">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Profil
    </a>
</div>

<div class="rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-sm">
    <h1 class="mb-8 text-2xl font-extrabold text-slate-900 tracking-tight">Edit Profil Admin</h1>

    <div class="mb-8 rounded-xl border border-blue-100 bg-blue-50/50 p-5 flex gap-4">
        <div class="rounded-lg bg-blue-500/10 p-2 text-blue-600 h-max flex-shrink-0">
            <svg class="h-5 w-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-blue-800 uppercase tracking-wider">Informasi Keamanan</p>
            <p class="mt-1 text-xs text-blue-600 font-medium">Mengubah alamat email atau kata sandi memerlukan verifikasi kode OTP. Jika Anda mengubah email, kode akan dikirim ke email baru yang Anda masukkan di formulir.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data" class="space-y-8"
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

        <!-- Profile Photo Row -->
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
            <template x-if="photoPreview">
                <img :src="photoPreview" alt="{{ $admin->name }}" class="h-24 w-24 rounded-full border-4 border-slate-100 object-cover shadow-md">
            </template>
            <template x-if="! photoPreview">
                <div class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-slate-100 bg-primary/10 text-3xl font-extrabold text-primary shadow-inner">
                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                </div>
            </template>
            <div>
                <label for="photo" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-all duration-300 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Pilih Foto Profil Baru
                </label>
                <input id="photo" type="file" name="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="hidden" @change="onPhotoChange">
                <p class="mt-2 text-[10px] text-slate-400 font-medium">Format: JPG, JPEG, PNG (Maksimal 2 MB)</p>
                @error('photo')
                    <p class="mt-1.5 text-xs text-red-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Form Fields Grid -->
        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Email <span class="text-red-500">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $admin->email) }}" required
                       class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 shadow-inner focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="whatsapp_number" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor WhatsApp Bantuan</label>
                <input id="whatsapp_number" type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $admin->whatsapp_number) }}"
                       class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 shadow-inner focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300"
                       placeholder="Contoh: 628xxxxxxxxxx">
                @error('whatsapp_number')
                    <p class="mt-1.5 text-xs text-red-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Kata Sandi Baru <span class="text-[10px] font-medium text-slate-400 lowercase">(kosongkan jika tidak diubah)</span></label>
                <input id="password" type="password" name="password"
                       class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 shadow-inner focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300"
                       placeholder="Minimal 8 karakter (kombinasi huruf & angka)">
                @error('password')
                    <p class="mt-1.5 text-xs text-red-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
            <button type="submit" :disabled="loading"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-xs font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-[0.98] transition-all duration-300 cursor-pointer disabled:opacity-60">
                <span x-cloak x-show="loading">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.profil') }}"
               class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 active:scale-[0.98] transition-all duration-300">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
