@extends('layouts.app')

@section('title', 'WhatsApp')

@section('content')
<div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">WhatsApp API</h1>
        <p class="mt-1 text-sm text-gray-500">Konfigurasi token Fonnte untuk mengirim laporan absensi ke wali kelas.</p>
    </div>
    <a href="{{ route('admin.pengaturan.whatsapp.riwayat') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition">
        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
        </svg>
        Riwayat Pengiriman
    </a>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

@php
    $hasToken = filled($config['token']);
    $isConfigured = $hasToken;
    $maskedToken = $hasToken ? '********'.substr($config['token'], -4) : 'Belum tersimpan';
@endphp

<div class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Status Koneksi</h2>
                    <p class="mt-1 text-sm text-gray-500">Token Fonnte diambil dari dashboard Fonnte (Device menu).</p>
                </div>
                <span class="inline-flex w-fit items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm font-medium
                    {{ $isConfigured ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-50 text-gray-500' }}">
                    <span class="h-2 w-2 rounded-full {{ $isConfigured ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    {{ $isConfigured ? 'Siap dipakai' : 'Belum lengkap' }}
                </span>
            </div>

            <div class="mt-4 rounded-lg border {{ $hasToken ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }} px-3 py-2">
                <p class="text-sm font-medium {{ $hasToken ? 'text-green-800' : 'text-gray-600' }}">Token Fonnte</p>
                <p class="mt-0.5 break-all text-xs {{ $hasToken ? 'text-green-700' : 'text-gray-500' }}">{{ $maskedToken }}</p>
            </div>

            @unless ($isConfigured)
                <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                    Token Fonnte belum diisi. Laporan absensi tidak akan dikirim sampai token tersedia.
                </div>
            @endunless
        </div>

        <form method="POST" action="{{ route('admin.pengaturan.whatsapp.update') }}" class="space-y-4"
              x-data="{ loading: false }" @submit="loading = true">
            @csrf
            @method('PUT')

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="space-y-5">
                    <div>
                        <label for="fonnte_token" class="block text-sm font-medium text-gray-700">Token Fonnte</label>
                        <input id="fonnte_token" type="password" name="fonnte_token" value="{{ old('fonnte_token') }}"
                               class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                      placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                               placeholder="{{ $hasToken ? 'Kosongkan jika tidak ingin mengubah token' : 'Masukkan token dari dashboard Fonnte' }}">
                        <p class="mt-1.5 text-xs text-gray-500">Token lama dipertahankan jika kolom ini dikosongkan. Dapatkan token dari Fonnte Dashboard &raquo; Device &raquo; klik ikon token.</p>
                        @error('fonnte_token')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($hasToken)
                        <details class="rounded-lg border border-red-200 bg-red-50 p-4">
                            <summary class="cursor-pointer text-sm font-semibold text-red-800">Hapus token tersimpan</summary>
                            <div class="mt-3 space-y-2">
                                <label class="flex items-center gap-2 text-sm text-red-800">
                                    <input type="checkbox" name="clear_fonnte_token" value="1" class="checkbox checkbox-error checkbox-sm">
                                    Hapus Token Fonnte
                                </label>
                            </div>
                        </details>
                    @endif
                </div>
            </div>

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
                Simpan
            </button>
        </form>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
         data-test-url="{{ route('admin.pengaturan.whatsapp.test', [], false) }}"
         x-data="{
             endpoint: '',
             configured: {{ $isConfigured ? 'true' : 'false' }},
             phone: '',
             message: 'Test WhatsApp Sentri Siswa',
             loading: false,
             result: '',
             resultType: '',
             cooldown: 0,
             cooldownTarget: '',
             timer: null,
             init() {
                 this.endpoint = this.$el.dataset.testUrl;
             },
             normalizedPhone(value) {
                 const cleaned = String(value).replace(/[^0-9+]/g, '');
                 if (cleaned.startsWith('+62')) {
                     return cleaned.slice(1);
                 }
                 if (cleaned.startsWith('0')) {
                     return `62${cleaned.slice(1)}`;
                 }
                 return cleaned.replace(/^\+/, '');
             },
             isCurrentNumberCoolingDown() {
                 return this.cooldown > 0 && this.cooldownTarget === this.normalizedPhone(this.phone);
             },
             startCooldown(seconds) {
                 this.cooldown = Number(seconds) || 0;
                 this.cooldownTarget = this.normalizedPhone(this.phone);
                 if (this.timer) {
                     clearInterval(this.timer);
                 }
                 if (this.cooldown > 0) {
                     this.timer = setInterval(() => {
                         this.cooldown = Math.max(0, this.cooldown - 1);
                         if (this.cooldown === 0) {
                             clearInterval(this.timer);
                             this.timer = null;
                         }
                     }, 1000);
                 }
             },
             async sendTest() {
                 if (!this.configured || this.loading || this.isCurrentNumberCoolingDown() || !this.phone || !this.message) {
                     return;
                 }

                 this.loading = true;
                 this.result = '';
                 this.resultType = '';
                 const controller = new AbortController();
                 const timeout = setTimeout(() => controller.abort(), 65000);

                 try {
                     const response = await fetch(this.endpoint, {
                         method: 'POST',
                         credentials: 'same-origin',
                         signal: controller.signal,
                         headers: {
                             'Accept': 'application/json',
                             'Content-Type': 'application/json',
                             'X-Requested-With': 'XMLHttpRequest',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                         },
                         body: JSON.stringify({
                             phone: this.phone,
                             message: this.message,
                         }),
                     });

                     const contentType = response.headers.get('content-type') || '';
                     const data = contentType.includes('application/json')
                         ? await response.json()
                         : { success: false, error: `Server membalas ${response.status}, tetapi bukan JSON.` };

                     if (data.retry_after) {
                         this.startCooldown(data.retry_after);
                     }

                     this.result = data.success ? 'API berhasil mengirim pesan test.' : (data.error || 'Gagal mengirim pesan test.');
                     this.resultType = data.success ? 'success' : 'error';
                 } catch (error) {
                     this.result = error.name === 'AbortError'
                         ? 'Request test melewati batas 65 detik.'
                         : 'Terjadi kesalahan koneksi saat mengirim test.';
                     this.resultType = 'error';
                 } finally {
                     clearTimeout(timeout);
                     this.loading = false;
                 }
             },
         }">
        <h2 class="text-lg font-semibold text-gray-900">Test API</h2>
        <p class="mt-1 text-sm text-gray-500">Cek token Fonnte bisa mengirim pesan atau tidak.</p>

        @unless ($isConfigured)
            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                Lengkapi token Fonnte dulu.
            </div>
        @endunless

        <div class="mt-5 space-y-5">
            <div>
                <label for="test_phone" class="block text-sm font-medium text-gray-700">Nomor Tujuan</label>
                <input id="test_phone" type="text" x-model="phone"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                       placeholder="08xxx atau 628xxx">
            </div>

            <div>
                <label for="test_message" class="block text-sm font-medium text-gray-700">Pesan Test</label>
                <textarea id="test_message" rows="3" x-model="message"
                          class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                 placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"></textarea>
            </div>

            <button type="button" @click="sendTest" :disabled="!configured || loading || isCurrentNumberCoolingDown() || !phone || !message"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                           hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                           transition-colors disabled:opacity-60">
                <span x-cloak x-show="loading">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                <span x-text="!configured ? 'Belum Lengkap' : (isCurrentNumberCoolingDown() ? `Tunggu ${cooldown}s` : 'Kirim Test')"></span>
            </button>

            <template x-if="result">
                <div class="rounded-lg border p-4 text-sm font-medium"
                     :class="resultType === 'success' ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'">
                    <p x-text="result"></p>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
