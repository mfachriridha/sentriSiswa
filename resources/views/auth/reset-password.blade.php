@extends('layouts.guest')

@section('title', 'Reset Kata Sandi - Sentri Siswa')

@section('content')
<div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sm:p-10 transition-all duration-300">
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 shadow-md border border-primary/20">
                    <svg class="h-8 w-8 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-2xl font-extrabold text-slate-800 tracking-tight">Buat Kata Sandi Baru</h2>
                <p class="mt-2 text-xs font-semibold text-slate-500">Masukkan kata sandi baru Anda</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3.5">
                    <ul class="list-disc pl-4 text-xs font-semibold text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $email ?? '') }}" required autofocus
                           class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300"
                           placeholder="nama@email.com">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Kata Sandi Baru</label>
                    <input id="password" type="password" name="password" required
                           class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300"
                           placeholder="Minimal 8 karakter">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Konfirmasi Sandi Baru</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300"
                           placeholder="Ulangi sandi baru">
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-[0.98] transition-all duration-300 cursor-pointer">
                    Perbarui Kata Sandi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
