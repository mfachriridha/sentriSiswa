@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="mb-6">
    <a href="{{ route('siswa.profil') }}"
       class="inline-flex items-center gap-2 text-base text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-8">
    <h1 class="mb-8 text-2xl font-bold text-gray-900">Edit Profil</h1>

    {{-- Photo section (separate forms, not nested) --}}
    <div class="flex items-center gap-6 mb-8">
        @if ($student->studentProfile?->photo)
            <img src="{{ asset('storage/'.$student->studentProfile->photo) }}" alt="{{ $student->name }}"
                 class="h-20 w-20 rounded-full object-cover border-2 border-gray-200">
        @else
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-2xl font-bold text-primary">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
        @endif
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('siswa.profil.photo') }}" enctype="multipart/form-data">
                @csrf
                <label for="photo" class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Ubah Foto
                </label>
                <input id="photo" type="file" name="photo" accept="image/*" class="hidden" onchange="this.closest('form').submit()">
            </form>
            @if ($student->studentProfile?->photo)
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
                <label class="block text-base font-medium text-gray-500">Nama</label>
                <p class="mt-1.5 text-base text-gray-900">{{ $student->name }}</p>
            </div>

            <div>
                <label for="email" class="block text-base font-medium text-gray-700">Email <span class="text-sm font-normal text-gray-400">(opsional)</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $student->email) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-base font-medium text-gray-500">NISN</label>
                <p class="mt-1.5 text-base text-gray-900">{{ $student->studentProfile?->nisn ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-base font-medium text-gray-500">NIS</label>
                <p class="mt-1.5 text-base text-gray-900">{{ $student->studentProfile?->nis ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-base font-medium text-gray-500">Kelas</label>
                <p class="mt-1.5 text-base text-gray-900">{{ $student->studentProfile?->class?->name ?? '-' }}</p>
            </div>

            <div>
                <label for="phone" class="block text-base font-medium text-gray-700">Telepon</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $student->studentProfile?->phone) }}"
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
                                 placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('address', $student->studentProfile?->address) }}</textarea>
                @error('address')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-base font-semibold text-white shadow-sm
                           hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
                Simpan
            </button>
            <a href="{{ route('siswa.profil') }}"
               class="rounded-lg border border-gray-300 px-6 py-3 text-base font-medium text-gray-700
                      hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection