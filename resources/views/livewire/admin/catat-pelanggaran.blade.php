<div class="p-6" x-data="{ searchOpen: false }" @click.away="searchOpen = false">
    <div class="mb-6">
        <flux:heading size="xl" class="text-zinc-900">{{ __('Catat Pelanggaran') }}</flux:heading>
        <flux:subheading class="text-zinc-600">{{ __('Catat pelanggaran siswa dan lihat riwayat terbaru') }}</flux:subheading>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6 space-y-4">
            <flux:heading size="base" class="text-zinc-900">{{ __('Form Pelanggaran') }}</flux:heading>

            <div class="relative">
                <flux:field>
                    <flux:label>{{ __('Cari Siswa') }}</flux:label>
                    <flux:input wire:model.live.debounce.300ms="searchSiswa" placeholder="Ketik nama atau NIS..."
                        @focus="searchOpen = true" />
                </flux:field>

                @if(!empty($hasilPencarian))
                    <div class="absolute z-10 w-full mt-1 bg-white border-2 border-zinc-300 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                        x-show="searchOpen">
                        @foreach($hasilPencarian as $s)
                            <button wire:click="selectSiswa({{ $s['id'] }})"
                                @click="searchOpen = false"
                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-zinc-50 flex items-center justify-between transition">
                                <span class="font-medium text-zinc-900">{{ $s['nama'] }}</span>
                                <span class="text-zinc-500 text-xs">{{ $s['nis'] }} - {{ $s['kelas'] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <flux:field>
                <flux:label>{{ __('Jenis Pelanggaran') }}</flux:label>
                <flux:select wire:model="poin_pelanggaran_id">
                    <option value="">{{ __('-- Pilih --') }}</option>
                    @foreach($daftarPoin as $p)
                        <option value="{{ $p->id }}">{{ $p->kategori }} - {{ $p->jenis_pelanggaran }} ({{ $p->poin }})</option>
                    @endforeach
                </flux:select>
                <flux:error name="poin_pelanggaran_id" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Keterangan') }}</flux:label>
                <flux:textarea wire:model="keterangan" rows="3" placeholder="Detail pelanggaran..." />
                <flux:error name="keterangan" />
            </flux:field>

            <flux:button variant="primary" wire:click="simpan" class="w-full" icon="check">
                {{ __('Simpan Pelanggaran') }}
            </flux:button>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6 space-y-4">
            <flux:heading size="base" class="text-zinc-900">{{ __('Pelanggaran Terbaru') }}</flux:heading>

            <div class="overflow-auto max-h-96">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-zinc-200 sticky top-0">
                            <th class="text-left p-3 font-semibold text-zinc-700">{{ __('Siswa') }}</th>
                            <th class="text-left p-3 font-semibold text-zinc-700">{{ __('Jenis') }}</th>
                            <th class="text-left p-3 font-semibold text-zinc-700">{{ __('Poin') }}</th>
                            <th class="text-left p-3 font-semibold text-zinc-700">{{ __('Tanggal') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse($recent as $r)
                            <tr wire:key="rec-{{ $r->id }}">
                                <td class="p-3 font-medium text-zinc-900">{{ $r->user?->siswa?->nama ?? $r->user?->name ?? '-' }}</td>
                                <td class="p-3 text-zinc-600">{{ $r->poinPelanggaran?->jenis_pelanggaran ?? '-' }}</td>
                                <td class="p-3">
                                    <flux:badge color="red" size="sm">{{ $r->poinPelanggaran?->poin ?? 0 }}</flux:badge>
                                </td>
                                <td class="p-3 text-zinc-500 text-xs">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center">
                                    <flux:icon.exclamation-circle class="size-8 text-zinc-300 mx-auto mb-2" />
                                    <flux:text class="text-zinc-500">{{ __('Belum ada pelanggaran.') }}</flux:text>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
