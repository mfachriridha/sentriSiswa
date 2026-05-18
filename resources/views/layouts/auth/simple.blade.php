<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-gradient-to-br from-brand-50 via-white to-brand-100 antialiased">
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-6">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-3" wire:navigate>
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-300/50">
                    <x-app-logo-icon class="w-9 h-9 fill-current text-white" />
                </div>
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-brand-700">Sentri Siswa</h1>
                    <p class="text-sm font-medium text-brand-600">Portal Absensi Sekolah</p>
                </div>
            </a>
            <div class="bg-white rounded-2xl shadow-xl shadow-brand-200/60 border-2 border-brand-200 p-8">
                {{ $slot }}
            </div>
        </div>
    </div>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>
</html>
