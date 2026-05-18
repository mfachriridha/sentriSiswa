<div class="p-6" x-data="{ searchOpen: false }" @click.away="searchOpen = false">
    <flux:heading size="xl" class="mb-6">{{ __('Catat Pelanggaran') }}</flux:heading>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <flux:card class="space-y-4">
            <flux:heading size="base">{{ __('Form Pelanggaran') }}</flux:heading>

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
                                class="w-full text-left px-3 py-2 text-sm hover:bg-zinc-100 flex items-center justify-between">
                                <span>{{ $s['nama'] }}</span>
                                <span class="text-zinc-400 text-xs">{{ $s['nis'] }} - {{ $s['kelas'] }}</span>
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

            <flux:button variant="primary" wire:click="simpan" class="w-full">
                {{ __('Simpan Pelanggaran') }}
            </flux:button>
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="base">{{ __('Pelanggaran Terbaru') }}</flux:heading>

            <div class="overflow-auto max-h-96">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-zinc-300 sticky top-0 bg-white">
                            <th class="text-left p-2 font-semibold text-zinc-700">{{ __('Siswa') }}</th>
                            <th class="text-left p-2 font-semibold text-zinc-700">{{ __('Jenis') }}</th>
                            <th class="text-left p-2 font-semibold text-zinc-700">{{ __('Poin') }}</th>
                            <th class="text-left p-2 font-semibold text-zinc-700">{{ __('Tanggal') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-300">
                        @forelse($recent as $r)
                            <tr wire:key="rec-{{ $r->id }}">
                                <td class="p-2">{{ $r->user?->siswa?->nama ?? $r->user?->name ?? '-' }}</td>
                                <td class="p-2">{{ $r->poinPelanggaran?->jenis_pelanggaran ?? '-' }}</td>
                                <td class="p-2">
                                    <flux:badge color="red" size="sm">{{ $r->poinPelanggaran?->poin ?? 0 }}</flux:badge>
                                </td>
                                <td class="p-2">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-zinc-500">{{ __('Belum ada pelanggaran.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>
</div>
