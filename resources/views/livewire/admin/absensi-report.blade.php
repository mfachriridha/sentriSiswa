<div class="p-6" x-data="{ showFoto: false, fotoUrl: '' }">
    <flux:heading size="xl" class="mb-6">{{ __('Laporan Absensi') }}</flux:heading>

    <flux:card class="mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <flux:field class="flex-1 min-w-[150px]">
                <flux:label>{{ __('Kelas') }}</flux:label>
                <flux:select wire:model.live="kelas_id">
                    <option value="">{{ __('Semua Kelas') }}</option>
                    @foreach($semuaKelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field class="flex-1 min-w-[150px]">
                <flux:label>{{ __('Tanggal') }}</flux:label>
                <flux:input wire:model.live="tanggal" type="date" />
            </flux:field>

            <flux:field class="flex-1 min-w-[150px]">
                <flux:label>{{ __('Status') }}</flux:label>
                <flux:select wire:model.live="status">
                    <option value="">{{ __('Semua') }}</option>
                    <option value="hadir">{{ __('Hadir') }}</option>
                    <option value="terlambat">{{ __('Terlambat') }}</option>
                    <option value="izin">{{ __('Izin') }}</option>
                    <option value="sakit">{{ __('Sakit') }}</option>
                    <option value="alfa">{{ __('Alfa') }}</option>
                </flux:select>
            </flux:field>

            <flux:button wire:click="resetFilters" variant="ghost" size="sm">
                {{ __('Reset Filter') }}
            </flux:button>
        </div>
    </flux:card>

    <flux:card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Nama') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Kelas') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Tanggal') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Jam') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Foto') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Keterangan') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($absensi as $a)
                        <tr wire:key="absensi-{{ $a->id }}">
                            <td class="p-3">{{ $a->user?->siswa?->nama ?? $a->user?->name ?? '-' }}</td>
                            <td class="p-3">{{ $a->kelas?->nama ?? '-' }}</td>
                            <td class="p-3">{{ $a->tanggal }}</td>
                            <td class="p-3">{{ $a->jam_absen }}</td>
                            <td class="p-3">
                                <flux:badge
                                    :color="$a->status === 'hadir' ? 'green' : ($a->status === 'terlambat' ? 'amber' : ($a->status === 'izin' ? 'blue' : ($a->status === 'sakit' ? 'purple' : 'red')))"
                                    size="sm">
                                    {{ ucfirst($a->status) }}
                                </flux:badge>
                            </td>
                            <td class="p-3">
                                @if($a->foto_selfie)
                                    <button @click="fotoUrl = '{{ asset('storage/' . $a->foto_selfie) }}'; showFoto = true"
                                        class="text-brand-600 hover:text-brand-700 underline text-xs">
                                        {{ __('Lihat') }}
                                    </button>
                                @else
                                    <span class="text-zinc-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="p-3 max-w-xs truncate">{{ $a->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-zinc-500">{{ __('Tidak ada data absensi.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-zinc-200 dark:border-zinc-700">
            {{ $absensi->links() }}
        </div>
    </flux:card>

    {{-- Foto Modal --}}
    <div x-show="showFoto" x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
        @click.self="showFoto = false">
        <div class="bg-white dark:bg-zinc-900 rounded-xl max-w-lg mx-4 overflow-hidden shadow-2xl">
            <div class="flex justify-end p-2">
                <flux:button icon="x-mark" variant="ghost" size="sm" @click="showFoto = false" />
            </div>
            <img :src="fotoUrl" class="max-w-full max-h-[70vh] object-contain mx-auto" />
        </div>
    </div>
</div>
