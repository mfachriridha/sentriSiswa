<div class="w-full max-w-md mx-auto">
    <div class="mb-6 text-center">
        <flux:heading size="xl" class="mb-2">{{ __('Daftar Akun') }}</flux:heading>
        <flux:subheading>{{ __('Masukkan NIP atau NIS untuk mendaftar') }}</flux:subheading>
    </div>

    @if(!$validated)
        <div class="mb-4">
            <div class="flex rounded-lg bg-zinc-100 dark:bg-zinc-800 p-1 gap-1">
                <button
                    wire:click="setRole('siswa')"
                    class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition {{ $role === 'siswa' ? 'bg-white dark:bg-zinc-700 shadow-sm text-brand-700' : 'text-zinc-500 hover:text-zinc-700' }}"
                >
                    {{ __('Siswa') }}
                </button>
                <button
                    wire:click="setRole('guru')"
                    class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition {{ $role === 'guru' ? 'bg-white dark:bg-zinc-700 shadow-sm text-brand-700' : 'text-zinc-500 hover:text-zinc-700' }}"
                >
                    {{ __('Guru') }}
                </button>
            </div>
        </div>

        @if($role === 'guru')
            <div class="flex flex-col gap-4">
                <flux:input
                    wire:model="nip"
                    label="{{ __('NIP') }}"
                    placeholder="12312312 123412 1 123"
                    autofocus
                />
                @error('nip')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror

                <flux:button wire:click="cekIdentitas" variant="primary" class="w-full">
                    {{ __('Cek NIP') }}
                </flux:button>
            </div>
        @else
            <div class="flex flex-col gap-4">
                <flux:input
                    wire:model="nis"
                    label="{{ __('NIS') }}"
                    placeholder="123456789"
                    autofocus
                />
                @error('nis')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror

                <flux:button wire:click="cekIdentitas" variant="primary" class="w-full">
                    {{ __('Cek NIS') }}
                </flux:button>
            </div>
        @endif

    @else
        <div class="mb-6 p-4 rounded-lg bg-brand-50 dark:bg-brand-950 border border-brand-200 dark:border-brand-800">
            <div class="flex items-center gap-2 mb-1">
                <flux:icon.check-circle class="text-brand-600" />
                <span class="text-sm text-brand-700 dark:text-brand-300 font-medium">{{ __('Selamat datang,') }}</span>
            </div>
            <p class="text-lg font-semibold text-brand-800 dark:text-brand-200">{{ $nama }}</p>
        </div>

        <form wire:submit="register" class="flex flex-col gap-4">
            <flux:input
                wire:model="email"
                label="{{ __('Email') }}"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
                autofocus
            />
            @error('email')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror

            <flux:input
                wire:model="password"
                label="{{ __('Password') }}"
                type="password"
                required
                autocomplete="new-password"
                placeholder="{{ __('Password') }}"
                viewable
            />

            <flux:input
                wire:model="password_confirmation"
                label="{{ __('Ulangi Password') }}"
                type="password"
                required
                autocomplete="new-password"
                placeholder="{{ __('Ulangi Password') }}"
                viewable
            />
            @error('password')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror

            @if($role === 'guru')
                <flux:input
                    wire:model="no_hp"
                    label="{{ __('Nomor HP (aktif WhatsApp)') }}"
                    type="tel"
                    required
                    placeholder="08123456789"
                />
                @error('no_hp')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            @endif

            <flux:button type="submit" variant="primary" class="w-full mt-2">
                {{ __('Daftar') }}
            </flux:button>
        </form>

        <div class="mt-4 text-center">
            <button wire:click="setRole('{{ $role }}')" class="text-sm text-brand-600 hover:text-brand-700">
                {{ __('← Ganti NIP/NIS') }}
            </button>
        </div>
    @endif

    <div class="mt-6 text-center text-sm text-zinc-500">
        {{ __('Sudah punya akun?') }}
        <a href="{{ route('login') }}" class="text-brand-600 hover:text-brand-700 font-medium">
            {{ __('Masuk') }}
        </a>
    </div>
</div>
