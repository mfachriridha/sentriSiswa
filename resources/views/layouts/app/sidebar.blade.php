<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            @php $role = auth()->user()->role; @endphp

            @if($role === 'admin')
                <flux:sidebar.group :heading="__('Admin Panel')" class="grid">
                    <flux:sidebar.item icon="squares-2x2" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="academic-cap" :href="route('admin.kelas')" :current="request()->routeIs('admin.kelas')" wire:navigate>
                        {{ __('Kelas') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" :href="route('admin.siswa')" :current="request()->routeIs('admin.siswa')" wire:navigate>
                        {{ __('Siswa') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="briefcase" :href="route('admin.guru')" :current="request()->routeIs('admin.guru')" wire:navigate>
                        {{ __('Guru') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="arrow-up-tray" :href="route('admin.import')" :current="request()->routeIs('admin.import')" wire:navigate>
                        {{ __('Import Data') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                <flux:sidebar.group :heading="__('Pelanggaran & Poin')" class="grid">
                    <flux:sidebar.item icon="exclamation-triangle" :href="route('admin.poin-pelanggaran')" :current="request()->routeIs('admin.poin-pelanggaran')" wire:navigate>
                        {{ __('Poin Pelanggaran') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="pencil-square" :href="route('admin.catat-pelanggaran')" :current="request()->routeIs('admin.catat-pelanggaran')" wire:navigate>
                        {{ __('Catat Pelanggaran') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard-document-check" :href="route('admin.pengajuan-poin')" :current="request()->routeIs('admin.pengajuan-poin')" wire:navigate>
                        {{ __('Pengajuan Poin') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                <flux:sidebar.group :heading="__('Lainnya')" class="grid">
                    <flux:sidebar.item icon="document-text" :href="route('admin.tata-tertib')" :current="request()->routeIs('admin.tata-tertib')" wire:navigate>
                        {{ __('Tata Tertib') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="calendar-days" :href="route('admin.absensi')" :current="request()->routeIs('admin.absensi')" wire:navigate>
                        {{ __('Absensi') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="cog-6-tooth" :href="route('admin.konfigurasi')" :current="request()->routeIs('admin.konfigurasi')" wire:navigate>
                        {{ __('Konfigurasi') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @elseif($role === 'guru')
                @php
                    $isWaliKelas = auth()->user()->guru?->waliKelas()->exists();
                    $isBk = auth()->user()->guru?->bkTingkat()->exists();
                @endphp
                <flux:sidebar.group :heading="__('Guru Panel')" class="grid">
                    <flux:sidebar.item icon="squares-2x2" :href="route('guru.dashboard')" :current="request()->routeIs('guru.dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    @if($isWaliKelas)
                    <flux:sidebar.item icon="calendar-days" :href="route('guru.absensi')" :current="request()->routeIs('guru.absensi')" wire:navigate>
                        {{ __('Absensi Kelas') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="arrow-up-circle" :href="route('guru.pengajuan-poin')" :current="request()->routeIs('guru.pengajuan-poin')" wire:navigate>
                        {{ __('Pengajuan Poin') }}
                    </flux:sidebar.item>
                    @endif
                    @if($isBk)
                    <flux:sidebar.item icon="eye" :href="route('guru.monitoring')" :current="request()->routeIs('guru.monitoring')" wire:navigate>
                        {{ __('Monitoring') }}
                    </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
                <flux:sidebar.group :heading="__('Akun')" class="grid">
                    <flux:sidebar.item icon="user-circle" :href="route('guru.profile')" :current="request()->routeIs('guru.profile')" wire:navigate>
                        {{ __('Profile') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @elseif($role === 'siswa')
                <flux:sidebar.group :heading="__('Menu Siswa')" class="grid">
                    <flux:sidebar.item icon="squares-2x2" :href="route('siswa.dashboard')" :current="request()->routeIs('siswa.dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clock" :href="route('siswa.riwayat')" :current="request()->routeIs('siswa.riwayat')" wire:navigate>
                        {{ __('Riwayat Absensi') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="exclamation-circle" :href="route('siswa.pelanggaran')" :current="request()->routeIs('siswa.pelanggaran')" wire:navigate>
                        {{ __('Pelanggaran') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="document-text" :href="route('siswa.tata-tertib')" :current="request()->routeIs('siswa.tata-tertib')" wire:navigate>
                        {{ __('Tata Tertib') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                <flux:sidebar.group :heading="__('Akun')" class="grid">
                    <flux:sidebar.item icon="user-circle" :href="route('siswa.profile')" :current="request()->routeIs('siswa.profile')" wire:navigate>
                        {{ __('Profile') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endif
        </flux:sidebar.nav>

        <flux:spacer />

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:spacer />
        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>
                <flux:menu.separator />
                <flux:menu.radio.group>
                    @if($role === 'guru')
                    <flux:menu.item :href="route('guru.profile')" icon="cog" wire:navigate>{{ __('Profile') }}</flux:menu.item>
                    @elseif($role === 'siswa')
                    <flux:menu.item :href="route('siswa.profile')" icon="cog" wire:navigate>{{ __('Profile') }}</flux:menu.item>
                    @endif
                </flux:menu.radio.group>
                <flux:menu.separator />
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <flux:main>
        {{ $slot }}
    </flux:main>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>
</html>
