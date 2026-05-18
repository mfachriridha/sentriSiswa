<div class="p-6">
    <div class="mb-6">
        <flux:heading size="xl" class="text-zinc-900 text-2xl">{{ __('Dashboard Admin') }}</flux:heading>
        <flux:subheading class="text-zinc-600 text-base">{{ __('Ringkasan data absensi dan pelanggaran') }}</flux:subheading>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-lg bg-brand-100">
                    <flux:icon.user-group class="size-6 text-brand-600" />
                </div>
                <flux:badge color="zinc" >{{ __('Total') }}</flux:badge>
            </div>
            <flux:heading size="xl" class="text-zinc-900 text-3xl">{{ $totalSiswa }}</flux:heading>
            <flux:text class="text-zinc-500 text-base mt-2">{{ __('Total siswa terdaftar') }}</flux:text>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-lg bg-green-100">
                    <flux:icon.check-circle class="size-6 text-green-600" />
                </div>
                <flux:badge color="green" >{{ __('Hadir') }}</flux:badge>
            </div>
            <flux:heading size="xl" class="text-zinc-900 text-3xl">{{ $hadirHariIni }}</flux:heading>
            <flux:text class="text-zinc-500 text-base mt-2">{{ __('Siswa hadir hari ini') }}</flux:text>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-lg bg-red-100">
                    <flux:icon.x-circle class="size-6 text-red-600" />
                </div>
                <flux:badge color="red" >{{ __('Alfa') }}</flux:badge>
            </div>
            <flux:heading size="xl" class="text-zinc-900 text-3xl">{{ $tanpaKeterangan }}</flux:heading>
            <flux:text class="text-zinc-500 text-base mt-2">{{ __('Tanpa keterangan') }}</flux:text>
        </div>

        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-lg bg-amber-100">
                    <flux:icon.clock class="size-6 text-amber-600" />
                </div>
                <flux:badge color="amber" >{{ __('Pending') }}</flux:badge>
            </div>
            <flux:heading size="xl" class="text-zinc-900 text-3xl">{{ $pengajuanPending }}</flux:heading>
            <flux:text class="text-zinc-500 text-base mt-2">{{ __('Pengajuan menunggu review') }}</flux:text>
        </div>
    </div>
</div>
