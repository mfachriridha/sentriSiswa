@extends('layouts.app')

@section('title', 'Ganti Kata Sandi')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.profil.edit') }}"
       class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors uppercase tracking-wider">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Edit Profil
    </a>
</div>

<div class="max-w-lg rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-sm">
    <div class="mb-6 flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10">
            <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Ganti Kata Sandi</h1>
            <p class="text-xs text-slate-500 font-medium">Verifikasi identitas lewat OTP sebelum mengubah kata sandi</p>
        </div>
    </div>

    <x-alert type="success" :message="session('success')" />

    <div class="mb-6 rounded-xl border border-slate-100 bg-slate-50 p-4">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kode OTP dikirim ke</p>
        <p class="text-sm font-bold text-slate-800">{{ $maskedEmail ?: '(email belum diset)' }}</p>
        @if (! $maskedEmail)
            <p class="mt-2 text-xs text-amber-600 font-medium">Atur email Anda di halaman Edit Profil terlebih dahulu.</p>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.profil.ganti-sandi') }}"
          x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div class="flex items-center gap-4">
            <button type="submit" :disabled="loading || !{{ $maskedEmail ? 'true' : 'false' }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-3 text-xs font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-[0.98] transition-all duration-300 disabled:opacity-60">
                <span x-cloak x-show="loading">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                Kirim OTP ke Email
            </button>
            <a href="{{ route('admin.profil.edit') }}"
               class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 active:scale-[0.98] transition-all duration-300">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
