<div class="p-6">
    <div class="mb-6">
        <flux:heading size="xl" class="text-zinc-900">{{ __('Dashboard Admin') }}</flux:heading>
        <flux:subheading class="text-zinc-600">{{ __('Ringkasan data absensi dan pelanggaran') }}</flux:subheading>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-lg bg-brand-100">
                    <flux:icon.user-group class="size-5 text-brand-600" />
                </div>
                <flux:badge color="zinc" size="sm">{{ __('Total') }}</flux:badge>
            </div>
            <flux:heading size="xl" class="text-zinc-900">{{ $totalSiswa }}</flux:heading>
            <flux:text class="text-zinc-500 text-sm mt-1">{{ __('Total siswa terdaftar') }}</flux:text>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-lg bg-green-100">
                    <flux:icon.check-circle class="size-5 text-green-600" />
                </div>
                <flux:badge color="green" size="sm">{{ __('Hadir') }}</flux:badge>
            </div>
            <flux:heading size="xl" class="text-zinc-900">{{ $hadirHariIni }}</flux:heading>
            <flux:text class="text-zinc-500 text-sm mt-1">{{ __('Siswa hadir hari ini') }}</flux:text>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-lg bg-red-100">
                    <flux:icon.x-circle class="size-5 text-red-600" />
                </div>
                <flux:badge color="red" size="sm">{{ __('Alfa') }}</flux:badge>
            </div>
            <flux:heading size="xl" class="text-zinc-900">{{ $tanpaKeterangan }}</flux:heading>
            <flux:text class="text-zinc-500 text-sm mt-1">{{ __('Tanpa keterangan') }}</flux:text>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-lg bg-amber-100">
                    <flux:icon.clock class="size-5 text-amber-600" />
                </div>
                <flux:badge color="amber" size="sm">{{ __('Pending') }}</flux:badge>
            </div>
            <flux:heading size="xl" class="text-zinc-900">{{ $pengajuanPending }}</flux:heading>
            <flux:text class="text-zinc-500 text-sm mt-1">{{ __('Pengajuan menunggu review') }}</flux:text>
        </div>
    </div>
</div>
