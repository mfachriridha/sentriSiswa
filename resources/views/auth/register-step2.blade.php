@extends('layouts.guest')

@section('title', 'Lengkapi Pendaftaran - Sentri Siswa')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="mb-8 text-center">
                <img src="{{ asset('storage/assets/logo/logo-website.png') }}" alt="Sentri Siswa"
                     class="mx-auto h-24 w-auto rounded-xl border-4 border-white shadow-lg">
                <h2 class="mt-6 text-2xl font-bold text-gray-900">Lengkapi Pendaftaran</h2>
                <p class="mt-2 text-sm text-gray-500">
                    @if ($role === 'teacher')
                        Verifikasi NIP <span class="font-semibold">{{ $identity }}</span> berhasil
                    @else
                        Verifikasi NISN/NIS <span class="font-semibold">{{ $identity }}</span> berhasil
                    @endif
                </p>
                @if($name)
                    <p class="mt-1 text-base font-semibold text-gray-900">{{ $name }}</p>
                @endif
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                    <ul class="list-disc pl-4 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                                  transition-colors"
                           placeholder="contoh@email.com">
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi <span class="text-red-500">*</span></label>
                    <input id="password" type="password" name="password" required
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                                  transition-colors"
                           placeholder="Minimal 8 karakter, huruf dan angka">
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Ulangi Kata Sandi <span class="text-red-500">*</span></label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                                  transition-colors">
                    @error('password_confirmation')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if ($role === 'teacher')
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Nomor HP <span class="text-red-500">*</span></label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm
                                      placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                                      transition-colors"
                               placeholder="081234567890">
                        <p class="mt-1 text-xs text-gray-500">Digunakan untuk notifikasi WhatsApp.</p>
                        @error('phone')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <button type="submit"
                        class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                               hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                               transition-colors">
                    Daftar
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Sudah punya akun? <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection