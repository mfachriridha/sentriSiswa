@extends('layouts.guest')

@section('title', 'Lupa Kata Sandi - Sentri Siswa')

@section('content')
<div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sm:p-10 transition-all duration-300">
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 shadow-md border border-primary/20">
                    <svg class="h-8 w-8 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-2xl font-extrabold text-slate-800 tracking-tight">Lupa Sandi</h2>
                <p class="mt-2 text-xs font-semibold text-slate-500">
                    Masukkan email Anda untuk menerima tautan reset kata sandi
                </p>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-xl border border-green-100 bg-green-50 p-4 text-center">
                    <svg class="mx-auto mb-2 h-8 w-8 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <p class="text-xs font-bold text-green-700">{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3.5">
                    <ul class="list-disc pl-4 text-xs font-semibold text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300"
                           placeholder="nama@email.com">
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-[0.98] transition-all duration-300 cursor-pointer">
                    Kirim Tautan Reset
                </button>
            </form>

            <p class="mt-8 text-center text-xs font-semibold text-slate-400">
                <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark hover:underline transition-colors">← Kembali ke halaman masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection
