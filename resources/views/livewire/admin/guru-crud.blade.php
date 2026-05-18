<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ __('Manajemen Guru') }}</flux:heading>
        <flux:button variant="primary" wire:click="openCreate" icon="plus">
            {{ __('Tambah Guru') }}
        </flux:button>
    </div>

    <flux:card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-zinc-300">
                        <th class="text-left p-3 font-semibold text-zinc-700">{{ __('Nama') }}</th>
                        <th class="text-left p-3 font-semibold text-zinc-700">{{ __('NIP') }}</th>
                        <th class="text-left p-3 font-semibold text-zinc-700">{{ __('No HP') }}</th>
                        <th class="text-left p-3 font-semibold text-zinc-700">{{ __('Status') }}</th>
                        <th class="text-left p-3 font-semibold text-zinc-700">{{ __('Wali Kelas') }}</th>
                        <th class="text-left p-3 font-semibold text-zinc-700">{{ __('BK') }}</th>
                        <th class="text-right p-3 font-semibold text-zinc-700">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-300">
                    @forelse($semuaGuru as $guru)
                        <tr class="hover:bg-zinc-50 cursor-pointer"
                            wire:key="guru-{{ $guru->id }}" wire:click="openDetail({{ $guru->id }})">
                            <td class="p-3">{{ $guru->nama }}</td>
                            <td class="p-3">{{ $guru->nip }}</td>
                            <td class="p-3">{{ $guru->no_hp ?? '-' }}</td>
                            <td class="p-3">
                                <flux:badge :color="$guru->status_aktif === 'Aktif' ? 'green' : 'zinc'" size="sm">
                                    {{ $guru->status_aktif }}
                                </flux:badge>
                            </td>
                            <td class="p-3">{{ $guru->wali_kelas_nama }}</td>
                            <td class="p-3">{{ $guru->bk_tingkat_list }}</td>
                            <td class="p-3 text-right">
                                <div class="flex justify-end gap-1" wire:click.stop>
                                    <flux:button size="xs" wire:click="openEdit({{ $guru->id }})" icon="pencil-square" />
                                    <flux:button size="xs" variant="danger" wire:click="delete({{ $guru->id }})"
                                        wire:confirm="{{ __('Yakin hapus guru ini?') }}" icon="trash" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-zinc-500">{{ __('Belum ada data guru.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>

    {{-- Tambah/Edit Modal --}}
    <flux:modal wire:model="showModal" class="max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ $editing ? __('Edit Guru') : __('Tambah Guru') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Nama') }}</flux:label>
                <flux:input wire:model="nama" placeholder="Nama lengkap" />
                <flux:error name="nama" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('NIP') }}</flux:label>
                <flux:input wire:model="nip" placeholder="Nomor Induk Pegawai" />
                <flux:error name="nip" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button variant="primary" wire:click="save">{{ __('Simpan') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Detail Modal --}}
    <flux:modal wire:model="showDetailModal" class="max-w-lg" wire:key="guru-detail-modal">
        <div class="space-y-4">
            @php $g = \App\Models\Guru::with('user')->find($detailGuruId); @endphp
            @if($g)
                <flux:heading size="lg">{{ __('Detail Guru') }}: {{ $g->nama }}</flux:heading>
            @endif

            <div class="flex border-b-2 border-zinc-300">
                <button wire:click="setDetailTab('data')"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $detailTab === 'data' ? 'border-brand-600 text-brand-600' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                    {{ __('Data Guru') }}
                </button>
                <button wire:click="setDetailTab('walikelas')"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $detailTab === 'walikelas' ? 'border-brand-600 text-brand-600' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                    {{ __('Wali Kelas') }}
                </button>
                <button wire:click="setDetailTab('bk')"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $detailTab === 'bk' ? 'border-brand-600 text-brand-600' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                    {{ __('BK') }}
                </button>
            </div>

            @if($detailTab === 'data' && $g)
                <div class="grid grid-cols-2 gap-4">
                    <div><flux:text class="text-zinc-600 font-medium">{{ __('Nama') }}</flux:text><flux:text>{{ $g->nama }}</flux:text></div>
                    <div><flux:text class="text-zinc-600 font-medium">{{ __('NIP') }}</flux:text><flux:text>{{ $g->nip }}</flux:text></div>
                    <div><flux:text class="text-zinc-600 font-medium">{{ __('No HP') }}</flux:text><flux:text>{{ $g->no_hp ?? '-' }}</flux:text></div>
                    <div><flux:text class="text-zinc-600 font-medium">{{ __('Status Akun') }}</flux:text>
                        <flux:badge :color="$g->user_id ? 'green' : 'zinc'" size="sm">
                            {{ $g->user_id ? 'Aktif' : 'Belum Aktif' }}
                        </flux:badge>
                    </div>
                </div>

            @elseif($detailTab === 'walikelas')
                <form wire:submit="saveWaliKelas" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Kelas') }}</flux:label>
                        <flux:select wire:model="wk_kelas_id">
                            <option value="">{{ __('-- Pilih Kelas --') }}</option>
                            @foreach($this->daftarKelasTersedia as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="wk_kelas_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Tahun Ajaran') }}</flux:label>
                        <flux:input wire:model="wk_tahun_ajaran" placeholder="2025/2026" />
                        <flux:error name="wk_tahun_ajaran" />
                    </flux:field>

                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary">{{ __('Simpan Wali Kelas') }}</flux:button>
                    </div>
                </form>

            @elseif($detailTab === 'bk')
                <form wire:submit="saveBk" class="space-y-4">
                    <flux:text>{{ __('Pilih tingkat yang dibimbing:') }}</flux:text>
                    <div class="space-y-2">
                        @foreach([10, 11, 12] as $t)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <flux:checkbox wire:model="bk_tingkat" value="{{ $t }}" />
                                <span>{{ __('Tingkat') }} {{ $t }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary">{{ __('Simpan BK') }}</flux:button>
                    </div>
                </form>
            @endif
        </div>
    </flux:modal>
</div>
