@extends('layouts.app')

@section('title', 'Detail Pesan WhatsApp')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.settings.whatsapp.riwayat') }}" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
    </a>
    <h1 class="text-2xl font-bold text-gray-900">Detail Pesan</h1>
</div>

<div class="grid gap-5 lg:grid-cols-[1fr_0.8fr]">
    {{-- Isi Pesan --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Isi Pesan</h2>
        <pre class="whitespace-pre-wrap text-sm text-gray-800 leading-relaxed font-sans">{{ $pesanWhatsapp->message }}</pre>
    </div>

    {{-- Info & Status --}}
    <div class="space-y-5">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informasi Pengiriman</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Kelas</dt>
                    <dd class="font-medium text-gray-800 text-right">{{ $pesanWhatsapp->schoolClass?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Penerima</dt>
                    <dd class="font-medium text-gray-800 text-right">{{ $pesanWhatsapp->recipient_name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Nomor HP</dt>
                    <dd class="font-medium text-gray-800 text-right">{{ $pesanWhatsapp->recipient_phone }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Tipe Pesan</dt>
                    <dd class="text-gray-700 text-right">{{ $pesanWhatsapp->message_type }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-t border-gray-100 pt-3">
                    <dt class="text-gray-500">Status</dt>
                    <dd>
                        @php
                            $badge = match($pesanWhatsapp->status) {
                                'sent'       => 'badge-success',
                                'failed'     => 'badge-error',
                                'processing' => 'badge-warning',
                                default      => 'badge-ghost',
                            };
                            $label = match($pesanWhatsapp->status) {
                                'sent'       => 'Terkirim',
                                'failed'     => 'Gagal',
                                'pending'    => 'Pending',
                                'processing' => 'Memproses',
                                default      => $pesanWhatsapp->status,
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ $label }}</span>
                    </dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Waktu Kirim</dt>
                    <dd class="text-gray-700 text-right">{{ $pesanWhatsapp->sent_at?->format('d/m/Y H:i:s') ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Dibuat</dt>
                    <dd class="text-gray-700 text-right">{{ $pesanWhatsapp->created_at->format('d/m/Y H:i:s') }}</dd>
                </div>
                @if ($pesanWhatsapp->provider_message_id)
                <div class="flex justify-between gap-4 border-t border-gray-100 pt-3">
                    <dt class="text-gray-500">Fonnte Message ID</dt>
                    <dd class="text-gray-700 text-right font-mono text-xs">{{ $pesanWhatsapp->provider_message_id }}</dd>
                </div>
                @endif
            </dl>
        </div>

        @if ($pesanWhatsapp->response)
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Response API Fonnte</h2>
            @php
                $decoded = json_decode($pesanWhatsapp->response, true);
            @endphp
            @if ($decoded)
                <pre class="whitespace-pre-wrap text-xs text-gray-700 bg-gray-50 rounded-lg p-4 overflow-auto">{{ json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-4">{{ $pesanWhatsapp->response }}</p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
