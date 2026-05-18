<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl" class="text-zinc-900 text-2xl">{{ __('Manajemen Guru') }}</flux:heading>
            <flux:subheading class="text-zinc-600 text-base">{{ __('Kelola data guru, wali kelas, dan BK') }}</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="openCreate" icon="plus" class="text-base px-5 py-2.5">
            {{ __('Tambah Guru') }}
        </flux:button>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-base">
                <thead>
                    <tr class="bg-zinc-50 border-b border-zinc-200">
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Nama') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('NIP') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('No HP') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Status') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('Wali Kelas') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-700 text-base">{{ __('BK') }}</th>
                        <th class="text-right p-4 font-semibold text-zinc-700 text-base">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($semuaGuru as $guru)
                        <tr class="hover:bg-zinc-50 cursor-pointer transition"
                            wire:key="guru-{{ $guru->id }}" wire:click="openDetail({{ $guru->id }})">
                            <td class="p-4 font-medium text-zinc-900 text-base">{{ $guru->nama }}</td>
                            <td class="p-4 text-zinc-600 text-base">{{ $guru->nip }}</td>
                            <td class="p-4 text-zinc-600 text-base">{{ $guru->no_hp ?? '-' }}</td>
                            <td class="p-4">
                                <flux:badge :color="$guru->status_aktif === 'Aktif' ? 'green' : 'zinc'" >
                                    {{ $guru->status_aktif }}
                                </flux:badge>
                            </td>
                            <td class="p-4 text-zinc-600 text-base">{{ $guru->wali_kelas_nama }}</td>
                            <td class="p-4 text-zinc-600 text-base">{{ $guru->bk_tingkat_list }}</td>
                            <td class="p-4 text-right">
                                <div class="flex justify-end gap-2" wire:click.stop>
                                    <flux:button size="sm" wire:click="openEdit({{ $guru->id }})" icon="pencil-square" variant="ghost" />
                                    <flux:button size="sm" variant="danger" wire:click="delete({{ $guru->id }})"
                                        wire:confirm="{{ __('Yakin hapus guru ini?') }}" icon="trash" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center">
                                <flux:icon.briefcase class="size-12 text-zinc-300 mx-auto mb-3" />
                                <flux:heading size="base" class="text-zinc-500 mb-2 text-base">{{ __('Belum ada data guru') }}</flux:heading>
                                <flux:text class="text-zinc-400 text-base">{{ __('Klik tombol "Tambah Guru" untuk memulai') }}</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tambah/Edit Modal --}}
    <flux:modal wire:model="showModal" class="max-w-md">
        <div class="space-y-5">
            <flux:heading size="lg" class="text-xl">{{ $editing ? __('Edit Guru') : __('Tambah Guru') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Nama') }}</flux:label>
                <flux:input wire:model="nama" placeholder="Nama lengkap" class="text-base" />
                <flux:error name="nama" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('NIP') }}</flux:label>
                <flux:input wire:model="nip" placeholder="Nomor Induk Pegawai" class="text-base" />
                <flux:error name="nip" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-3">
                <flux:button wire:click="$set('showModal', false)" class="text-base px-5">{{ __('Batal') }}</flux:button>
                <flux:button variant="primary" wire:click="save" class="text-base px-5">{{ __('Simpan') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Detail Modal --}}
    <flux:modal wire:model="showDetailModal" class="max-w-lg" wire:key="guru-detail-modal">
        <div class="space-y-5">
            @php $g = \App\Models\Guru::with('user')->find($detailGuruId); @endphp
            @if($g)
                <flux:heading size="lg" class="text-xl">{{ __('Detail Guru') }}: {{ $g->nama }}</flux:heading>
            @endif

            <div class="flex border-b border-zinc-200">
                <button wire:click="setDetailTab('data')"
                    class="px-5 py-3 text-base font-medium border-b-2 transition {{ $detailTab === 'data' ? 'border-brand-600 text-brand-600' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                    {{ __('Data Guru') }}
                </button>
                <button wire:click="setDetailTab('walikelas')"
                    class="px-5 py-3 text-base font-medium border-b-2 transition {{ $detailTab === 'walikelas' ? 'border-brand-600 text-brand-600' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                    {{ __('Wali Kelas') }}
                </button>
                <button wire:click="setDetailTab('bk')"
                    class="px-5 py-3 text-base font-medium border-b-2 transition {{ $detailTab === 'bk' ? 'border-brand-600 text-brand-600' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                    {{ __('BK') }}
                </button>
            </div>

            @if($detailTab === 'data' && $g)
                <div class="grid grid-cols-2 gap-5">
                    <div><flux:text class="text-zinc-600 font-medium text-base">{{ __('Nama') }}</flux:text><flux:text class="text-zinc-900 text-base">{{ $g->nama }}</flux:text></div>
                    <div><flux:text class="text-zinc-600 font-medium text-base">{{ __('NIP') }}</flux:text><flux:text class="text-zinc-900 text-base">{{ $g->nip }}</flux:text></div>
                    <div><flux:text class="text-zinc-600 font-medium text-base">{{ __('No HP') }}</flux:text><flux:text class="text-zinc-900 text-base">{{ $g->no_hp ?? '-' }}</flux:text></div>
                    <div><flux:text class="text-zinc-600 font-medium text-base">{{ __('Status Akun') }}</flux:text>
                        <flux:badge :color="$g->user_id ? 'green' : 'zinc'" >
                            {{ $g->user_id ? 'Aktif' : 'Belum Aktif' }}
                        </flux:badge>
                    </div>
                </div>

            @elseif($detailTab === 'walikelas')
                <form wire:submit="saveWaliKelas" class="space-y-5">
                    <flux:field>
                        <flux:label>{{ __('Kelas') }}</flux:label>
                        <flux:select wire:model="wk_kelas_id" class="text-base">
                            <option value="">{{ __('-- Pilih Kelas --') }}</option>
                            @foreach($this->daftarKelasTersedia as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="wk_kelas_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Tahun Ajaran') }}</flux:label>
                        <flux:input wire:model="wk_tahun_ajaran" placeholder="2025/2026" class="text-base" />
                        <flux:error name="wk_tahun_ajaran" />
                    </flux:field>

                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary" class="text-base px-5">{{ __('Simpan Wali Kelas') }}</flux:button>
                    </div>
                </form>

            @elseif($detailTab === 'bk')
                <form wire:submit="saveBk" class="space-y-5">
                    <flux:text class="text-zinc-700 text-base">{{ __('Pilih tingkat yang dibimbing:') }}</flux:text>
                    <div class="space-y-3">
                        @foreach([10, 11, 12] as $t)
                            <label class="flex items-center gap-3 cursor-pointer">
                                <flux:checkbox wire:model="bk_tingkat" value="{{ $t }}" />
                                <span class="text-zinc-700 text-base">{{ __('Tingkat') }} {{ $t }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary" class="text-base px-5">{{ __('Simpan BK') }}</flux:button>
                    </div>
                </form>
            @endif
        </div>
    </flux:modal>
</div>
