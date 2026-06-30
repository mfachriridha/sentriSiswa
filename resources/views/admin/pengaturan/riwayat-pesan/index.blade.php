@extends('layouts.app')

@section('title', 'Riwayat Pengiriman Pesan WhatsApp')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.settings.whatsapp.index') }}" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Riwayat Pengiriman Pesan</h1>
        <p class="mt-0.5 text-sm text-gray-500">Log semua pesan WhatsApp yang dikirim, termasuk pesan uji.</p>
    </div>
</div>

{{-- Filter --}}
<form method="GET" class="mb-5 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
        <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">Semua Status</option>
            <option value="sent" @selected(request('status') === 'sent')>Terkirim</option>
            <option value="failed" @selected(request('status') === 'failed')>Gagal</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Kelas</label>
        <select name="kelas_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">Semua Kelas</option>
            @foreach ($kelas as $k)
                <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal</label>
        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
               class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
    </div>
    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary/90">Filter</button>
    @if (request()->hasAny(['status', 'class_id', 'tanggal']))
        <a href="{{ route('admin.settings.whatsapp.riwayat') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Reset</a>
    @endif
</form>

<div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Penerima</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu Kirim</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Dibuat</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($pesan as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $p->kelas?->nama ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @if($p->tipe_pesan === 'test')
                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-xs font-semibold text-purple-700">Test</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">Laporan</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800">{{ $p->nama_penerima ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $p->telepon_penerima }}</p>
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $badge = match($p->status) {
                                'sent'       => 'badge-success',
                                'failed'     => 'badge-error',
                                'processing' => 'badge-warning',
                                default      => 'badge-ghost',
                            };
                            $label = match($p->status) {
                                'sent'       => 'Terkirim',
                                'failed'     => 'Gagal',
                                'pending'    => 'Pending',
                                'processing' => 'Memproses',
                                default      => $p->status,
                            };
                        @endphp
                        <span class="badge {{ $badge }} badge-sm">{{ $label }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $p->dikirim_pada ? $p->dikirim_pada->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $p->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.settings.whatsapp.riwayat.show', $p) }}"
                           class="text-xs font-semibold text-primary hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">Belum ada riwayat pengiriman pesan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $pesan->links() }}
</div>
@endsection
