<div class="p-6">
    <div class="mb-6">
        <flux:heading size="xl" class="text-zinc-900">{{ __('Konfigurasi Absensi') }}</flux:heading>
        <flux:subheading class="text-zinc-600">{{ __('Atur waktu absensi dan batas keterlambatan') }}</flux:subheading>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6 max-w-lg space-y-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-2 rounded-lg bg-brand-100">
                <flux:icon.clock class="size-5 text-brand-600" />
            </div>
            <flux:heading size="base" class="text-zinc-900">{{ __('Waktu Absensi') }}</flux:heading>
        </div>

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

        <flux:separator />

        <flux:button variant="primary" wire:click="save" icon="check" class="w-full">
            {{ __('Simpan Konfigurasi') }}
        </flux:button>
    </div>
</div>
