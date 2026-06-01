@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<x-page-header title="Dashboard Guru" :description="'Selamat datang, '.auth()->user()->name.'!'" />

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @if (auth()->user()->isHomeroom())
        <x-shortcut-card :href="route('guru.kelas-saya')" title="Kelas Saya" description="Pantau status absensi harian siswa.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>

        <x-shortcut-card :href="route('guru.absensi.index')" title="Rekap Absensi" description="Filter dan ekspor laporan kehadiran kelas.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-6 9l2 2 4-4"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>
    @endif

    @if (auth()->user()->isStudentAffairs())
        <x-shortcut-card :href="route('guru.monitoring.index')" title="Monitoring Siswa" description="Cari siswa dan periksa catatan disiplin.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>

        <x-shortcut-card :href="route('guru.pelanggaran-siswa.index')" title="Pelanggaran Siswa" description="Catat dan kelola pelanggaran siswa.">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-2.99L13.74 4a2 2 0 00-3.48 0L3.33 16.01A2 2 0 005.07 19z"/>
                </svg>
            </x-slot:icon>
        </x-shortcut-card>
    @endif

    <x-shortcut-card :href="route('guru.profil')" title="Profil Saya" description="Periksa dan perbarui informasi akun.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>
</div>
@endsection
