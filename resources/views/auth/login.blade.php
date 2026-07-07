@extends('layouts.guest')

@section('title', 'Masuk - Sentri Siswa')

@section('content')
<div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sm:p-10 transition-all duration-300">
            <div class="mb-8 text-center">
                <img src="{{ asset('storage/assets/logo/logo-website.png') }}" alt="Sentri Siswa"
                     class="mx-auto h-20 w-auto rounded-2xl border-4 border-slate-100 shadow-md">
                <h2 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-800">Sentri Siswa</h2>
                <p class="mt-2 text-xs font-semibold text-slate-500">Sistem Pengawasan Kehadiran & Tata Tertib Siswa</p>
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

            @if (session('error'))
                <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3.5">
                    <p class="text-xs font-semibold text-red-600">{{ session('error') }}</p>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 rounded-xl border border-green-100 bg-green-50 px-4 py-3.5">
                    <p class="text-xs font-semibold text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all duration-300"
                           placeholder="nama@sekolah.sch.id">
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Kata Sandi</label>
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-primary hover:text-primary-dark transition-colors">Lupa kata sandi?</a>
                    </div>
                    <input id="password" type="password" name="password" required
                           class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all duration-300"
                           placeholder="••••••••">
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-[0.98] transition-all duration-300 cursor-pointer">
                    Masuk
                </button>
            </form>

            <p class="mt-8 text-center text-xs font-semibold text-slate-400">
                Belum terdaftar? <a href="{{ route('register') }}" class="text-primary hover:text-primary-dark hover:underline transition-colors font-bold">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection