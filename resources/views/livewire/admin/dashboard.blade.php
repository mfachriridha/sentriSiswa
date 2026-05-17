<div class="p-6">
    <flux:heading size="xl" class="mb-6">{{ __('Dashboard Admin') }}</flux:heading>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-brand-100 dark:bg-brand-900">
                    <flux:icon.user-group class="size-5 text-brand-600" />
                </div>
                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('Total Siswa') }}</flux:text>
            </div>
            <flux:heading size="xl">{{ $totalSiswa }}</flux:heading>
        </flux:card>

        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900">
                    <flux:icon.check-circle class="size-5 text-green-600" />
                </div>
                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('Hadir Hari Ini') }}</flux:text>
            </div>
            <flux:heading size="xl">{{ $hadirHariIni }}</flux:heading>
        </flux:card>

        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-red-100 dark:bg-red-900">
                    <flux:icon.x-circle class="size-5 text-red-600" />
                </div>
                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('Tanpa Keterangan') }}</flux:text>
            </div>
            <flux:heading size="xl">{{ $tanpaKeterangan }}</flux:heading>
        </flux:card>

        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-amber-100 dark:bg-amber-900">
                    <flux:icon.clock class="size-5 text-amber-600" />
                </div>
                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('Pengajuan Pending') }}</flux:text>
            </div>
            <flux:heading size="xl">{{ $pengajuanPending }}</flux:heading>
        </flux:card>
    </div>
</div>
