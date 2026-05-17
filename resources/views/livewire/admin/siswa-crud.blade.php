<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ __('Manajemen Siswa') }}</flux:heading>
        <flux:button variant="primary" wire:click="openCreate" icon="plus">
            {{ __('Tambah Siswa') }}
        </flux:button>
    </div>

    <flux:card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Nama') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('NIS') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Kelas') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Poin') }}</th>
                        <th class="text-left p-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                        <th class="text-right p-3 font-medium text-zinc-500">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($semuaSiswa as $siswa)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer"
                            wire:key="siswa-{{ $siswa->id }}" wire:click="openDetail({{ $siswa->id }})">
                            <td class="p-3">{{ $siswa->nama }}</td>
                            <td class="p-3">{{ $siswa->nis }}</td>
                            <td class="p-3">{{ $siswa->kelas?->nama ?? '-' }}</td>
                            <td class="p-3">
                                <flux:badge :color="$siswa->poin >= 0 ? 'green' : 'red'" size="sm">
                                    {{ $siswa->poin }}
                                </flux:badge>
                            </td>
                            <td class="p-3">
                                <flux:badge :color="$siswa->status_aktif === 'Aktif' ? 'green' : 'zinc'" size="sm">
                                    {{ $siswa->status_aktif }}
                                </flux:badge>
                            </td>
                            <td class="p-3 text-right">
                                <div class="flex justify-end gap-1" wire:click.stop>
                                    <flux:button size="xs" wire:click="openEdit({{ $siswa->id }})" icon="pencil-square" />
                                    <flux:button size="xs" variant="danger" wire:click="delete({{ $siswa->id }})"
                                        wire:confirm="{{ __('Yakin hapus siswa ini?') }}" icon="trash" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-zinc-500">{{ __('Belum ada data siswa.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>

    {{-- Tambah/Edit Modal --}}
    <flux:modal wire:model="showModal" class="max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ $editing ? __('Edit Siswa') : __('Tambah Siswa') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Nama') }}</flux:label>
                <flux:input wire:model="nama" placeholder="Nama lengkap" />
                <flux:error name="nama" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('NIS') }}</flux:label>
                <flux:input wire:model="nis" placeholder="Nomor Induk Siswa" />
                <flux:error name="nis" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('NISN') }}</flux:label>
                <flux:input wire:model="nisn" placeholder="Nomor Induk Siswa Nasional" />
                <flux:error name="nisn" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Jenis Kelamin') }}</flux:label>
                <flux:select wire:model="jenis_kelamin">
                    <option value="Laki-laki">{{ __('Laki-laki') }}</option>
                    <option value="Perempuan">{{ __('Perempuan') }}</option>
                </flux:select>
                <flux:error name="jenis_kelamin" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Kelas') }}</flux:label>
                <flux:select wire:model="kelas_id">
                    <option value="">{{ __('-- Pilih Kelas --') }}</option>
                    @foreach($this->daftarKelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="kelas_id" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('No HP') }}</flux:label>
                <flux:input wire:model="no_hp" placeholder="08123456789" />
                <flux:error name="no_hp" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button variant="primary" wire:click="save">{{ __('Simpan') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Detail Modal --}}
    <flux:modal wire:model="showDetailModal" class="max-w-2xl" wire:key="detail-modal">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Detail Siswa') }}</flux:heading>

            <div class="flex border-b border-zinc-200 dark:border-zinc-700">
                <button wire:click="setDetailTab('dasar')"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $detailTab === 'dasar' ? 'border-brand-600 text-brand-600' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                    {{ __('Data Dasar') }}
                </button>
                <button wire:click="setDetailTab('biodata')"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $detailTab === 'biodata' ? 'border-brand-600 text-brand-600' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                    {{ __('Biodata') }}
                </button>
                <button wire:click="setDetailTab('orangtua')"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $detailTab === 'orangtua' ? 'border-brand-600 text-brand-600' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                    {{ __('Orang Tua') }}
                </button>
            </div>

            @if($detailTab === 'dasar')
                @php $s = \App\Models\Siswa::with('kelas', 'user')->find($detailSiswaId); @endphp
                @if($s)
                    <div class="grid grid-cols-2 gap-4">
                        <div><flux:text class="text-zinc-500">{{ __('Nama') }}</flux:text><flux:text>{{ $s->nama }}</flux:text></div>
                        <div><flux:text class="text-zinc-500">{{ __('NIS') }}</flux:text><flux:text>{{ $s->nis }}</flux:text></div>
                        <div><flux:text class="text-zinc-500">{{ __('NISN') }}</flux:text><flux:text>{{ $s->nisn ?? '-' }}</flux:text></div>
                        <div><flux:text class="text-zinc-500">{{ __('Kelas') }}</flux:text><flux:text>{{ $s->kelas?->nama ?? '-' }}</flux:text></div>
                        <div><flux:text class="text-zinc-500">{{ __('Jenis Kelamin') }}</flux:text><flux:text>{{ $s->jenis_kelamin }}</flux:text></div>
                        <div><flux:text class="text-zinc-500">{{ __('No HP') }}</flux:text><flux:text>{{ $s->no_hp ?? '-' }}</flux:text></div>
                        <div><flux:text class="text-zinc-500">{{ __('Poin') }}</flux:text><flux:text>{{ $s->poin }}</flux:text></div>
                        <div><flux:text class="text-zinc-500">{{ __('Status Akun') }}</flux:text>
                            <flux:badge :color="$s->user_id ? 'green' : 'zinc'" size="sm">
                                {{ $s->user_id ? 'Aktif' : 'Belum Aktif' }}
                            </flux:badge>
                        </div>
                    </div>
                @endif

            @elseif($detailTab === 'biodata')
                <form wire:submit="saveBiodata" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Tempat Lahir') }}</flux:label>
                            <flux:input wire:model="tempat_lahir" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Tanggal Lahir') }}</flux:label>
                            <flux:input wire:model="tanggal_lahir" type="date" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Agama') }}</flux:label>
                            <flux:input wire:model="agama" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Status Keluarga') }}</flux:label>
                            <flux:input wire:model="status_keluarga" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Anak Ke') }}</flux:label>
                            <flux:input wire:model="anak_ke" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('No Telp Rumah') }}</flux:label>
                            <flux:input wire:model="no_telp_rumah" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Sekolah Asal') }}</flux:label>
                            <flux:input wire:model="sekolah_asal" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Diterima Kelas') }}</flux:label>
                            <flux:input wire:model="diterima_kelas" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Diterima Tanggal') }}</flux:label>
                            <flux:input wire:model="diterima_tanggal" type="date" />
                        </flux:field>
                    </div>
                    <flux:field>
                        <flux:label>{{ __('Alamat') }}</flux:label>
                        <flux:textarea wire:model="alamat" rows="2" />
                    </flux:field>
                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary">{{ __('Simpan Biodata') }}</flux:button>
                    </div>
                </form>

            @elseif($detailTab === 'orangtua')
                <form wire:submit="saveOrangTua" class="space-y-4">
                    <flux:heading size="base" class="text-zinc-500">{{ __('Data Ayah') }}</flux:heading>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Nama Ayah') }}</flux:label>
                            <flux:input wire:model="nama_ayah" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Pekerjaan Ayah') }}</flux:label>
                            <flux:input wire:model="pekerjaan_ayah" />
                        </flux:field>
                    </div>
                    <flux:heading size="base" class="text-zinc-500">{{ __('Data Ibu') }}</flux:heading>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Nama Ibu') }}</flux:label>
                            <flux:input wire:model="nama_ibu" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Pekerjaan Ibu') }}</flux:label>
                            <flux:input wire:model="pekerjaan_ibu" />
                        </flux:field>
                    </div>
                    <flux:field>
                        <flux:label>{{ __('Alamat Orang Tua') }}</flux:label>
                        <flux:textarea wire:model="alamat_ortu" rows="2" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('No Telp Orang Tua') }}</flux:label>
                        <flux:input wire:model="no_telp_ortu" />
                    </flux:field>
                    <flux:separator />
                    <flux:heading size="base" class="text-zinc-500">{{ __('Data Wali') }}</flux:heading>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Nama Wali') }}</flux:label>
                            <flux:input wire:model="nama_wali" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Pekerjaan Wali') }}</flux:label>
                            <flux:input wire:model="pekerjaan_wali" />
                        </flux:field>
                    </div>
                    <flux:field>
                        <flux:label>{{ __('Alamat Wali') }}</flux:label>
                        <flux:textarea wire:model="alamat_wali" rows="2" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('No Telp Wali') }}</flux:label>
                        <flux:input wire:model="no_telp_wali" />
                    </flux:field>
                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary">{{ __('Simpan Data Orang Tua') }}</flux:button>
                    </div>
                </form>
            @endif
        </div>
    </flux:modal>
</div>
