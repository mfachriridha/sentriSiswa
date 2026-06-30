@extends('layouts.app')

@section('title', 'Kata Sandi Baru')

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

<div class="max-w-md rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-sm">
    <div class="mb-6 flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-100">
            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Kata Sandi Baru</h1>
            <p class="text-xs text-slate-500 font-medium">OTP berhasil diverifikasi</p>
        </div>
    </div>

    <x-alert type="success" :message="session('success')" />

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-xs text-red-700 font-semibold">
            <ul class="list-disc pl-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.profil.set-sandi-baru') }}"
          x-data="{ loading: false }" @submit="loading = true" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Kata Sandi Baru <span class="text-red-500">*</span></label>
            <input id="password" type="password" name="password" required
                   placeholder="Min. 8 karakter (huruf & angka)"
                   class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 shadow-inner focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300">
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Konfirmasi Kata Sandi <span class="text-red-500">*</span></label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   placeholder="Ulangi kata sandi baru"
                   class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 shadow-inner focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300">
        </div>

        <button type="submit" :disabled="loading"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-xs font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-[0.98] transition-all duration-300 disabled:opacity-60">
            <span x-cloak x-show="loading">
                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </span>
            Simpan Kata Sandi
        </button>
    </form>
</div>
@endsection
