@extends('layouts.app')

@section('title', 'WhatsApp')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">WhatsApp</h1>
    <p class="mt-1 text-base text-gray-500">Konfigurasi token Fonnte untuk mengirim notifikasi WhatsApp otomatis ke wali kelas.</p>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

{{-- Status Token --}}
<div class="mb-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-700">Status Token</p>
                @if ($fonnteToken)
                    <p class="mt-1 text-sm text-gray-500">Token terakhir: ••••••••••••••{{ substr($fonnteToken, -4) }}</p>
                @endif
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-medium
                {{ $fonnteToken ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-50 text-gray-500 border border-gray-200' }}">
                <span class="h-2 w-2 rounded-full {{ $fonnteToken ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                {{ $fonnteToken ? 'Token tersimpan' : 'Token belum dikonfigurasi' }}
            </span>
        </div>
    </div>
</div>

{{-- Form Token --}}
<form method="POST" action="{{ route('admin.settings.whatsapp.update') }}" class="space-y-6"
      x-data="{ loading: false }" @submit="loading = true">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white p-8">
        <div class="max-w-xl space-y-6">
            <div>
                <label for="fonnte_token" class="block text-base font-medium text-gray-700">Fonnte Token</label>
                <input id="fonnte_token" type="password" name="fonnte_token" value="{{ old('fonnte_token') }}"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                       placeholder="{{ $fonnteToken ? 'Kosongkan jika tidak ingin mengubah' : 'Masukkan token dari Fonnte' }}">
                <p class="mt-1.5 text-sm text-gray-500">Dapatkan token dari <a href="https://fonnte.com" target="_blank" class="text-primary hover:underline">fonnte.com</a>.</p>
                @error('fonnte_token')
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

@if ($fonnteToken)
    {{-- Test Kirim --}}
    <div class="mt-8 rounded-xl border border-gray-200 bg-white p-8"
         x-data="{
             phone: '',
             message: '',
             loading: false,
             result: null,
             resultType: '',

             sendTest() {
                 this.loading = true;
                 this.result = null;

                 fetch('{{ route('admin.settings.whatsapp.test') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                     },
                     body: JSON.stringify({
                         phone: this.phone,
                         message: this.message,
                     }),
                 })
                 .then(r => r.json())
                 .then(data => {
                     this.result = data.success ? 'Pesan berhasil dikirim!' : (data.error || 'Gagal mengirim pesan.');
                     this.resultType = data.success ? 'success' : 'error';
                 })
                 .catch(() => {
                     this.result = 'Terjadi kesalahan koneksi.';
                     this.resultType = 'error';
                 })
                 .finally(() => {
                     this.loading = false;
                 });
             },
         }">
        <h2 class="text-lg font-semibold text-gray-900">Test Kirim WhatsApp</h2>
        <p class="mt-1 text-sm text-gray-500">Kirim pesan percobaan untuk memastikan token berfungsi.</p>

        <div class="mt-6 max-w-xl space-y-5">
            <div>
                <label for="test_phone" class="block text-sm font-medium text-gray-700">Nomor HP</label>
                <input id="test_phone" type="text" x-model="phone"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                       placeholder="08xxx atau 628xxx atau +628xxx">
                <p class="mt-1 text-xs text-gray-500">Format otomatis disesuaikan (08xxx → 628xxx).</p>
            </div>

            <div>
                <label for="test_message" class="block text-sm font-medium text-gray-700">Pesan</label>
                <textarea id="test_message" x-model="message" rows="3"
                          class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                                 placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                          placeholder="Tulis pesan percobaan..."></textarea>
            </div>

            <div class="flex items-center gap-4">
                <button type="button" @click="sendTest()" :disabled="loading || !phone || !message"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-base font-semibold text-white shadow-sm
                               hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                               transition-colors disabled:opacity-60">
                    <span x-show="loading">
                        <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </span>
                    Kirim Test
                </button>
            </div>

            <template x-if="result">
                <div class="rounded-lg border p-4 text-sm font-medium"
                     :class="resultType === 'success' ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'">
                    <p x-text="result"></p>
                </div>
            </template>
        </div>
    </div>
@endif
@endsection