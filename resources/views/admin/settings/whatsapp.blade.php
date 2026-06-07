@extends('layouts.app')

@section('title', 'WhatsApp')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">WhatsApp</h1>
    <p class="mt-1 text-sm text-gray-500">Konfigurasi Wapisender untuk laporan WhatsApp otomatis ke wali kelas.</p>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

@php
    $hasApiKey = filled($config['api_key']);
    $hasDeviceKey = filled($config['device_key']);
@endphp

<div class="mb-6 rounded-xl border border-gray-200 bg-white p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-gray-700">Status Konfigurasi</p>
            <p class="mt-1 text-sm text-gray-500">
                {{ $hasApiKey && $hasDeviceKey ? 'API Key dan Device Key tersimpan.' : 'Lengkapi API Key dan Device Key Wapisender.' }}
            </p>
            @if ($hasApiKey)
                <p class="mt-1 text-xs text-gray-500">API Key terakhir: ••••••••••••••{{ substr($config['api_key'], -4) }}</p>
            @endif
        </div>
        <span class="inline-flex w-fit items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm font-medium
            {{ $hasApiKey && $hasDeviceKey ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-50 text-gray-500' }}">
            <span class="h-2 w-2 rounded-full {{ $hasApiKey && $hasDeviceKey ? 'bg-green-500' : 'bg-gray-400' }}"></span>
            {{ $hasApiKey && $hasDeviceKey ? 'Siap digunakan' : 'Belum lengkap' }}
        </span>
    </div>
</div>

<form method="POST" action="{{ route('admin.settings.whatsapp.update') }}" class="space-y-6"
      x-data="{ loading: false }" @submit="loading = true">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="max-w-2xl space-y-5">
            <div>
                <label for="wapisender_api_key" class="block text-sm font-medium text-gray-700">Wapisender API Key</label>
                <input id="wapisender_api_key" type="password" name="wapisender_api_key" value="{{ old('wapisender_api_key') }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                       placeholder="{{ $hasApiKey ? 'Kosongkan jika tidak ingin mengubah API Key' : 'Masukkan API Key Wapisender' }}">
                <p class="mt-1.5 text-sm text-gray-500">API Key disimpan di server dan tidak ditampilkan ulang penuh.</p>
                @error('wapisender_api_key')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="wapisender_device_key" class="block text-sm font-medium text-gray-700">Device Key</label>
                <input id="wapisender_device_key" type="text" name="wapisender_device_key"
                       value="{{ old('wapisender_device_key', $config['device_key']) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                       placeholder="Contoh: WAPI-ABC1234">
                @error('wapisender_device_key')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="wapisender_timeout_seconds" class="block text-sm font-medium text-gray-700">Timeout</label>
                    <input id="wapisender_timeout_seconds" type="number" min="30" max="120" name="wapisender_timeout_seconds"
                           value="{{ old('wapisender_timeout_seconds', $config['timeout_seconds']) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <p class="mt-1 text-xs text-gray-500">Default aman: 60 detik.</p>
                    @error('wapisender_timeout_seconds')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="wapisender_delay_min_seconds" class="block text-sm font-medium text-gray-700">Delay Minimal</label>
                    <input id="wapisender_delay_min_seconds" type="number" min="1" max="60" name="wapisender_delay_min_seconds"
                           value="{{ old('wapisender_delay_min_seconds', $config['delay_min_seconds']) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('wapisender_delay_min_seconds')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="wapisender_delay_max_seconds" class="block text-sm font-medium text-gray-700">Delay Maksimal</label>
                    <input id="wapisender_delay_max_seconds" type="number" min="1" max="120" name="wapisender_delay_max_seconds"
                           value="{{ old('wapisender_delay_max_seconds', $config['delay_max_seconds']) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('wapisender_delay_max_seconds')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                Untuk laporan otomatis, sistem memakai <strong>is_priority=false</strong> dan <strong>simulate_typing=false</strong>.
                Efek pengiriman natural dibuat lewat antrean dan delay acak agar lebih stabil dan tidak mudah timeout.
            </div>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <button type="submit" :disabled="loading"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                       hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                       transition-colors disabled:opacity-60">
            <span x-cloak x-show="loading">
                <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </span>
            Simpan Konfigurasi
        </button>
    </div>
</form>
@endsection
