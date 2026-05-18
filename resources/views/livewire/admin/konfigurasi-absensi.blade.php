<div class="p-6">
    <flux:heading size="xl" class="mb-6">{{ __('Konfigurasi Absensi') }}</flux:heading>

    <flux:card class="max-w-md space-y-4">
        <flux:field>
            <flux:label>{{ __('Jam Mulai Absen') }}</flux:label>
            <flux:input wire:model="jam_mulai_absen" type="time" />
            <flux:error name="jam_mulai_absen" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Batas Terlambat') }}</flux:label>
            <flux:input wire:model="batas_terlambat" type="time" />
            <flux:error name="batas_terlambat" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Jam Akhir Absen') }}</flux:label>
            <flux:input wire:model="jam_akhir_absen" type="time" />
            <flux:error name="jam_akhir_absen" />
        </flux:field>

        <flux:button variant="primary" wire:click="save">
            {{ __('Simpan Konfigurasi') }}
        </flux:button>
    </flux:card>
</div>
