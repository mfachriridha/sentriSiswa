<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl" class="text-zinc-900">{{ __('Poin Pelanggaran') }}</flux:heading>
            <flux:subheading class="text-zinc-600">{{ __('Kelola kategori dan jenis pelanggaran') }}</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="openCreate" icon="plus">
            {{ __('Tambah') }}
        </flux:button>
    </div>

    @foreach($categories as $cat)
        @php $items = $grouped->get($cat, collect()); @endphp
        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm mb-4 overflow-hidden" wire:key="cat-{{ $cat }}">
            <button wire:click="toggleCategory('{{ $cat }}')"
                class="w-full flex items-center justify-between p-4 hover:bg-zinc-50 transition">
                <div class="flex items-center gap-3">
                    <flux:icon.chevron-right class="size-4 text-zinc-400 transition-transform {{ in_array($cat, $expandedCategories) ? 'rotate-90' : '' }}" />
                    <flux:heading size="base" class="text-zinc-900">
                        {{ $cat }}
                    </flux:heading>
                    <flux:badge color="zinc" size="sm">{{ $items->count() }}</flux:badge>
                </div>
            </button>

            @if(in_array($cat, $expandedCategories))
                <div class="border-t border-zinc-200">
                    @if($items->count())
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-zinc-200">
                                @foreach($items as $item)
                                    <tr class="hover:bg-zinc-50 transition" wire:key="poin-{{ $item->id }}">
                                        <td class="p-4 text-zinc-700">{{ $item->jenis_pelanggaran }}</td>
                                        <td class="p-4 text-center">
                                            <flux:badge color="red" size="sm">{{ $item->poin }} {{ __('poin') }}</flux:badge>
                                        </td>
                                        <td class="p-4 text-right">
                                            <div class="flex justify-end gap-1">
                                                <flux:button size="xs" wire:click="openEdit({{ $item->id }})" icon="pencil-square" variant="ghost" />
                                                <flux:button size="xs" variant="danger" wire:click="delete({{ $item->id }})"
                                                    wire:confirm="{{ __('Yakin hapus?') }}" icon="trash" />
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-8 text-center">
                            <flux:icon.exclamation-triangle class="size-8 text-zinc-300 mx-auto mb-2" />
                            <flux:text class="text-zinc-500">{{ __('Belum ada data di kategori ini.') }}</flux:text>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endforeach

    <flux:modal wire:model="showModal" class="max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ $editing ? __('Edit Poin Pelanggaran') : __('Tambah Poin Pelanggaran') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Kategori') }}</flux:label>
                <flux:select wire:model="kategori">
                    <option value="Ringan">{{ __('Ringan') }}</option>
                    <option value="Sedang">{{ __('Sedang') }}</option>
                    <option value="Berat">{{ __('Berat') }}</option>
                    <option value="Amat Berat">{{ __('Amat Berat') }}</option>
                </flux:select>
                <flux:error name="kategori" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Jenis Pelanggaran') }}</flux:label>
                <flux:input wire:model="jenis_pelanggaran" placeholder="Deskripsi pelanggaran" />
                <flux:error name="jenis_pelanggaran" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Poin') }}</flux:label>
                <flux:input wire:model="poin" type="number" min="1" />
                <flux:error name="poin" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button variant="primary" wire:click="save">{{ __('Simpan') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
