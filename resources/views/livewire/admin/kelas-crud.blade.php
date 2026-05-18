<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl" class="text-zinc-900 text-2xl">{{ __('Manajemen Kelas') }}</flux:heading>
            <flux:subheading class="text-zinc-600 text-base">{{ __('Kelola data kelas sekolah') }}</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="openCreate" icon="plus" class="text-base px-5 py-2.5">
            {{ __('Tambah Kelas') }}
        </flux:button>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-base">
                <thead>
                    <tr class="bg-zinc-50 border-b border-zinc-200">
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Nama Kelas') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Tingkat') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Jurusan') }}</th>
                        <th class="text-right p-4 font-semibold text-zinc-700 text-base">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($semuaKelas as $kelas)
                        <tr class="hover:bg-zinc-50 transition" wire:key="kelas-{{ $kelas->id }}">
                            <td class="p-4 font-medium text-zinc-900 text-base">{{ $kelas->nama }}</td>
                            <td class="p-4 text-zinc-600 text-base">{{ $kelas->tingkat }}</td>
                            <td class="p-4 text-zinc-600 text-base">{{ $kelas->jurusan }}</td>
                            <td class="p-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" wire:click="openEdit({{ $kelas->id }})" icon="pencil-square" variant="ghost" />
                                    <flux:button size="sm" variant="danger" wire:click="delete({{ $kelas->id }})"
                                        wire:confirm="{{ __('Yakin hapus kelas ini?') }}" icon="trash" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-12 text-center">
                                <flux:icon.academic-cap class="size-12 text-zinc-300 mx-auto mb-3" />
                                <flux:heading size="base" class="text-zinc-500 mb-2 text-base">{{ __('Belum ada data kelas') }}</flux:heading>
                                <flux:text class="text-zinc-400 text-base">{{ __('Klik tombol "Tambah Kelas" untuk memulai') }}</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <flux:modal wire:model="showModal" class="max-w-md">
        <div class="space-y-5">
            <flux:heading size="lg" class="text-xl">{{ $editing ? __('Edit Kelas') : __('Tambah Kelas') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Nama Kelas') }}</flux:label>
                <flux:input wire:model="nama" placeholder="Contoh: X IPA 1" class="text-base" />
                <flux:error name="nama" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Tingkat') }}</flux:label>
                <flux:select wire:model="tingkat" class="text-base">
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </flux:select>
                <flux:error name="tingkat" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Jurusan') }}</flux:label>
                <flux:select wire:model="jurusan" class="text-base">
                    <option value="IPA">IPA</option>
                    <option value="IPS">IPS</option>
                    <option value="Bahasa">Bahasa</option>
                </flux:select>
                <flux:error name="jurusan" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-3">
                <flux:button wire:click="$set('showModal', false)" class="text-base px-5">{{ __('Batal') }}</flux:button>
                <flux:button variant="primary" wire:click="save" class="text-base px-5">{{ __('Simpan') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
