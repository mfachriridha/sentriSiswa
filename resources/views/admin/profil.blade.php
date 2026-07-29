@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Profil</h1>
        <p class="mt-1 text-xs font-semibold text-slate-500">Informasi akun bantuan dan pengaturan akses aplikasi.</p>
    </div>
    <a href="{{ route('admin.profil.edit') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-[0.98] transition-all duration-300">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
        </svg>
        Edit Profil
    </a>
</div>

<x-alert type="success" :message="session('success')" />

<div class="rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-sm">
    <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
        <!-- Profile Picture Column -->
        <div class="flex flex-col items-center text-center">
            @if ($admin->foto)
                <img src="{{ asset('storage/'.$admin->foto) }}" alt="{{ $admin->nama }}"
                     class="h-32 w-32 rounded-full border-4 border-slate-50 shadow-xl object-cover">
            @else
                <div class="flex h-32 w-32 items-center justify-center rounded-full border-4 border-slate-50 bg-primary/10 text-4xl font-extrabold text-primary shadow-inner">
                    {{ strtoupper(substr($admin->nama, 0, 1)) }}
                </div>
            @endif
            <h2 class="text-xl font-extrabold text-slate-900 mt-4">{{ $admin->nama }}</h2>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 mt-2 text-xs font-bold text-primary">
                <span class="h-1.5 w-1.5 rounded-full bg-primary animate-pulse"></span>
                {{ $admin->roleLabel() }}
            </span>
        </div>

        <!-- Details Info Card Column -->
        <div class="flex-1 w-full grid gap-5 md:grid-cols-2">
            <div class="rounded-xl border border-slate-50 bg-slate-50/50 p-5 shadow-inner">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Alamat Email</span>
                <p class="mt-2 text-sm font-semibold text-slate-800">{{ $admin->email }}</p>
                <div class="mt-4 flex items-center gap-2 text-xs text-green-600 font-semibold bg-green-50 rounded-lg px-3 py-1.5 w-max">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Email Aktif Terverifikasi
                </div>
            </div>

            <div class="rounded-xl border border-slate-50 bg-slate-50/50 p-5 shadow-inner">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nomor WhatsApp Bantuan</span>
                <p class="mt-2 text-sm font-semibold text-slate-800">{{ $admin->nomor_wa ?? '-' }}</p>
                <p class="mt-4 text-[10px] text-slate-500 font-medium">Nomor yang tertera di atas akan digunakan sebagai kontak bantuan bagi siswa & guru saat mengalami kendala pendaftaran.</p>
            </div>
        </div>
    </div>
</div>
@endsection
