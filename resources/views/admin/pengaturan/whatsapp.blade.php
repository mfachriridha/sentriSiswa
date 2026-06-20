@extends('layouts.app')

@section('title', 'WhatsApp')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">WhatsApp API</h1>
    <p class="mt-1 text-sm text-gray-500">Konfigurasi minimal untuk mengirim laporan WhatsApp ke wali kelas.</p>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

@php
    $hasAccessToken = filled($config['access_token']);
    $hasPhoneNumberId = filled($config['phone_number_id']);
    $hasWebhookVerifyToken = filled($config['webhook_verify_token']);
    $isConfigured = $hasAccessToken && $hasPhoneNumberId;
    $maskedAccessToken = $hasAccessToken ? '********'.substr($config['access_token'], -6) : 'Belum tersimpan';
    $maskedPhoneNumberId = $hasPhoneNumberId ? '********'.substr($config['phone_number_id'], -4) : 'Belum tersimpan';
@endphp

<div class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Koneksi API</h2>
                    <p class="mt-1 text-sm text-gray-500">Isi dua data ini dari Meta WhatsApp API Setup.</p>
                </div>
                <span class="inline-flex w-fit items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm font-medium
                    {{ $isConfigured ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-50 text-gray-500' }}">
                    <span class="h-2 w-2 rounded-full {{ $isConfigured ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    {{ $isConfigured ? 'Siap dites' : 'Belum lengkap' }}
                </span>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-lg border {{ $hasAccessToken ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }} px-3 py-2">
                    <p class="text-sm font-medium {{ $hasAccessToken ? 'text-green-800' : 'text-gray-600' }}">API Token</p>
                    <p class="mt-0.5 break-all text-xs {{ $hasAccessToken ? 'text-green-700' : 'text-gray-500' }}">{{ $maskedAccessToken }}</p>
                </div>
                <div class="rounded-lg border {{ $hasPhoneNumberId ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }} px-3 py-2">
                    <p class="text-sm font-medium {{ $hasPhoneNumberId ? 'text-green-800' : 'text-gray-600' }}">Phone Number ID</p>
                    <p class="mt-0.5 break-all text-xs {{ $hasPhoneNumberId ? 'text-green-700' : 'text-gray-500' }}">{{ $maskedPhoneNumberId }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.whatsapp.update') }}" class="space-y-4"
              x-data="{ loading: false }" @submit="loading = true">
            @csrf
            @method('PUT')
            <input type="hidden" name="whatsapp_cloud_api_version" value="{{ $config['api_version'] ?: 'v23.0' }}">

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="space-y-5">
                    <div>
                        <label for="whatsapp_cloud_access_token" class="block text-sm font-medium text-gray-700">API Token</label>
                        <input id="whatsapp_cloud_access_token" type="password" name="whatsapp_cloud_access_token" value="{{ old('whatsapp_cloud_access_token') }}"
                               class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                      placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                               placeholder="{{ $hasAccessToken ? 'Kosongkan jika tidak ingin mengubah token' : 'Masukkan Access Token Meta' }}">
                        <p class="mt-1.5 text-xs text-gray-500">Token lama dipertahankan jika kolom ini dikosongkan.</p>
                        @error('whatsapp_cloud_access_token')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="whatsapp_cloud_phone_number_id" class="block text-sm font-medium text-gray-700">Phone Number ID</label>
                        <input id="whatsapp_cloud_phone_number_id" type="text" name="whatsapp_cloud_phone_number_id"
                               value="{{ old('whatsapp_cloud_phone_number_id') }}"
                               class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                      placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                               placeholder="{{ $hasPhoneNumberId ? 'Kosongkan jika tidak ingin mengubah Phone Number ID' : 'Contoh: 123456789012345' }}">
                        @error('whatsapp_cloud_phone_number_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-lg border border-primary/20 bg-primary/5 p-4"
                         x-data="{
                             callbackUrl: @js(route('whatsapp.webhook.verify')),
                             callbackCopied: false,
                             webhookToken: @js(old('whatsapp_webhook_verify_token', '')),
                             tokenCopied: false,
                             generateWebhookToken() {
                                 const bytes = new Uint8Array(24);
                                 crypto.getRandomValues(bytes);
                                 this.webhookToken = Array.from(bytes, byte => byte.toString(16).padStart(2, '0')).join('');
                                 this.tokenCopied = false;
                             },
                             async copyValue(value, target) {
                                 if (!value) {
                                     return;
                                 }

                                 if (navigator.clipboard && window.isSecureContext) {
                                     await navigator.clipboard.writeText(value);
                                 } else {
                                     const textarea = document.createElement('textarea');
                                     textarea.value = value;
                                     textarea.setAttribute('readonly', '');
                                     textarea.style.position = 'fixed';
                                     textarea.style.opacity = '0';
                                     document.body.appendChild(textarea);
                                     textarea.select();
                                     document.execCommand('copy');
                                     document.body.removeChild(textarea);
                                 }

                                 this[target] = true;
                                 setTimeout(() => this[target] = false, 1500);
                             },
                         }">
                        <div class="flex flex-col gap-1">
                            <h3 class="text-sm font-semibold text-gray-900">Webhook Meta</h3>
                            <p class="text-sm text-gray-600">Wajib diisi di Meta agar sistem bisa menerima status pesan terkirim/gagal.</p>
                        </div>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Callback URL</label>
                                <div class="mt-1.5 flex flex-col gap-2 sm:flex-row">
                                    <input type="text" readonly :value="callbackUrl"
                                           class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm">
                                    <button type="button" @click="copyValue(callbackUrl, 'callbackCopied')"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                                        <span x-text="callbackCopied ? 'Tersalin' : 'Copy'"></span>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="whatsapp_webhook_verify_token" class="block text-sm font-medium text-gray-700">Verify Token</label>
                                <div class="mt-1.5 flex flex-col gap-2 sm:flex-row">
                                    <input id="whatsapp_webhook_verify_token" type="text" name="whatsapp_webhook_verify_token"
                                           x-model="webhookToken" readonly
                                           class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm
                                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                           placeholder="{{ $hasWebhookVerifyToken ? 'Token sudah tersimpan. Generate jika ingin mengganti.' : 'Klik Generate Token' }}">
                                    <button type="button" @click="generateWebhookToken"
                                            class="inline-flex items-center justify-center rounded-lg border border-primary/30 bg-white px-4 py-3 text-sm font-semibold text-primary shadow-sm hover:bg-primary/5">
                                        Generate
                                    </button>
                                    <button type="button" @click="copyValue(webhookToken, 'tokenCopied')" :disabled="!webhookToken"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50">
                                        <span x-text="tokenCopied ? 'Tersalin' : 'Copy'"></span>
                                    </button>
                                </div>
                                <p class="mt-1.5 text-xs text-gray-500">Generate token, klik copy, simpan konfigurasi, lalu tempel token yang sama di Meta. Jika token lama tidak terlihat, generate token baru.</p>
                                @error('whatsapp_webhook_verify_token')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if ($hasAccessToken || $hasPhoneNumberId || $hasWebhookVerifyToken)
                        <details class="rounded-lg border border-red-200 bg-red-50 p-4">
                            <summary class="cursor-pointer text-sm font-semibold text-red-800">Hapus data tersimpan</summary>
                            <div class="mt-3 space-y-2">
                                @if ($hasAccessToken)
                                    <label class="flex items-center gap-2 text-sm text-red-800">
                                        <input type="checkbox" name="clear_whatsapp_cloud_access_token" value="1" class="checkbox checkbox-error checkbox-sm">
                                        Hapus API Token
                                    </label>
                                @endif

                                @if ($hasPhoneNumberId)
                                    <label class="flex items-center gap-2 text-sm text-red-800">
                                        <input type="checkbox" name="clear_whatsapp_cloud_phone_number_id" value="1" class="checkbox checkbox-error checkbox-sm">
                                        Hapus Phone Number ID
                                    </label>
                                @endif

                                @if ($hasWebhookVerifyToken)
                                    <label class="flex items-center gap-2 text-sm text-red-800">
                                        <input type="checkbox" name="clear_whatsapp_webhook_verify_token" value="1" class="checkbox checkbox-error checkbox-sm">
                                        Hapus Webhook Verify Token
                                    </label>
                                @endif
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
         data-test-url="{{ route('admin.settings.whatsapp.test', [], false) }}"
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
        <p class="mt-1 text-sm text-gray-500">Cek token dan Phone Number ID bisa mengirim pesan atau tidak.</p>

        @unless ($isConfigured)
            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                Lengkapi API Token dan Phone Number ID dulu.
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
