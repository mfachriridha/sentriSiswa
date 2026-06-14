@extends('layouts.app')

@section('title', 'WhatsApp')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">WhatsApp</h1>
    <p class="mt-1 text-sm text-gray-500">Konfigurasi WhatsApp Cloud API resmi untuk laporan WhatsApp otomatis ke wali kelas.</p>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

@php
    $hasAccessToken = filled($config['access_token']);
    $hasPhoneNumberId = filled($config['phone_number_id']);
    $hasBusinessAccountId = filled($config['business_account_id']);
    $hasWebhookVerifyToken = filled($config['webhook_verify_token']);
    $isConfigured = $hasAccessToken && $hasPhoneNumberId && filled($config['api_version']);
    $maskedAccessToken = $hasAccessToken ? '********'.substr($config['access_token'], -6) : 'Belum tersimpan';
    $maskedPhoneNumberId = $hasPhoneNumberId ? '********'.substr($config['phone_number_id'], -4) : 'Belum tersimpan';
    $maskedBusinessAccountId = $hasBusinessAccountId ? '********'.substr($config['business_account_id'], -4) : 'Opsional';
    $maskedWebhookVerifyToken = $hasWebhookVerifyToken ? '********'.substr($config['webhook_verify_token'], -6) : 'Belum tersimpan';
@endphp

