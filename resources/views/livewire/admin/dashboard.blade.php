<div class="p-6">
    <flux:heading size="xl" class="mb-6 text-zinc-900">{{ __('Dashboard Admin') }}</flux:heading>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-brand-100">
                    <flux:icon.user-group class="size-5 text-brand-600" />
                </div>
                <flux:text class="text-zinc-800 font-medium">{{ __('Total Siswa') }}</flux:text>
            </div>
            <flux:heading size="xl" class="text-zinc-900">{{ $totalSiswa }}</flux:heading>
        </flux:card>

        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-green-100">
                    <flux:icon.check-circle class="size-5 text-green-600" />
                </div>
                <flux:text class="text-zinc-800 font-medium">{{ __('Hadir Hari Ini') }}</flux:text>
            </div>
            <flux:heading size="xl" class="text-zinc-900">{{ $hadirHariIni }}</flux:heading>
        </flux:card>

        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-red-100">
                    <flux:icon.x-circle class="size-5 text-red-600" />
                </div>
                <flux:text class="text-zinc-800 font-medium">{{ __('Tanpa Keterangan') }}</flux:text>
            </div>
            <flux:heading size="xl" class="text-zinc-900">{{ $tanpaKeterangan }}</flux:heading>
        </flux:card>

        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-amber-100">
                    <flux:icon.clock class="size-5 text-amber-600" />
                </div>
                <flux:text class="text-zinc-800 font-medium">{{ __('Pengajuan Pending') }}</flux:text>
            </div>
            <flux:heading size="xl" class="text-zinc-900">{{ $pengajuanPending }}</flux:heading>
        </flux:card>
    </div>
</div>
