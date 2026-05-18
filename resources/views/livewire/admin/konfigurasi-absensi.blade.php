<div class="p-6">
    <div class="mb-6">
        <flux:heading size="xl" class="text-zinc-900 text-2xl">{{ __('Konfigurasi Absensi') }}</flux:heading>
        <flux:subheading class="text-zinc-600 text-base">{{ __('Atur waktu absensi dan batas keterlambatan') }}</flux:subheading>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6 max-w-lg space-y-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-3 rounded-lg bg-brand-100">
                <flux:icon.clock class="size-6 text-brand-600" />
            </div>
            <flux:heading size="base" class="text-zinc-900 text-lg">{{ __('Waktu Absensi') }}</flux:heading>
        </div>

        <flux:field>
            <flux:label>{{ __('Jam Mulai Absen') }}</flux:label>
            <flux:input wire:model="jam_mulai_absen" type="time" class="text-base" />
            <flux:error name="jam_mulai_absen" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Batas Terlambat') }}</flux:label>
            <flux:input wire:model="batas_terlambat" type="time" class="text-base" />
            <flux:error name="batas_terlambat" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Jam Akhir Absen') }}</flux:label>
            <flux:input wire:model="jam_akhir_absen" type="time" class="text-base" />
            <flux:error name="jam_akhir_absen" />
        </flux:field>

        <flux:separator />

        <flux:button variant="primary" wire:click="save" icon="check" class="w-full text-base py-3">
            {{ __('Simpan Konfigurasi') }}
        </flux:button>
    </div>
</div>
