<x-layouts::app.sidebar :title="__('Dashboard Guru')">
    <div class="p-6">
        <flux:heading size="xl" class="mb-2">{{ __('Dashboard Guru') }}</flux:heading>
        <flux:subheading class="mb-6">{{ __('Selamat datang, :name', ['name' => auth()->user()->name]) }}</flux:subheading>
        <div class="p-8 text-center text-brand-500 border-2 border-dashed border-brand-300 rounded-xl bg-brand-50/50">
            <flux:icon.information-circle class="size-8 mx-auto mb-3 text-brand-400" />
            <flux:heading size="base" class="text-brand-700 mb-1">{{ __('Halaman Dalam Pengembangan') }}</flux:heading>
            <flux:text class="text-brand-500">{{ __('Fitur ini akan segera tersedia.') }}</flux:text>
        </div>
    </div>
</x-layouts::app.sidebar>
