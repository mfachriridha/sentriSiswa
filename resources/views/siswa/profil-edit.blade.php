@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="mb-6">
    <a href="{{ route('siswa.profil') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Edit Profil</h1>

    <div class="mb-6 flex items-start gap-6"
         x-data="{
             photoPreview: @js($student->profilSiswa?->foto ? asset('storage/'.$student->profilSiswa->foto) : ''),
             selected: false,
             onPhotoChange(event) {
                 const file = event.target.files[0];
                 if (!file) {
                     return;
                 }
                 if (!file.type.startsWith('image/')) {
                     event.target.value = '';
                     this.selected = false;
                     return;
                 }
                 this.selected = true;
                 this.photoPreview = URL.createObjectURL(file);
             },
         }">
        <template x-if="photoPreview">
            <img :src="photoPreview" alt="{{ $student->nama }}"
                 class="h-20 w-20 rounded-full border-2 border-gray-200 object-cover">
        </template>
        <template x-if="!photoPreview">
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-2xl font-bold text-primary">
                {{ strtoupper(substr($student->nama, 0, 1)) }}
            </div>
        </template>

        <div class="flex flex-wrap items-center gap-2">
            <form method="POST" action="{{ route('siswa.profil.photo') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                @csrf
                <label for="photo" class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Pilih Foto
                </label>
                <input id="photo" type="file" name="photo" accept="image/*" class="hidden" @change="onPhotoChange">
                <button type="submit" x-show="selected" x-cloak
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-primary-dark">
                    Upload Foto
                </button>
            </form>
            @if ($student->profilSiswa?->foto)
                <form method="POST" action="{{ route('siswa.profil.photo.delete') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 transition-colors"
                            onclick="return confirm('Hapus foto?')">
                        Hapus Foto
                    </button>
                </form>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('siswa.profil.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-500">Nama</label>
                <p class="mt-1.5 text-sm text-gray-900">{{ $student->nama }}</p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-sm font-normal text-gray-400">(opsional)</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $student->email) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500">NISN</label>
                <p class="mt-1.5 text-sm text-gray-900">{{ $student->profilSiswa?->nisn ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500">NIS</label>
                <p class="mt-1.5 text-sm text-gray-900">{{ $student->profilSiswa?->nis ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500">Kelas</label>
                <p class="mt-1.5 text-sm text-gray-900">{{ $student->profilSiswa?->kelas?->nama ?? '-' }}</p>
            </div>

            <div>
                <label for="telepon" class="block text-sm font-medium text-gray-700">Telepon</label>
                <input id="telepon" type="text" name="telepon" value="{{ old('telepon', $student->profilSiswa?->telepon) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('telepon')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3"
                          class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                 placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('alamat', $student->profilSiswa?->alamat) }}</textarea>
                @error('alamat')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-gray-100">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                           hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
                Simpan
            </button>
            <a href="{{ route('siswa.profil') }}"
               class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700
                      hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <a href="{{ route('siswa.profil.ganti-sandi') }}"
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