<div class="mb-6 rounded-xl border border-gray-200 bg-white p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-3">
            <p class="text-sm font-medium text-gray-700">Status Konfigurasi</p>
            <p class="mt-1 text-sm text-gray-500">
                {{ $isConfigured ? 'Access Token dan Phone Number ID tersimpan.' : 'Lengkapi Access Token dan Phone Number ID dari Meta Developer.' }}
            </p>
            <div class="grid gap-2 text-sm sm:grid-cols-2 lg:grid-cols-5">
                <div class="rounded-lg border {{ $hasAccessToken ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }} px-3 py-2">
                    <p class="font-medium {{ $hasAccessToken ? 'text-green-800' : 'text-gray-600' }}">Access Token</p>
                    <p class="mt-0.5 break-all text-xs {{ $hasAccessToken ? 'text-green-700' : 'text-gray-500' }}">{{ $maskedAccessToken }}</p>
                </div>
                <div class="rounded-lg border {{ $hasPhoneNumberId ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }} px-3 py-2">
                    <p class="font-medium {{ $hasPhoneNumberId ? 'text-green-800' : 'text-gray-600' }}">Phone Number ID</p>
                    <p class="mt-0.5 break-all text-xs {{ $hasPhoneNumberId ? 'text-green-700' : 'text-gray-500' }}">{{ $maskedPhoneNumberId }}</p>
                </div>
                <div class="rounded-lg border {{ $hasBusinessAccountId ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }} px-3 py-2">
                    <p class="font-medium {{ $hasBusinessAccountId ? 'text-green-800' : 'text-gray-600' }}">Business Account ID</p>
                    <p class="mt-0.5 break-all text-xs {{ $hasBusinessAccountId ? 'text-green-700' : 'text-gray-500' }}">{{ $maskedBusinessAccountId }}</p>
                </div>
                <div class="rounded-lg border border-green-200 bg-green-50 px-3 py-2">
                    <p class="font-medium text-green-800">Graph API Version</p>
                    <p class="mt-0.5 text-xs text-green-700">{{ $config['api_version'] }}</p>
                </div>
                <div class="rounded-lg border {{ $hasWebhookVerifyToken ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }} px-3 py-2">
                    <p class="font-medium {{ $hasWebhookVerifyToken ? 'text-green-800' : 'text-gray-600' }}">Webhook Verify Token</p>
                    <p class="mt-0.5 break-all text-xs {{ $hasWebhookVerifyToken ? 'text-green-700' : 'text-gray-500' }}">{{ $maskedWebhookVerifyToken }}</p>
                </div>
            </div>
        </div>
        <span class="inline-flex w-fit items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm font-medium
            {{ $isConfigured ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-50 text-gray-500' }}">
            <span class="h-2 w-2 rounded-full {{ $isConfigured ? 'bg-green-500' : 'bg-gray-400' }}"></span>
            {{ $isConfigured ? 'Siap digunakan' : 'Belum lengkap' }}
        </span>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <form method="POST" action="{{ route('admin.settings.whatsapp.update') }}" class="space-y-6"
          x-data="{ loading: false }" @submit="loading = true">
        @csrf
        @method('PUT')

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-gray-900">Koneksi WhatsApp Cloud API</h2>
            <p class="mt-1 text-sm text-gray-500">Masukkan credential dari Meta Developer > WhatsApp > API Setup.</p>

            <div class="mt-5 space-y-5">
                <div>
                    <label for="whatsapp_cloud_access_token" class="block text-sm font-medium text-gray-700">Access Token</label>
                    <input id="whatsapp_cloud_access_token" type="password" name="whatsapp_cloud_access_token" value="{{ old('whatsapp_cloud_access_token') }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                           placeholder="{{ $hasAccessToken ? 'Kosongkan jika tidak ingin mengubah Access Token' : 'Masukkan Access Token Meta' }}">
                    <p class="mt-1.5 text-sm text-gray-500">Token lama dipertahankan jika kolom ini dikosongkan.</p>
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

                <div>
                    <label for="whatsapp_cloud_business_account_id" class="block text-sm font-medium text-gray-700">Business Account ID <span class="text-gray-400">(opsional)</span></label>
                    <input id="whatsapp_cloud_business_account_id" type="text" name="whatsapp_cloud_business_account_id"
                           value="{{ old('whatsapp_cloud_business_account_id') }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                           placeholder="{{ $hasBusinessAccountId ? 'Kosongkan jika tidak ingin mengubah Business Account ID' : 'Contoh: 987654321098765' }}">
                    @error('whatsapp_cloud_business_account_id')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="whatsapp_cloud_api_version" class="block text-sm font-medium text-gray-700">Graph API Version</label>
                    <input id="whatsapp_cloud_api_version" type="text" name="whatsapp_cloud_api_version"
                           value="{{ old('whatsapp_cloud_api_version', $config['api_version']) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                           placeholder="v23.0">
                    @error('whatsapp_cloud_api_version')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm font-semibold text-blue-900">Webhook Meta</p>
                    <p class="mt-1 text-sm text-blue-800">Tempel URL dan verify token ini di Step 2 Configure Webhooks.</p>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-blue-900">Callback URL</label>
                            <input type="text" readonly value="{{ route('whatsapp.webhook.verify') }}"
                                   class="mt-1.5 block w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm">
                        </div>

                        <div>
                            <label for="whatsapp_webhook_verify_token" class="block text-sm font-medium text-blue-900">Verify Token</label>
                            <input id="whatsapp_webhook_verify_token" type="password" name="whatsapp_webhook_verify_token"
                                   value="{{ old('whatsapp_webhook_verify_token') }}"
                                   class="mt-1.5 block w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm
                                          placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                   placeholder="{{ $hasWebhookVerifyToken ? 'Kosongkan jika tidak ingin mengubah verify token' : 'Minimal 16 karakter, bebas tapi rahasia' }}">
                            <p class="mt-1.5 text-xs text-blue-700">Kalau belum ada, buat token acak sendiri lalu simpan dan pakai token yang sama di Meta.</p>
                            @error('whatsapp_webhook_verify_token')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                @if ($hasAccessToken || $hasPhoneNumberId || $hasBusinessAccountId || $hasWebhookVerifyToken)
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                        <p class="text-sm font-semibold text-red-800">Hapus credential tersimpan</p>
                        <p class="mt-1 text-xs text-red-700">Centang data yang ingin dihapus, lalu klik Simpan Konfigurasi.</p>

                        <div class="mt-3 space-y-2">
                            @if ($hasAccessToken)
                                <label class="flex items-center gap-2 text-sm text-red-800">
                                    <input type="checkbox" name="clear_whatsapp_cloud_access_token" value="1" class="checkbox checkbox-error checkbox-sm">
                                    Hapus Access Token tersimpan
                                </label>
                            @endif

                            @if ($hasPhoneNumberId)
                                <label class="flex items-center gap-2 text-sm text-red-800">
                                    <input type="checkbox" name="clear_whatsapp_cloud_phone_number_id" value="1" class="checkbox checkbox-error checkbox-sm">
                                    Hapus Phone Number ID tersimpan
                                </label>
                            @endif

                            @if ($hasBusinessAccountId)
                                <label class="flex items-center gap-2 text-sm text-red-800">
                                    <input type="checkbox" name="clear_whatsapp_cloud_business_account_id" value="1" class="checkbox checkbox-error checkbox-sm">
                                    Hapus Business Account ID tersimpan
                                </label>
                            @endif

                            @if ($hasWebhookVerifyToken)
                                <label class="flex items-center gap-2 text-sm text-red-800">
                                    <input type="checkbox" name="clear_whatsapp_webhook_verify_token" value="1" class="checkbox checkbox-error checkbox-sm">
                                    Hapus Webhook Verify Token tersimpan
                                </label>
                            @endif
                        </div>
                    </div>
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
            Simpan Konfigurasi
        </button>
    </form>

    <div class="rounded-xl border border-gray-200 bg-white p-6"
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

                     this.result = data.success ? 'Pesan test berhasil dikirim.' : (data.error || 'Gagal mengirim pesan test.');
                     this.resultType = data.success ? 'success' : 'error';
                 } catch (error) {
                     this.result = error.name === 'AbortError'
                         ? 'Request test melewati batas 65 detik. Cek koneksi WhatsApp Cloud API atau token Meta.'
                         : 'Terjadi kesalahan koneksi saat mengirim test.';
                     this.resultType = 'error';
                 } finally {
                     clearTimeout(timeout);
                     this.loading = false;
                 }
             },
         }">
        <h2 class="text-lg font-semibold text-gray-900">Test Kirim</h2>
        <p class="mt-1 text-sm text-gray-500">Gunakan untuk debugging. Nomor yang sama diberi cooldown 60 detik.</p>
        <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-3 text-sm text-blue-800">
            Pesan teks bebas dari Cloud API bisa gagal jika nomor tujuan belum berada dalam customer service window WhatsApp.
        </div>
        @unless ($isConfigured)
            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                Test kirim aktif setelah Access Token dan Phone Number ID tersimpan.
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
                <label for="test_message" class="block text-sm font-medium text-gray-700">Pesan</label>
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
                <span x-text="!configured ? 'Lengkapi Credential Dulu' : (isCurrentNumberCoolingDown() ? `Tunggu ${cooldown}s` : 'Kirim Test')"></span>
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
