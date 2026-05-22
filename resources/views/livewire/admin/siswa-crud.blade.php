<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl" class="text-zinc-900 text-2xl">{{ __('Manajemen Siswa') }}</flux:heading>
            <flux:subheading class="text-zinc-700 text-base">{{ __('Kelola data siswa dan biodata') }}</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="openCreate" icon="plus" class="text-base px-5 py-2.5">
            {{ __('Tambah Siswa') }}
        </flux:button>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-base">
                <thead>
                    <tr class="bg-zinc-50 border-b border-zinc-200">
                        <th class="text-left p-4 font-semibold text-zinc-800 text-base">{{ __('Nama') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-800 text-base">{{ __('NIS') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-800 text-base">{{ __('Kelas') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-800 text-base">{{ __('Poin') }}</th>
                        <th class="text-left p-4 font-semibold text-zinc-800 text-base">{{ __('Status') }}</th>
                        <th class="text-right p-4 font-semibold text-zinc-800 text-base">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($semuaSiswa as $siswa)
                        <tr class="hover:bg-zinc-50 cursor-pointer transition"
                            wire:key="siswa-{{ $siswa->id }}" wire:click="openDetail({{ $siswa->id }})">
                            <td class="p-4 font-medium text-zinc-900 text-base">{{ $siswa->nama }}</td>
                            <td class="p-4 text-zinc-700 text-base">{{ $siswa->nis }}</td>
                            <td class="p-4 text-zinc-700 text-base">{{ $siswa->kelas?->nama ?? '-' }}</td>
                            <td class="p-4">
                                <flux:badge :color="$siswa->poin >= 0 ? 'green' : 'red'" >
                                    {{ $siswa->poin }}
                                </flux:badge>
                            </td>
                            <td class="p-4">
                                <flux:badge :color="$siswa->status_aktif === 'Aktif' ? 'green' : 'zinc'" >
                                    {{ $siswa->status_aktif }}
                                </flux:badge>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex justify-end gap-2" wire:click.stop>
                                    <flux:button size="sm" wire:click="openEdit({{ $siswa->id }})" icon="pencil-square" variant="ghost" />
                                    <flux:button size="sm" variant="danger" wire:click="delete({{ $siswa->id }})"
                                        wire:confirm="{{ __('Yakin hapus siswa ini?') }}" icon="trash" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center">
                                <flux:icon.user-group class="size-12 text-zinc-300 mx-auto mb-3" />
                                <flux:heading size="base" class="text-zinc-600 mb-2 text-base">{{ __('Belum ada data siswa') }}</flux:heading>
                                <flux:text class="text-zinc-500 text-base">{{ __('Klik tombol "Tambah Siswa" untuk memulai') }}</flux:text>
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
            <flux:heading size="lg" class="text-xl">{{ $editing ? __('Edit Siswa') : __('Tambah Siswa') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Nama') }}</flux:label>
                <flux:input wire:model="nama" placeholder="Nama lengkap" class="text-base" />
                <flux:error name="nama" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('NIS') }}</flux:label>
                <flux:input wire:model="nis" placeholder="Nomor Induk Siswa" class="text-base" />
                <flux:error name="nis" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('NISN') }}</flux:label>
                <flux:input wire:model="nisn" placeholder="Nomor Induk Siswa Nasional" class="text-base" />
                <flux:error name="nisn" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Jenis Kelamin') }}</flux:label>
                <flux:select wire:model="jenis_kelamin" class="text-base">
                    <option value="Laki-laki">{{ __('Laki-laki') }}</option>
                    <option value="Perempuan">{{ __('Perempuan') }}</option>
                </flux:select>
                <flux:error name="jenis_kelamin" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Kelas') }}</flux:label>
                <flux:select wire:model="kelas_id" class="text-base">
                    <option value="">{{ __('-- Pilih Kelas --') }}</option>
                    @foreach($this->daftarKelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="kelas_id" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('No HP') }}</flux:label>
                <flux:input wire:model="no_hp" placeholder="08123456789" class="text-base" />
                <flux:error name="no_hp" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-3">
                <flux:button wire:click="$set('showModal', false)" class="text-base px-5">{{ __('Batal') }}</flux:button>
                <flux:button variant="primary" wire:click="save" class="text-base px-5">{{ __('Simpan') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Detail Modal --}}
    <flux:modal wire:model="showDetailModal" class="max-w-2xl" wire:key="detail-modal">
        <div class="space-y-5">
            <flux:heading size="lg" class="text-xl">{{ __('Detail Siswa') }}</flux:heading>

            <div class="flex border-b border-zinc-200">
                        <button wire:click="setDetailTab('dasar')"
                            class="px-5 py-3 text-base font-medium border-b-2 transition {{ $detailTab === 'dasar' ? 'border-brand-600 text-brand-600' : 'border-transparent text-zinc-600 hover:text-zinc-800' }}">
                    {{ __('Data Dasar') }}
                </button>
                        <button wire:click="setDetailTab('biodata')"
                            class="px-5 py-3 text-base font-medium border-b-2 transition {{ $detailTab === 'biodata' ? 'border-brand-600 text-brand-600' : 'border-transparent text-zinc-600 hover:text-zinc-800' }}">
                    {{ __('Biodata') }}
                </button>
                        <button wire:click="setDetailTab('orangtua')"
                            class="px-5 py-3 text-base font-medium border-b-2 transition {{ $detailTab === 'orangtua' ? 'border-brand-600 text-brand-600' : 'border-transparent text-zinc-600 hover:text-zinc-800' }}">
                    {{ __('Orang Tua') }}
                </button>
            </div>

            @if($detailTab === 'dasar')
                @php $s = \App\Models\Siswa::with('kelas', 'user')->find($detailSiswaId); @endphp
                @if($s)
                    <div class="grid grid-cols-2 gap-5">
                        <div><flux:text class="text-zinc-700 font-medium text-base">{{ __('Nama') }}</flux:text><flux:text class="text-zinc-900 text-base">{{ $s->nama }}</flux:text></div>
                        <div><flux:text class="text-zinc-700 font-medium text-base">{{ __('NIS') }}</flux:text><flux:text class="text-zinc-900 text-base">{{ $s->nis }}</flux:text></div>
                        <div><flux:text class="text-zinc-700 font-medium text-base">{{ __('NISN') }}</flux:text><flux:text class="text-zinc-900 text-base">{{ $s->nisn ?? '-' }}</flux:text></div>
                        <div><flux:text class="text-zinc-700 font-medium text-base">{{ __('Kelas') }}</flux:text><flux:text class="text-zinc-900 text-base">{{ $s->kelas?->nama ?? '-' }}</flux:text></div>
                        <div><flux:text class="text-zinc-700 font-medium text-base">{{ __('Jenis Kelamin') }}</flux:text><flux:text class="text-zinc-900 text-base">{{ $s->jenis_kelamin }}</flux:text></div>
                        <div><flux:text class="text-zinc-700 font-medium text-base">{{ __('No HP') }}</flux:text><flux:text class="text-zinc-900 text-base">{{ $s->no_hp ?? '-' }}</flux:text></div>
                        <div><flux:text class="text-zinc-700 font-medium text-base">{{ __('Poin') }}</flux:text><flux:text class="text-zinc-900 text-base">{{ $s->poin }}</flux:text></div>
                        <div><flux:text class="text-zinc-700 font-medium text-base">{{ __('Status Akun') }}</flux:text>
                            <flux:badge :color="$s->user_id ? 'green' : 'zinc'" >
                                {{ $s->user_id ? 'Aktif' : 'Belum Aktif' }}
                            </flux:badge>
                        </div>
                    </div>
                @endif

            @elseif($detailTab === 'biodata')
                <form wire:submit="saveBiodata" class="space-y-5">
                    <div class="grid grid-cols-2 gap-5">
                        <flux:field>
                            <flux:label>{{ __('Tempat Lahir') }}</flux:label>
                            <flux:input wire:model="tempat_lahir" class="text-base" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Tanggal Lahir') }}</flux:label>
                            <flux:input wire:model="tanggal_lahir" type="date" class="text-base" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Agama') }}</flux:label>
                            <flux:input wire:model="agama" class="text-base" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Status Keluarga') }}</flux:label>
                            <flux:input wire:model="status_keluarga" class="text-base" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Anak Ke') }}</flux:label>
                            <flux:input wire:model="anak_ke" class="text-base" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('No Telp Rumah') }}</flux:label>
                            <flux:input wire:model="no_telp_rumah" class="text-base" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Sekolah Asal') }}</flux:label>
                            <flux:input wire:model="sekolah_asal" class="text-base" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Diterima Kelas') }}</flux:label>
                            <flux:input wire:model="diterima_kelas" class="text-base" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Diterima Tanggal') }}</flux:label>
                            <flux:input wire:model="diterima_tanggal" type="date" class="text-base" />
                        </flux:field>
                    </div>
                    <flux:field>
                        <flux:label>{{ __('Alamat') }}</flux:label>
                        <flux:textarea wire:model="alamat" rows="2" class="text-base" />
                    </flux:field>
                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary" class="text-base px-5">{{ __('Simpan Biodata') }}</flux:button>
                    </div>
                </form>

            @elseif($detailTab === 'orangtua')
                <form wire:submit="saveOrangTua" class="space-y-5">
                    <flux:heading size="base" class="text-zinc-700 text-lg">{{ __('Data Ayah') }}</flux:heading>
                    <div class="grid grid-cols-2 gap-5">
                        <flux:field>
                            <flux:label>{{ __('Nama Ayah') }}</flux:label>
                            <flux:input wire:model="nama_ayah" class="text-base" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Pekerjaan Ayah') }}</flux:label>
                            <flux:input wire:model="pekerjaan_ayah" class="text-base" />
                        </flux:field>
                    </div>
                    <flux:heading size="base" class="text-zinc-700 text-lg">{{ __('Data Ibu') }}</flux:heading>
                    <div class="grid grid-cols-2 gap-5">
                        <flux:field>
                            <flux:label>{{ __('Nama Ibu') }}</flux:label>
                            <flux:input wire:model="nama_ibu" class="text-base" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Pekerjaan Ibu') }}</flux:label>
                            <flux:input wire:model="pekerjaan_ibu" class="text-base" />
                        </flux:field>
                    </div>
                    <flux:field>
                        <flux:label>{{ __('Alamat Orang Tua') }}</flux:label>
                        <flux:textarea wire:model="alamat_ortu" rows="2" class="text-base" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('No Telp Orang Tua') }}</flux:label>
                        <flux:input wire:model="no_telp_ortu" class="text-base" />
                    </flux:field>
                    <flux:separator />
                    <flux:heading size="base" class="text-zinc-700 text-lg">{{ __('Data Wali') }}</flux:heading>
                    <div class="grid grid-cols-2 gap-5">
                        <flux:field>
                            <flux:label>{{ __('Nama Wali') }}</flux:label>
                            <flux:input wire:model="nama_wali" class="text-base" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Pekerjaan Wali') }}</flux:label>
                            <flux:input wire:model="pekerjaan_wali" class="text-base" />
                        </flux:field>
                    </div>
                    <flux:field>
                        <flux:label>{{ __('Alamat Wali') }}</flux:label>
                        <flux:textarea wire:model="alamat_wali" rows="2" class="text-base" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('No Telp Wali') }}</flux:label>
                        <flux:input wire:model="no_telp_wali" class="text-base" />
                    </flux:field>
                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary" class="text-base px-5">{{ __('Simpan Data Orang Tua') }}</flux:button>
                    </div>
                </form>
            @endif
        </div>
    </flux:modal>
</div>
