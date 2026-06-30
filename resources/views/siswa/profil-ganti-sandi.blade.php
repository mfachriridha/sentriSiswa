@extends('layouts.app')

@section('title', 'Ganti Kata Sandi')

@section('content')
<div class="mb-6">
    <a href="{{ route('siswa.profil.edit') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Edit Profil
    </a>
</div>

<div class="max-w-lg rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Ganti Kata Sandi</h1>
        <p class="mt-1 text-sm text-gray-500">Verifikasi identitas lewat OTP sebelum mengubah kata sandi.</p>
    </div>

    <x-alert type="success" :message="session('success')" />

    <div class="mb-6 rounded-lg border border-gray-100 bg-gray-50 p-4">
        <p class="text-xs font-medium text-gray-500 mb-1">Kode OTP dikirim ke</p>
        <p class="text-sm font-semibold text-gray-900">{{ $maskedEmail ?: '(email belum diset)' }}</p>
        @if (! $maskedEmail)
            <p class="mt-2 text-xs text-amber-600 font-medium">Atur email Anda di halaman Edit Profil terlebih dahulu.</p>
        @endif
    </div>

    <form method="POST" action="{{ route('siswa.profil.ganti-sandi') }}"
          x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div class="flex items-center gap-4">
            <button type="submit" :disabled="loading || !{{ $maskedEmail ? 'true' : 'false' }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                           hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors disabled:opacity-60">
                <span x-cloak x-show="loading">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                Kirim OTP ke Email
            </button>
            <a href="{{ route('siswa.profil.edit') }}"
               class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
