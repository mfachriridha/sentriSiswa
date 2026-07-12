@extends('layouts.guest')

@section('title', 'Lengkapi Pendaftaran - Sentri Siswa')

@section('content')
<div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sm:p-10 transition-all duration-300">
            <div class="mb-8 text-center">
                <img src="{{ asset('storage/assets/logo/logo-website.png') }}" alt="Sentri Siswa"
                     class="mx-auto h-20 w-auto rounded-2xl border-4 border-slate-100 shadow-md">
                <h2 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-800">Lengkapi Profil</h2>
                <p class="mt-2 text-xs font-semibold text-slate-500">
                    @if ($role === 'teacher')
                        NIP <span class="font-bold text-primary">{{ $identity }}</span> Terverifikasi
                    @else
                        NISN/NIS <span class="font-bold text-primary">{{ $identity }}</span> Terverifikasi
                    @endif
                </p>
                @if($name)
                    <p class="mt-1.5 text-sm font-bold text-slate-800">{{ $name }}</p>
                @endif
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

            <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Email <span class="text-red-500">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all duration-300"
                           placeholder="nama@email.com">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Kata Sandi <span class="text-red-500">*</span></label>
                    <input id="password" type="password" name="password" required
                           class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all duration-300"
                           placeholder="Minimal 8 karakter, huruf dan angka">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Ulangi Kata Sandi <span class="text-red-500">*</span></label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all duration-300"
                           placeholder="••••••••">
                </div>

                @if ($role === 'teacher')
                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor HP <span class="text-red-500">*</span></label>
                        <input id="phone" type="text" name="telepon" value="{{ old('telepon') }}" required
                               class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all duration-300"
                               placeholder="081234567890">
                        <p class="mt-1.5 text-[10px] text-slate-400 font-medium">Digunakan untuk mengirimkan notifikasi penting via WhatsApp.</p>
                    </div>
                @endif

                <button type="submit"
                        class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-[0.98] transition-all duration-300 cursor-pointer">
                    Daftar Akun
                </button>

            </form>

            <p class="mt-8 text-center text-xs font-semibold text-slate-400">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark hover:underline transition-colors font-bold">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection