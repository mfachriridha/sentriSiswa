<div class="p-6" x-data="{ showFoto: false, fotoUrl: '' }">
    <div class="mb-6">
        <flux:heading size="xl" class="text-zinc-900 text-2xl">{{ __('Laporan Absensi') }}</flux:heading>
        <flux:subheading class="text-zinc-600 text-base">{{ __('Lihat dan filter data absensi siswa') }}</flux:subheading>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-5 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <flux:field class="flex-1 min-w-[150px]">
                <flux:label>{{ __('Kelas') }}</flux:label>
                <flux:select wire:model.live="kelas_id" class="text-base">
                    <option value="">{{ __('Semua Kelas') }}</option>
                    @foreach($semuaKelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field class="flex-1 min-w-[150px]">
                <flux:label>{{ __('Tanggal') }}</flux:label>
                <flux:input wire:model.live="tanggal" type="date" class="text-base" />
            </flux:field>

            <flux:field class="flex-1 min-w-[150px]">
                <flux:label>{{ __('Status') }}</flux:label>
                <flux:select wire:model.live="status" class="text-base">
                    <option value="">{{ __('Semua') }}</option>
                    <option value="hadir">{{ __('Hadir') }}</option>
                    <option value="terlambat">{{ __('Terlambat') }}</option>
                    <option value="izin">{{ __('Izin') }}</option>
                    <option value="sakit">{{ __('Sakit') }}</option>
                    <option value="alfa">{{ __('Alfa') }}</option>
                </flux:select>
            </flux:field>

            <flux:button wire:click="resetFilters" variant="ghost" icon="arrow-path" class="text-base">
                {{ __('Reset') }}
            </flux:button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-base">
                <thead>
                    <tr class="bg-zinc-50 border-b border-zinc-200">
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Nama') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Kelas') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Tanggal') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Jam') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Status') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Foto') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Keterangan') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($absensi as $a)
                        <tr wire:key="absensi-{{ $a->id }}">
                            <td class="p-4 font-medium text-zinc-900 text-base">{{ $a->user?->siswa?->nama ?? $a->user?->name ?? '-' }}</td>
                            <td class="p-4 text-zinc-600 text-base">{{ $a->kelas?->nama ?? '-' }}</td>
                            <td class="p-4 text-zinc-600 text-base">{{ $a->tanggal }}</td>
                            <td class="p-4 text-zinc-600 text-base">{{ $a->jam_absen }}</td>
                            <td class="p-4">
                                <flux:badge
                                    :color="$a->status === 'hadir' ? 'green' : ($a->status === 'terlambat' ? 'amber' : ($a->status === 'izin' ? 'blue' : ($a->status === 'sakit' ? 'purple' : 'red')))"
                                    >
                                    {{ ucfirst($a->status) }}
                                </flux:badge>
                            </td>
                            <td class="p-4">
                                @if($a->foto_selfie)
                                    <button @click="fotoUrl = '{{ asset('storage/' . $a->foto_selfie) }}'; showFoto = true"
                                        class="text-brand-600 hover:text-brand-700 underline text-base font-medium">
                                        {{ __('Lihat') }}
                                    </button>
                                @else
                                    <span class="text-zinc-400 text-base">-</span>
                                @endif
                            </td>
                            <td class="p-4 max-w-xs truncate text-zinc-600 text-base" title="{{ $a->keterangan }}">{{ $a->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center">
                                <flux:icon.calendar-days class="size-12 text-zinc-300 mx-auto mb-3" />
                                <flux:heading size="base" class="text-zinc-500 mb-2 text-base">{{ __('Tidak ada data absensi') }}</flux:heading>
                                <flux:text class="text-zinc-400 text-base">{{ __('Data absensi akan muncul setelah siswa melakukan absensi') }}</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 bg-zinc-50">
            {{ $absensi->links() }}
        </div>
    </div>

    {{-- Foto Modal --}}
    <div x-show="showFoto" x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
        @click.self="showFoto = false">
        <div class="bg-white rounded-xl max-w-lg mx-4 overflow-hidden shadow-2xl">
            <div class="flex justify-between items-center p-4 border-b border-zinc-200">
                <flux:heading size="base" class="text-lg">{{ __('Foto Selfie') }}</flux:heading>
                <flux:button icon="x-mark" variant="ghost" size="sm" @click="showFoto = false" />
            </div>
            <div class="p-4">
                <img :src="fotoUrl" class="max-w-full max-h-[70vh] object-contain mx-auto rounded-lg" />
            </div>
        </div>
    </div>
</div>
