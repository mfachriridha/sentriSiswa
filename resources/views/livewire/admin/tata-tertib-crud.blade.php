<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ __('Tata Tertib') }}</flux:heading>
        <flux:button variant="primary" wire:click="openUpload" icon="arrow-up-tray">
            {{ __('Upload PDF') }}
        </flux:button>
    </div>

    <flux:card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200">
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Judul') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Tanggal Upload') }}</th>
                        <th class="text-right p-3 font-medium text-zinc-500">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($daftarTataTertib as $tt)
                        <tr wire:key="tt-{{ $tt->id }}">
                            <td class="p-3">{{ $tt->judul }}</td>
                            <td class="p-3">{{ $tt->created_at->format('d M Y') }}</td>
                            <td class="p-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ asset('storage/' . $tt->file_pdf) }}" target="_blank">
                                        <flux:button size="xs" icon="eye" />
                                    </a>
                                    <flux:button size="xs" variant="danger" wire:click="delete({{ $tt->id }})"
                                        wire:confirm="{{ __('Yakin hapus file ini?') }}" icon="trash" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-6 text-center text-zinc-500">{{ __('Belum ada file tata tertib.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>

    <flux:modal wire:model="showModal" class="max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Upload Tata Tertib') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Judul') }}</flux:label>
                <flux:input wire:model="judul" placeholder="Judul tata tertib" />
                <flux:error name="judul" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('File PDF') }}</flux:label>
                <flux:input type="file" wire:model="file_pdf" accept=".pdf" />
                <flux:error name="file_pdf" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button variant="primary" wire:click="upload">{{ __('Upload') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
