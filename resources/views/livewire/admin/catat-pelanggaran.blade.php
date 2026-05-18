<div class="p-6" x-data="{ searchOpen: false }" @click.away="searchOpen = false">
    <div class="mb-6">
        <flux:heading size="xl" class="text-zinc-900 text-2xl">{{ __('Catat Pelanggaran') }}</flux:heading>
        <flux:subheading class="text-zinc-600 text-base">{{ __('Catat pelanggaran siswa dan lihat riwayat terbaru') }}</flux:subheading>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6 space-y-5">
            <flux:heading size="base" class="text-zinc-900 text-lg">{{ __('Form Pelanggaran') }}</flux:heading>

            <div class="relative">
                <flux:field>
                    <flux:label>{{ __('Cari Siswa') }}</flux:label>
                    <flux:input wire:model.live.debounce.300ms="searchSiswa" placeholder="Ketik nama atau NIS..."
                        @focus="searchOpen = true" class="text-base" />
                </flux:field>

                @if(!empty($hasilPencarian))
                    <div class="absolute z-10 w-full mt-1 bg-white border-2 border-zinc-300 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                        x-show="searchOpen">
                        @foreach($hasilPencarian as $s)
                            <button wire:click="selectSiswa({{ $s['id'] }})"
                                @click="searchOpen = false"
                                class="w-full text-left px-5 py-3 text-base hover:bg-zinc-50 flex items-center justify-between transition">
                                <span class="font-medium text-zinc-900 text-base">{{ $s['nama'] }}</span>
                                <span class="text-zinc-500 text-sm">{{ $s['nis'] }} - {{ $s['kelas'] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <flux:field>
                <flux:label>{{ __('Jenis Pelanggaran') }}</flux:label>
                <flux:select wire:model="poin_pelanggaran_id" class="text-base">
                    <option value="">{{ __('-- Pilih --') }}</option>
                    @foreach($daftarPoin as $p)
                        <option value="{{ $p->id }}">{{ $p->kategori }} - {{ $p->jenis_pelanggaran }} ({{ $p->poin }})</option>
                    @endforeach
                </flux:select>
                <flux:error name="poin_pelanggaran_id" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Keterangan') }}</flux:label>
                <flux:textarea wire:model="keterangan" rows="3" placeholder="Detail pelanggaran..." class="text-base" />
                <flux:error name="keterangan" />
            </flux:field>

            <flux:button variant="primary" wire:click="simpan" class="w-full text-base py-3" icon="check">
                {{ __('Simpan Pelanggaran') }}
            </flux:button>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6 space-y-4">
            <flux:heading size="base" class="text-zinc-900 text-lg">{{ __('Pelanggaran Terbaru') }}</flux:heading>

            <div class="overflow-auto max-h-96">
                <table class="w-full text-base">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-zinc-200 sticky top-0">
                            <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Siswa') }}</th>
                            <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Jenis') }}</th>
                            <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Poin') }}</th>
                            <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Tanggal') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse($recent as $r)
                            <tr wire:key="rec-{{ $r->id }}">
                                <td class="p-4 font-medium text-zinc-900 text-base">{{ $r->user?->siswa?->nama ?? $r->user?->name ?? '-' }}</td>
                                <td class="p-4 text-zinc-600 text-base">{{ $r->poinPelanggaran?->jenis_pelanggaran ?? '-' }}</td>
                                <td class="p-4">
                                    <flux:badge color="red" >{{ $r->poinPelanggaran?->poin ?? 0 }}</flux:badge>
                                </td>
                                <td class="p-4 text-zinc-500 text-sm">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center">
                                    <flux:icon.exclamation-circle class="size-8 text-zinc-300 mx-auto mb-2" />
                                    <flux:text class="text-zinc-500 text-base">{{ __('Belum ada pelanggaran.') }}</flux:text>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
