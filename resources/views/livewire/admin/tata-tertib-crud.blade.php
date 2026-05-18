<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl" class="text-zinc-900 text-2xl">{{ __('Tata Tertib') }}</flux:heading>
            <flux:subheading class="text-zinc-600 text-base">{{ __('Kelola dokumen tata tertib sekolah') }}</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="openUpload" icon="arrow-up-tray" class="text-base px-5 py-2.5">
            {{ __('Upload PDF') }}
        </flux:button>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-base">
                <thead>
                    <tr class="bg-zinc-50 border-b border-zinc-200">
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Judul') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Tanggal Upload') }}</th>
                        <th class="text-right p-4 font-semibold text-zinc-700 text-base">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($daftarTataTertib as $tt)
                        <tr wire:key="tt-{{ $tt->id }}">
                            <td class="p-4 font-medium text-zinc-900 text-base">{{ $tt->judul }}</td>
                            <td class="p-4 text-zinc-600 text-base">{{ $tt->created_at->format('d M Y') }}</td>
                            <td class="p-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ asset('storage/' . $tt->file_pdf) }}" target="_blank">
                                        <flux:button size="sm" icon="eye" variant="ghost" />
                                    </a>
                                    <flux:button size="sm" variant="danger" wire:click="delete({{ $tt->id }})"
                                        wire:confirm="{{ __('Yakin hapus file ini?') }}" icon="trash" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-12 text-center">
                                <flux:icon.document-text class="size-12 text-zinc-300 mx-auto mb-3" />
                                <flux:heading size="base" class="text-zinc-500 mb-2 text-base">{{ __('Belum ada file tata tertib') }}</flux:heading>
                                <flux:text class="text-zinc-400 text-base">{{ __('Klik tombol "Upload PDF" untuk menambahkan') }}</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <flux:modal wire:model="showModal" class="max-w-md">
        <div class="space-y-5">
            <flux:heading size="lg" class="text-xl">{{ __('Upload Tata Tertib') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Judul') }}</flux:label>
                <flux:input wire:model="judul" placeholder="Judul tata tertib" class="text-base" />
                <flux:error name="judul" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('File PDF') }}</flux:label>
                <flux:input type="file" wire:model="file_pdf" accept=".pdf" class="text-base" />
                <flux:error name="file_pdf" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-3">
                <flux:button wire:click="$set('showModal', false)" class="text-base px-5">{{ __('Batal') }}</flux:button>
                <flux:button variant="primary" wire:click="upload" class="text-base px-5">{{ __('Upload') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
