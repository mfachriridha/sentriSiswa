<div class="p-6">
    <flux:heading size="xl" class="mb-6">{{ __('Import Data') }}</flux:heading>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <flux:card class="space-y-4">
            <flux:heading size="base">{{ __('Template CSV') }}</flux:heading>
            <flux:text class="text-zinc-500">
                {{ __('Unduh template CSV yang sesuai, isi data, lalu upload melalui form di samping.') }}
            </flux:text>
            <div class="flex gap-2">
                <flux:button variant="outline" icon="arrow-down-tray" wire:click="downloadTemplate('guru')">
                    {{ __('Template Guru') }}
                </flux:button>
                <flux:button variant="outline" icon="arrow-down-tray" wire:click="downloadTemplate('siswa')">
                    {{ __('Template Siswa') }}
                </flux:button>
            </div>
            <div class="mt-2 text-xs text-zinc-400">
                <p>{{ __('Format Guru: Nama;nip') }}</p>
                <p>{{ __('Format Siswa: Nama;Jenis Kelamin;Kelas;NIS;NISN') }}</p>
            </div>
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="base">{{ __('Upload CSV') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Tipe Data') }}</flux:label>
                <flux:select wire:model="tipe">
                    <option value="guru">{{ __('Guru') }}</option>
                    <option value="siswa">{{ __('Siswa') }}</option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('File CSV') }}</flux:label>
                <flux:input type="file" wire:model="file" accept=".csv,.txt" />
                <flux:error name="file" />
            </flux:field>

            @if($file)
                <flux:button variant="primary" wire:click="processImport" :loading="$processing">
                    {{ __('Proses Import') }}
                </flux:button>
            @endif

            @if($result)
                <flux:separator />
                <div class="space-y-2">
                    <div class="flex gap-4">
                        <flux:badge color="green">{{ __('Sukses: :count', ['count' => $result['success']]) }}</flux:badge>
                        <flux:badge color="red">{{ __('Gagal: :count', ['count' => $result['failed']]) }}</flux:badge>
                    </div>
                    @if(!empty($result['errors']))
                        <div class="max-h-48 overflow-y-auto space-y-1 mt-2">
                            @foreach($result['errors'] as $error)
                                <div class="text-xs text-red-600 bg-red-50 p-2 rounded">
                                    {{ $error }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </flux:card>
    </div>
</div>
