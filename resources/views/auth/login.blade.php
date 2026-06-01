@extends('layouts.guest')

@section('title', 'Masuk - Sentri Siswa')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="mb-6 text-center">
                <img src="{{ asset('storage/assets/logo/logo-website.png') }}" alt="Sentri Siswa"
                     class="mx-auto h-24 w-auto rounded-xl border-4 border-white shadow-lg">
                <h2 class="mt-6 text-2xl font-bold text-gray-900">Sentri Siswa</h2>
                <p class="mt-2 text-sm text-gray-500">Sistem Pengawasan Absensi</p>
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

            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                    <p class="text-sm text-red-600">{{ session('error') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                                  transition-colors">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                    <input id="password" type="password" name="password" required
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                                  transition-colors">
                </div>

                <button type="submit"
                        class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                               hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                               transition-colors">
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Belum terdaftar? <a href="{{ route('register') }}" class="font-medium text-primary hover:underline">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection