<div class="p-6">
    <div class="mb-6">
        <flux:heading size="xl" class="text-zinc-900 text-2xl">{{ __('Import Data') }}</flux:heading>
        <flux:subheading class="text-zinc-700 text-base">{{ __('Upload data guru dan siswa melalui file CSV') }}</flux:subheading>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-3 rounded-lg bg-brand-100">
                    <flux:icon.arrow-down-tray class="size-6 text-brand-600" />
                </div>
                <flux:heading size="base" class="text-zinc-900 text-lg">{{ __('Template CSV') }}</flux:heading>
            </div>
            <flux:text class="text-zinc-600 text-base">
                {{ __('Unduh template CSV yang sesuai, isi data, lalu upload melalui form di samping.') }}
            </flux:text>
            <div class="flex gap-3">
                <flux:button variant="outline" icon="arrow-down-tray" wire:click="downloadTemplate('guru')" class="text-base">
                    {{ __('Template Guru') }}
                </flux:button>
                <flux:button variant="outline" icon="arrow-down-tray" wire:click="downloadTemplate('siswa')" class="text-base">
                    {{ __('Template Siswa') }}
                </flux:button>
            </div>
            <div class="mt-2 p-4 bg-zinc-50 rounded-lg text-base text-zinc-700 space-y-2">
                <p><strong class="text-zinc-800">{{ __('Format Guru:') }}</strong> Nama;nip</p>
                <p><strong class="text-zinc-800">{{ __('Format Siswa:') }}</strong> Nama;Jenis Kelamin;Kelas;NIS;NISN</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-3 rounded-lg bg-green-100">
                    <flux:icon.arrow-up-tray class="size-6 text-green-600" />
                </div>
                <flux:heading size="base" class="text-zinc-900 text-lg">{{ __('Upload CSV') }}</flux:heading>
            </div>

            <flux:field>
                <flux:label>{{ __('Tipe Data') }}</flux:label>
                <flux:select wire:model="tipe" class="text-base">
                    <option value="guru">{{ __('Guru') }}</option>
                    <option value="siswa">{{ __('Siswa') }}</option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('File CSV') }}</flux:label>
                <flux:input type="file" wire:model="file" accept=".csv,.txt" class="text-base" />
                <flux:error name="file" />
            </flux:field>

            @if($file)
                <flux:button variant="primary" wire:click="processImport" :loading="$processing" icon="arrow-up-tray" class="text-base">
                    {{ __('Proses Import') }}
                </flux:button>
            @endif

            @if($result)
                <flux:separator />
                <div class="space-y-3">
                    <div class="flex gap-4">
                        <flux:badge color="green" >{{ __('Sukses: :count', ['count' => $result['success']]) }}</flux:badge>
                        <flux:badge color="red" >{{ __('Gagal: :count', ['count' => $result['failed']]) }}</flux:badge>
                    </div>
                    @if(!empty($result['errors']))
                        <div class="max-h-48 overflow-y-auto space-y-2 mt-2">
                            @foreach($result['errors'] as $error)
                                <div class="text-base text-red-700 bg-red-50 border border-red-200 p-3 rounded-lg">
                                    {{ $error }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
