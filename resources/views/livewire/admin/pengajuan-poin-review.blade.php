<div class="p-6">
    <flux:heading size="xl" class="mb-6">{{ __('Pengajuan Poin') }}</flux:heading>

    <div class="flex gap-2 mb-4">
        @foreach(['pending' => __('Pending'), 'disetujui' => __('Disetujui'), 'ditolak' => __('Ditolak'), 'semua' => __('Semua')] as $key => $label)
            <flux:button wire:click="setFilter('{{ $key }}')"
                variant="{{ $filter === $key ? 'primary' : 'ghost' }}" size="sm">
                {{ $label }}
            </flux:button>
        @endforeach
    </div>

    <flux:card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200">
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Siswa') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Pengaju') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Poin') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Keterangan') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Catatan Admin') }}</th>
                        <th class="text-right p-3 font-medium text-zinc-500">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($daftarPengajuan as $p)
                        <tr wire:key="pengajuan-{{ $p->id }}">
                            <td class="p-3">{{ $p->user?->siswa?->nama ?? $p->user?->name ?? '-' }}</td>
                            <td class="p-3">{{ $p->guru?->nama ?? '-' }}</td>
                            <td class="p-3">
                                <flux:badge color="green" size="sm">+{{ $p->jumlah_poin }}</flux:badge>
                            </td>
                            <td class="p-3 max-w-xs truncate" title="{{ $p->keterangan }}">{{ $p->keterangan ?? '-' }}</td>
                            <td class="p-3">
                                <flux:badge
                                    :color="$p->status === 'disetujui' ? 'green' : ($p->status === 'ditolak' ? 'red' : 'amber')"
                                    size="sm">
                                    {{ ucfirst($p->status) }}
                                </flux:badge>
                            </td>
                            <td class="p-3 max-w-xs truncate" title="{{ $p->catatan_admin }}">{{ $p->catatan_admin ?? '-' }}</td>
                            <td class="p-3 text-right">
                                @if($p->status === 'pending')
                                    <div class="flex justify-end gap-1">
                                        <flux:button size="xs" variant="primary"
                                            wire:click="setujui({{ $p->id }})"
                                            wire:confirm="{{ __('Setujui pengajuan ini?') }}"
                                            icon="check" />
                                        <flux:button size="xs" variant="danger"
                                            wire:click="openTolak({{ $p->id }})"
                                            icon="x-mark" />
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-zinc-500">
                                {{ __('Tidak ada pengajuan poin.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>

    <flux:modal wire:model="showTolakModal" class="max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Tolak Pengajuan') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Alasan Penolakan') }}</flux:label>
                <flux:textarea wire:model="catatan_admin" rows="3" placeholder="Tulis alasan penolakan..." />
                <flux:error name="catatan_admin" />
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showTolakModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button variant="danger" wire:click="tolak">{{ __('Tolak') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
