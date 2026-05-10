@extends('layouts.guest')

@section('title', 'Admin - SentriSiswa')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-[#001e40] rounded-lg flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i class="bi bi-shield-fill text-white text-xl"></i>
            </div>
            <h2 class="text-xl font-black text-[#001e40] tracking-tight">Admin Panel</h2>
            <p class="text-xs text-[#43474f] mt-1">Masuk ke dashboard admin</p>
        </div>

        <div class="bg-white rounded-lg border border-[#c3c6d1]/30 p-6 shadow-sm">
            @if(session('error') || $errors->any())
            <div class="mb-4 p-3 bg-[#ffdad6] border border-[#ba1a1a]/30 rounded-lg text-xs font-semibold text-[#ba1a1a] flex items-center gap-2">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') ?? $errors->first('email') }}
            </div>
            @endif
            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-[#43474f] uppercase tracking-wider mb-2">Email</label>
                    <div class="relative">
                        <i class="bi bi-envelope-fill absolute left-3.5 top-1/2 -translate-y-1/2 text-[#737780] text-sm"></i>
                        <input type="email" name="email" required class="input-field !pl-10" placeholder="admin@email.com">
                    </div>
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-bold text-[#43474f] uppercase tracking-wider mb-2">Kata Sandi</label>
                    <div class="relative">
                        <i class="bi bi-lock-fill absolute left-3.5 top-1/2 -translate-y-1/2 text-[#737780] text-sm"></i>
                        <input type="password" name="password" required class="input-field !pl-10 !pr-12" placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 hover:bg-[#edeeef] rounded transition">
                            <i id="eyeIcon" class="bi bi-eye text-[#737780]"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full py-3"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
            </form>
        </div>

        <div class="mt-6 p-3 bg-[#d0e4ff] border border-[#0062a0]/20 rounded-lg">
            <p class="text-[11px] font-bold text-[#00497a] uppercase tracking-wider">Demo Admin</p>
            <p class="text-xs text-[#00497a] mt-1">
                Email: <span class="font-semibold">admin@sentrisiswa.sch.id</span><br>
                Kata Sandi: <span class="font-semibold">admin123</span>
            </p>
        </div>

        <p class="text-center text-xs text-[#43474f] mt-6">
            <a href="{{ route('landing') }}" class="text-[#0062a0] hover:text-[#001e40] font-semibold">&larr; Kembali ke beranda</a>
        </p>
    </div>
</div>

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password') || document.querySelector('input[name="password"]');
    const icon = document.getElementById('eyeIcon');
    if (!input) return;
    if (input.type === 'password') { input.type = 'text'; icon.classList.replace('bi-eye','bi-eye-slash'); }
    else { input.type = 'password'; icon.classList.replace('bi-eye-slash','bi-eye'); }
}
</script>
@endpush
@endsection