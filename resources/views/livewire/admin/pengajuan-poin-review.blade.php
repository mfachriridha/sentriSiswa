<div class="p-6">
    <div class="mb-6">
        <flux:heading size="xl" class="text-zinc-900 text-2xl">{{ __('Pengajuan Poin') }}</flux:heading>
        <flux:subheading class="text-zinc-700 text-base">{{ __('Review pengajuan poin dari guru') }}</flux:subheading>
    </div>

    <div class="flex gap-3 mb-5">
        @foreach(['pending' => __('Pending'), 'disetujui' => __('Disetujui'), 'ditolak' => __('Ditolak'), 'semua' => __('Semua')] as $key => $label)
            <flux:button wire:click="setFilter('{{ $key }}')"
                variant="{{ $filter === $key ? 'primary' : 'ghost' }}" class="text-base">
                {{ $label }}
            </flux:button>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-base">
                <thead>
                    <tr class="bg-zinc-50 border-b border-zinc-200">
                        <th class="text-left p-4 font-semibold text-zinc-800 text-base">{{ __('Siswa') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-800 text-base">{{ __('Pengaju') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-800 text-base">{{ __('Poin') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-800 text-base">{{ __('Keterangan') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-800 text-base">{{ __('Status') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-800 text-base">{{ __('Catatan Admin') }}</th>
                        <th class="text-right p-4 font-semibold text-zinc-800 text-base">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($daftarPengajuan as $p)
                        <tr wire:key="pengajuan-{{ $p->id }}">
                            <td class="p-4 font-medium text-zinc-900 text-base">{{ $p->user?->siswa?->nama ?? $p->user?->name ?? '-' }}</td>
                            <td class="p-4 text-zinc-700 text-base">{{ $p->guru?->nama ?? '-' }}</td>
                            <td class="p-4">
                                <flux:badge color="green" >+{{ $p->jumlah_poin }}</flux:badge>
                            </td>
                            <td class="p-4 max-w-xs truncate text-zinc-700 text-base" title="{{ $p->keterangan }}">{{ $p->keterangan ?? '-' }}</td>
                            <td class="p-4">
                                <flux:badge
                                    :color="$p->status === 'disetujui' ? 'green' : ($p->status === 'ditolak' ? 'red' : 'amber')"
                                    >
                                    {{ ucfirst($p->status) }}
                                </flux:badge>
                            </td>
                            <td class="p-4 max-w-xs truncate text-zinc-700 text-base" title="{{ $p->catatan_admin }}">{{ $p->catatan_admin ?? '-' }}</td>
                            <td class="p-4 text-right">
                                @if($p->status === 'pending')
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="sm" variant="primary"
                                            wire:click="setujui({{ $p->id }})"
                                            wire:confirm="{{ __('Setujui pengajuan ini?') }}"
                                            icon="check" />
                                        <flux:button size="sm" variant="danger"
                                            wire:click="openTolak({{ $p->id }})"
                                            icon="x-mark" />
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center">
                                <flux:icon.clipboard-document-check class="size-12 text-zinc-300 mx-auto mb-3" />
                                <flux:heading size="base" class="text-zinc-600 mb-2 text-base">{{ __('Tidak ada pengajuan poin') }}</flux:heading>
                                <flux:text class="text-zinc-500 text-base">{{ __('Pengajuan dari guru akan muncul di sini') }}</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <flux:modal wire:model="showTolakModal" class="max-w-md">
        <div class="space-y-5">
            <flux:heading size="lg" class="text-xl">{{ __('Tolak Pengajuan') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Alasan Penolakan') }}</flux:label>
                <flux:textarea wire:model="catatan_admin" rows="3" placeholder="Tulis alasan penolakan..." class="text-base" />
                <flux:error name="catatan_admin" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:button wire:click="$set('showTolakModal', false)" class="text-base px-5">{{ __('Batal') }}</flux:button>
                <flux:button variant="danger" wire:click="tolak" class="text-base px-5">{{ __('Tolak') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
