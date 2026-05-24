@extends('layouts.app')

@section('title', 'WhatsApp API')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">WhatsApp API</h1>
    <p class="mt-1 text-base text-gray-500">Konfigurasi API key Fonnte untuk mengirim notifikasi WhatsApp.</p>
</div>

<form method="POST" action="{{ route('admin.settings.whatsapp.update') }}" class="space-y-6"
      x-data="{ loading: false }" @submit="loading = true">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white p-8">
        <div class="max-w-xl space-y-6">
            <div>
                <label for="fonnte_api_key" class="block text-base font-medium text-gray-700">Fonnte API Key</label>
                <input id="fonnte_api_key" type="password" name="fonnte_api_key" value="{{ old('fonnte_api_key', $fonnteApiKey) }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                       placeholder="Masukkan API key dari Fonnte">
                <p class="mt-1.5 text-sm text-gray-500">Dapatkan API key dari <a href="https://dashboard.fonnte.com" target="_blank" class="text-primary hover:underline">dashboard.fonnte.com</a>.</p>
                @error('fonnte_api_key')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <button type="submit" :disabled="loading"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-base font-semibold text-white shadow-sm
                       hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                       transition-colors disabled:opacity-60">
            <span x-show="loading">
                <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </span>
            Simpan
        </button>
    </div>
</form>
@endsection