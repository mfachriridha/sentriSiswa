@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<x-page-header title="Dashboard" :description="'Selamat datang, '.auth()->user()->nama.'.'" />

<!-- SaaS Metrics Bar -->
<div class="mb-6 grid gap-4 sm:grid-cols-3">
    <!-- Total Siswa Card -->
    <div class="flex items-center gap-4 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="rounded-xl bg-primary/10 p-3 text-primary">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Siswa</p>
            <p class="text-2xl font-extrabold text-slate-800 mt-0.5">{{ \App\Models\StudentProfile::count() }}</p>
        </div>
    </div>

    <!-- Total Guru Card -->
    <div class="flex items-center gap-4 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="rounded-xl bg-teal-500/10 p-3 text-teal-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Guru</p>
            <p class="text-2xl font-extrabold text-slate-800 mt-0.5">{{ \App\Models\User::whereIn('role', ['wali_kelas', 'bk', 'kesiswaan'])->count() }}</p>
        </div>
    </div>

    <!-- Total Kelas Card -->
    <div class="flex items-center gap-4 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="rounded-xl bg-indigo-500/10 p-3 text-indigo-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kelas</p>
            <p class="text-2xl font-extrabold text-slate-800 mt-0.5">{{ \App\Models\SchoolClass::count() }}</p>
        </div>
    </div>
</div>

<div class="mb-6 grid gap-4 lg:grid-cols-3">
    <x-dashboard-bar-chart title="Siswa per Tingkat" :items="$charts['studentsByGrade']" />
    <x-dashboard-bar-chart title="Status Registrasi Siswa" :items="$charts['registration']" />
    <x-dashboard-bar-chart title="Komposisi Akun" :items="$charts['roles']" />
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <x-shortcut-card :href="route('admin.guru.index')" title="Data Guru" description="Kelola akun dan profil guru sekolah.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>

    <x-shortcut-card :href="route('admin.siswa.index')" title="Data Siswa" description="Kelola akun, kelas, dan biodata siswa.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>

    <x-shortcut-card :href="route('admin.kelas.index')" title="Data Kelas" description="Atur kelas dan wali kelas yang bertugas.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>
</div>
@endsection
