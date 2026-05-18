<x-layouts::auth :title="__('Masuk')">
    <div class="flex flex-col gap-6">
        <div class="text-center">
            <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200">{{ __('Masuk ke Sentri Siswa') }}</h2>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Masukkan email dan password Anda') }}</p>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <flux:input
                name="email"
                :label="__('Email')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <div>
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    viewable
                />
                @if (Route::has('password.request'))
                    <div class="mt-1 text-end">
                        <a href="{{ route('password.request') }}" class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400" wire:navigate>
                            {{ __('Lupa password?') }}
                        </a>
                    </div>
                @endif
            </div>

            <flux:checkbox name="remember" :label="__('Ingat saya')" :checked="old('remember')" />

            <flux:button type="submit" variant="primary" class="w-full" data-test="login-button">
                {{ __('Masuk') }}
            </flux:button>
        </form>

        @if (Route::has('register'))
            <div class="text-center text-sm text-zinc-500 dark:text-zinc-400">
                <span>{{ __('Belum punya akun?') }}</span>
                <a href="{{ route('register') }}" class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400" wire:navigate>
                    {{ __('Daftar') }}
                </a>
            </div>
        @endif
    </div>
</x-layouts::auth>
