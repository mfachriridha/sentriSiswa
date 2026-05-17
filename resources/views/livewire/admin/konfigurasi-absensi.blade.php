<div class="p-6" x-data="{ showToast: false, toastMessage: '' }"
    @saved.window="showToast = true; toastMessage = '{{ __('Konfigurasi berhasil disimpan.') }}'; setTimeout(() => showToast = false, 3000)">
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

        <div x-show="showToast" x-transition
            class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm">
            <span x-text="toastMessage"></span>
        </div>
    </flux:card>
</div>
