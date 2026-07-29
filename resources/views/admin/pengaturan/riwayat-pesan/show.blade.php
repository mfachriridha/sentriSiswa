@extends('layouts.app')

@section('title', 'Detail Pesan WhatsApp')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.pengaturan.whatsapp.riwayat') }}" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
    </a>
    <h1 class="text-2xl font-bold text-gray-900">Detail Pesan</h1>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<div class="grid gap-5 lg:grid-cols-[1fr_0.8fr]">
    {{-- Isi Pesan --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Isi Pesan</h2>
        <pre class="whitespace-pre-wrap text-sm text-gray-800 leading-relaxed font-sans">{{ $pesanWhatsapp->isi_pesan }}</pre>
    </div>

    {{-- Info & Status --}}
    <div class="space-y-5">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informasi Pengiriman</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Kelas</dt>
                    <dd class="font-medium text-gray-800 text-right">{{ $pesanWhatsapp->kelas?->nama ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Penerima</dt>
                    <dd class="font-medium text-gray-800 text-right">{{ $pesanWhatsapp->nama_penerima ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Nomor HP</dt>
                    <dd class="font-medium text-gray-800 text-right">{{ $pesanWhatsapp->telepon_penerima }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Tipe Pesan</dt>
                    <dd class="text-gray-700 text-right">{{ $pesanWhatsapp->tipe_pesan }}</dd>
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
                    <dt class="text-gray-500">Percobaan</dt>
                    <dd class="text-gray-700 text-right">{{ $pesanWhatsapp->percobaan }}× dari 3</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Waktu Kirim</dt>
                    <dd class="text-gray-700 text-right">{{ $pesanWhatsapp->dikirim_pada?->format('d/m/Y H:i:s') ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Dibuat</dt>
                    <dd class="text-gray-700 text-right">{{ $pesanWhatsapp->dibuat_pada->format('d/m/Y H:i:s') }}</dd>
                </div>
                @if ($pesanWhatsapp->id_pesan_provider)
                <div class="flex justify-between gap-4 border-t border-gray-100 pt-3">
                    <dt class="text-gray-500">Fonnte Message ID</dt>
                    <dd class="text-gray-700 text-right font-mono text-xs">{{ $pesanWhatsapp->id_pesan_provider }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4">
            @if ($pesanWhatsapp->dibuat_pada->isToday())
                <form method="POST" action="{{ route('admin.pengaturan.whatsapp.riwayat.kirim-ulang', $pesanWhatsapp) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Kirim Ulang
                    </button>
                    <p class="mt-2 text-xs text-gray-500">Isi pesan disusun ulang dari kondisi absensi terkini sebelum dikirim.</p>
                </form>
            @else
                <p class="text-sm text-gray-500">
                    Pesan ini dibuat di hari lain, datanya sudah kedaluwarsa. Kirim ulang tidak tersedia untuk pesan lama.
                </p>
            @endif
        </div>

        @if ($pesanWhatsapp->respons)
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Response API Fonnte</h2>
            @php
                $decoded = json_decode($pesanWhatsapp->respons, true);
            @endphp
            @if ($decoded)
                <pre class="whitespace-pre-wrap text-xs text-gray-700 bg-gray-50 rounded-lg p-4 overflow-auto">{{ json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-4">{{ $pesanWhatsapp->respons }}</p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
