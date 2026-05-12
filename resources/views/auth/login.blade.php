@extends('layouts.guest')

@section('title', 'Masuk - SentriSiswa')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-sm">

        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-[#1c6880] rounded-lg flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i class="bi bi-mortarboard-fill text-white text-xl"></i>
            </div>
            <h2 class="text-xl font-black text-[#1c6880] tracking-tight">Masuk</h2>
            <p class="text-xs text-[#43474f] mt-1">Masuk ke akun SentriSiswa Anda</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-lg border border-[#c3c6d1]/30 p-6 shadow-sm">
            @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-700 flex items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error') || $errors->any())
            <div class="mb-4 p-3 bg-[#ffdad6] border border-[#ba1a1a]/30 rounded-lg text-xs font-semibold text-[#ba1a1a] flex items-center gap-2">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') ?? $errors->first('email') }}
            </div>
            @endif
            <form action="{{ route('auth.login.post') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="email" class="block text-xs font-bold text-[#43474f] uppercase tracking-wider mb-2">
                        Email
                    </label>
                    <div class="relative">
                        <i class="bi bi-envelope-fill absolute left-3.5 top-1/2 -translate-y-1/2 text-[#737780] text-sm"></i>
                        <input type="email" id="email" name="email" required autocomplete="email"
                            class="input-field !pl-10"
                            placeholder="contoh@email.com">
                    </div>
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-xs font-bold text-[#43474f] uppercase tracking-wider mb-2">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <i class="bi bi-lock-fill absolute left-3.5 top-1/2 -translate-y-1/2 text-[#737780] text-sm"></i>
                        <input type="password" id="password" name="password" required
                            class="input-field !pl-10 !pr-12"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 hover:bg-[#edeeef] rounded transition">
                            <i id="eyeIcon" class="bi bi-eye text-[#737780]"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full py-3">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </form>
        </div>

        <!-- Daftar Link -->
        <p class="text-center text-xs text-[#43474f] mt-6">
            Belum punya akun?
            <a href="{{ route('auth.register') }}" class="text-[#0062a0] hover:text-[#1c6880] font-bold">Daftar di sini</a>
        </p>

        <!-- Info Demo -->
        <div class="mt-6 p-3 bg-[#d0e4ff] border border-[#0062a0]/20 rounded-lg">
            <p class="text-[11px] font-bold text-[#00497a] uppercase tracking-wider">Akun Demo</p>
            <div class="text-xs text-[#00497a] mt-1 space-y-0.5">
                <p><span class="font-semibold">Admin:</span> admin@sentrisiswa.sch.id / admin123</p>
                <p><span class="font-semibold">Wali Kelas 1:</span> ahmad.dahlan@sentrisiswa.sch.id / password123</p>
                <p><span class="font-semibold">Wali Kelas 2:</span> siti.aminah@sentrisiswa.sch.id / password123</p>
                <p><span class="font-semibold">BK:</span> rudi.hartono@sentrisiswa.sch.id / password123</p>
                <p><span class="font-semibold">Siswa:</span> andi.pratama@sentrisiswa.sch.id / password123</p>
                <hr class="border-[#0062a0]/20 my-1.5">
                <p class="text-[10px] opacity-70">Guru belum daftar: heru.suyana@ / ayu.dewi@ (daftar via NIP 18 digit)</p>
                <p class="text-[10px] opacity-70">Siswa belum daftar: NIS 3001, 3002, 3003</p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text'; icon.classList.replace('bi-eye','bi-eye-slash');
        } else {
            input.type = 'password'; icon.classList.replace('bi-eye-slash','bi-eye');
        }
    }
</script>
@endpush
@endsection