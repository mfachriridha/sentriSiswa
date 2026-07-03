@extends('layouts.app')

@section('title', 'Buat Pengajuan Poin')

@section('content')
<x-page-header title="Buat Pengajuan Poin" description="Ajukan penambahan poin untuk siswa di kelas binaan Anda. Jumlah poin akan ditentukan oleh kesiswaan saat menyetujui." />

<div class="rounded-xl border border-gray-200 bg-white p-4 lg:p-6">
    <form method="POST" action="{{ route('wali-kelas.pengajuan-poin.store') }}" class="space-y-5">
        @csrf
        <div>
            <label for="profil_siswa_id" class="block text-sm font-medium text-gray-700">Siswa</label>
            <select id="profil_siswa_id" name="profil_siswa_id" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Pilih siswa</option>
                @foreach ($students as $student)
                    <option value="{{ $student->nisn }}" {{ old('profil_siswa_id') == $student->nisn ? 'selected' : '' }}>{{ $student->pengguna?->nama }}</option>
                @endforeach
            </select>
            @error('profil_siswa_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="alasan" class="block text-sm font-medium text-gray-700">Alasan</label>
            <textarea id="alasan" name="alasan" rows="4" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('alasan') }}</textarea>
            @error('alasan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('wali-kelas.pengajuan-poin.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">Batal</a>
            <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">Kirim Pengajuan</button>
        </div>
    </form>
</div>
@endsection
