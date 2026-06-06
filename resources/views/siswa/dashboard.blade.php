@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<x-page-header title="Dashboard Siswa" :description="'Selamat datang, '.auth()->user()->name.'!'" />

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <x-shortcut-card :href="route('siswa.absensi')" title="Absensi" description="Catat kehadiran harian dan lihat status hari ini.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-6 9l2 2 4-4"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>

    <x-shortcut-card :href="route('siswa.poin')" title="Poin Saya" description="Pantau poin disiplin dan riwayat pelanggaran.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888 1.518-4.674a1 1 0 00-.363-1.118L3.665 8.291H8.58a1 1 0 00.95-.69l1.519-4.674z"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>

    <x-shortcut-card :href="route('siswa.profil')" title="Profil Saya" description="Lihat biodata dan perbarui kontak akun.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>

    <x-shortcut-card :href="route('siswa.tata-tertib.index')" title="Tata Tertib" description="Lihat PDF tata tertib sekolah yang berlaku.">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7m0 8h.01M5 21h14a2 2 0 002-2V7l-6-6H5a2 2 0 00-2 2v16a2 2 0 002 2z"/>
            </svg>
        </x-slot:icon>
    </x-shortcut-card>
</div>
@endsection
