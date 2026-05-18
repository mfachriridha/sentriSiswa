<x-layouts::app.sidebar :title="__('Riwayat Pelanggaran')">
    <div class="p-6">
        <flux:heading size="xl" class="mb-2">{{ __('Riwayat Pelanggaran') }}</flux:heading>
        <flux:subheading class="mb-6">{{ __('Lihat catatan pelanggaran Anda') }}</flux:subheading>
        <div class="p-8 text-center text-brand-500 border-2 border-dashed border-brand-300 rounded-xl bg-brand-50/50">
            <flux:icon.exclamation-circle class="size-8 mx-auto mb-3 text-brand-400" />
            <flux:heading size="base" class="text-brand-700 mb-1">{{ __('Halaman Dalam Pengembangan') }}</flux:heading>
            <flux:text class="text-brand-500">{{ __('Fitur ini akan segera tersedia.') }}</flux:text>
        </div>
    </div>
</x-layouts::app.sidebar>
