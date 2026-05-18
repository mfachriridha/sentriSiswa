<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ __('Manajemen Kelas') }}</flux:heading>
        <flux:button variant="primary" wire:click="openCreate" icon="plus">
            {{ __('Tambah Kelas') }}
        </flux:button>
    </div>

    <flux:card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-zinc-300">
                        <th class="text-left p-3 font-semibold text-zinc-700">{{ __('Nama Kelas') }}</th>
                        <th class="text-left p-3 font-semibold text-zinc-700">{{ __('Tingkat') }}</th>
                        <th class="text-left p-3 font-semibold text-zinc-700">{{ __('Jurusan') }}</th>
                        <th class="text-right p-3 font-semibold text-zinc-700">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-300">
                    @forelse($semuaKelas as $kelas)
                        <tr class="hover:bg-zinc-50" wire:key="kelas-{{ $kelas->id }}">
                            <td class="p-3">{{ $kelas->nama }}</td>
                            <td class="p-3">{{ $kelas->tingkat }}</td>
                            <td class="p-3">{{ $kelas->jurusan }}</td>
                            <td class="p-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <flux:button size="xs" wire:click="openEdit({{ $kelas->id }})" icon="pencil-square" />
                                    <flux:button size="xs" variant="danger" wire:click="delete({{ $kelas->id }})"
                                        wire:confirm="{{ __('Yakin hapus kelas ini?') }}" icon="trash" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-zinc-500">{{ __('Belum ada data kelas.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>

    <flux:modal wire:model="showModal" class="max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ $editing ? __('Edit Kelas') : __('Tambah Kelas') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Nama Kelas') }}</flux:label>
                <flux:input wire:model="nama" placeholder="Contoh: X IPA 1" />
                <flux:error name="nama" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Tingkat') }}</flux:label>
                <flux:select wire:model="tingkat">
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </flux:select>
                <flux:error name="tingkat" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Jurusan') }}</flux:label>
                <flux:select wire:model="jurusan">
                    <option value="IPA">IPA</option>
                    <option value="IPS">IPS</option>
                    <option value="Bahasa">Bahasa</option>
                </flux:select>
                <flux:error name="jurusan" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button variant="primary" wire:click="save">{{ __('Simpan') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
