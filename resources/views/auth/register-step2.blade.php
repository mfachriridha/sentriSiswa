@extends('layouts.guest')

@section('title', 'Lengkapi Pendaftaran - Sentri Siswa')

@section('content')
<div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-slate-800/40 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-700/60 p-8 sm:p-10 transition-all duration-300">
            <div class="mb-8 text-center">
                <img src="{{ asset('storage/assets/logo/logo-website.png') }}" alt="Sentri Siswa"
                     class="mx-auto h-20 w-auto rounded-2xl border-4 border-slate-700/50 shadow-xl">
                <h2 class="mt-6 text-2xl font-extrabold tracking-tight text-white">Lengkapi Profil</h2>
                <p class="mt-2 text-xs font-medium text-slate-400">
                    @if ($role === 'teacher')
                        NIP <span class="font-semibold text-primary">{{ $identity }}</span> Terverifikasi
                    @else
                        NISN/NIS <span class="font-semibold text-primary">{{ $identity }}</span> Terverifikasi
                    @endif
                </p>
                @if($name)
                    <p class="mt-1 text-sm font-bold text-white">{{ $name }}</p>
                @endif
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-900/50 bg-red-950/40 backdrop-blur px-4 py-3.5">
                    <ul class="list-disc pl-4 text-xs font-medium text-red-400 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Email <span class="text-red-500">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-900/50 px-4 py-3 text-sm text-white placeholder:text-slate-500 shadow-inner focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300"
                           placeholder="nama@email.com">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Kata Sandi <span class="text-red-500">*</span></label>
                    <input id="password" type="password" name="password" required
                           class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-900/50 px-4 py-3 text-sm text-white placeholder:text-slate-500 shadow-inner focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300"
                           placeholder="Minimal 8 karakter, huruf dan angka">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Ulangi Kata Sandi <span class="text-red-500">*</span></label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-900/50 px-4 py-3 text-sm text-white placeholder:text-slate-500 shadow-inner focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300"
                           placeholder="••••••••">
                </div>

                @if ($role === 'teacher')
                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Nomor HP <span class="text-red-500">*</span></label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                               class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-900/50 px-4 py-3 text-sm text-white placeholder:text-slate-500 shadow-inner focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300"
                               placeholder="081234567890">
                        <p class="mt-1.5 text-[10px] text-slate-500 font-medium">Digunakan untuk mengirimkan notifikasi penting via WhatsApp.</p>
                    </div>
                @endif

                <button type="submit"
                        class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-[0.98] transition-all duration-300 cursor-pointer">
                    Daftar Akun
                </button>
            </form>

            {{-- Separator --}}
            <div class="my-6 flex items-center gap-3">
                <div class="flex-1 border-t border-slate-700/60"></div>
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-500">atau</span>
                <div class="flex-1 border-t border-slate-700/60"></div>
            </div>

            {{-- Google Register --}}
            <a href="{{ route('google.redirect', ['mode' => 'register']) }}"
               class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-700 bg-slate-900/40 px-4 py-3
                      text-sm font-bold text-slate-300 hover:text-white hover:border-slate-600 hover:bg-slate-900/80 active:scale-[0.98] transition-all duration-300 cursor-pointer">
                <svg class="h-5 w-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Daftar dengan Google
            </a>

            <p class="mt-8 text-center text-xs font-semibold text-slate-500">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark hover:underline transition-colors">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection