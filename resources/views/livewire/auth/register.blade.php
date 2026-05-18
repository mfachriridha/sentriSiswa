<div class="w-full max-w-md mx-auto">
    <div class="mb-6 text-center">
        <flux:heading size="xl" class="mb-2 text-zinc-900">{{ __('Daftar ke Sentri Siswa') }}</flux:heading>
        <flux:subheading class="text-zinc-600">{{ __('Masukkan NIP atau NIS untuk mendaftar') }}</flux:subheading>
    </div>

    @if(!$validated)
        <div class="mb-4">
            <div class="flex rounded-lg bg-zinc-100 p-1 gap-1">
                <button
                    wire:click="setRole('siswa')"
                    class="flex-1 py-2.5 px-4 rounded-md text-sm font-semibold transition {{ $role === 'siswa' ? 'bg-white shadow text-brand-700' : 'text-zinc-500 hover:text-zinc-700' }}"
                >
                    {{ __('Siswa') }}
                </button>
                <button
                    wire:click="setRole('guru')"
                    class="flex-1 py-2.5 px-4 rounded-md text-sm font-semibold transition {{ $role === 'guru' ? 'bg-white shadow text-brand-700' : 'text-zinc-500 hover:text-zinc-700' }}"
                >
                    {{ __('Guru') }}
                </button>
            </div>
        </div>

        @if($role === 'guru')
            <div class="flex flex-col gap-4">
                <flux:field>
                    <flux:input wire:model="nip" label="{{ __('NIP') }}" placeholder="12312312 123412 1 123" autofocus />
                    <flux:error name="nip" />
                </flux:field>

                <flux:button wire:click="cekIdentitas" variant="primary" class="w-full">
                    {{ __('Cek NIP') }}
                </flux:button>
            </div>
        @else
            <div class="flex flex-col gap-4">
                <flux:field>
                    <flux:input wire:model="nis" label="{{ __('NIS') }}" placeholder="123456789" autofocus />
                    <flux:error name="nis" />
                </flux:field>

                <flux:button wire:click="cekIdentitas" variant="primary" class="w-full">
                    {{ __('Cek NIS') }}
                </flux:button>
            </div>
        @endif

    @else
        <div class="mb-6 p-4 rounded-lg bg-brand-50 border-2 border-brand-300">
            <div class="flex items-center gap-2 mb-1">
                <flux:icon.check-circle class="size-5 text-brand-600" />
                <span class="text-sm font-medium text-brand-700">{{ __('Selamat datang,') }}</span>
            </div>
            <p class="text-lg font-bold text-brand-800">{{ $nama }}</p>
        </div>

        <form wire:submit="register" class="flex flex-col gap-4">
            <flux:field>
                <flux:input wire:model="email" label="{{ __('Email') }}" type="email" required autocomplete="email" placeholder="email@example.com" autofocus />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:input wire:model="password" label="{{ __('Password') }}" type="password" required autocomplete="new-password" placeholder="{{ __('Password') }}" viewable />
                <flux:error name="password" />
            </flux:field>

            <flux:field>
                <flux:input wire:model="password_confirmation" label="{{ __('Ulangi Password') }}" type="password" required autocomplete="new-password" placeholder="{{ __('Ulangi Password') }}" viewable />
                <flux:error name="password_confirmation" />
            </flux:field>

            @if($role === 'guru')
                <flux:field>
                    <flux:input wire:model="no_hp" label="{{ __('Nomor HP (aktif WhatsApp)') }}" type="tel" required placeholder="08123456789" />
                    <flux:error name="no_hp" />
                </flux:field>
            @endif

            <flux:button type="submit" variant="primary" class="w-full mt-2">
                {{ __('Daftar') }}
            </flux:button>
        </form>

        <div class="mt-4 text-center">
            <button wire:click="setRole('{{ $role }}')" class="text-sm font-semibold text-brand-600 hover:text-brand-700 underline">
                {{ __('← Ganti NIP/NIS') }}
            </button>
        </div>
    @endif

    <div class="mt-6 text-center text-sm text-zinc-600">
        {{ __('Sudah punya akun?') }}
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">
            {{ __('Masuk') }}
        </a>
    </div>
</div>
