@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="bi bi-shield-check text-white text-4xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Sentri Siswa</h2>
            <p class="mt-2 text-sm text-gray-600">Masuk ke akun Anda</p>
        </div>

        <!-- Login Form -->
        <div class="card">
            <form id="loginForm" class="space-y-6">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-person-fill mr-1"></i> Username
                    </label>
                    <input type="text" id="username" name="username" required
                        class="input-field"
                        placeholder="Masukkan username">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-lock-fill mr-1"></i> Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="input-field pr-12"
                            placeholder="Masukkan password">
                        <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="remember" class="ml-2 text-sm text-gray-600">Ingat saya</label>
                    </div>
                    <a href="{{ route('auth.register') }}" class="text-sm text-blue-600 hover:text-blue-800">Butuh akun?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary w-full py-3 text-base inline-flex items-center justify-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('auth.register') }}" class="font-medium text-blue-600 hover:text-blue-800">Register sekarang</a>
                </p>
            </div>
        </div>

        <!-- Role Info -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                <i class="bi bi-info-circle"></i> Sistem akan otomatis mendeteksi role Anda (Siswa/Guru)
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });

    // Form submission (UI only - no backend)
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('UI Only: Login berhasil! (Backend belum diimplementasi)');
    });
</script>
@endpush
@endsection
