@extends('layouts.guest')

@section('title', 'Masuk - SentriSiswa')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/20">
                <i class="bi bi-mortarboard-fill text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">SentriSiswa</h2>
            <p class="text-sm text-slate-500 mt-1">Pilih peran Anda untuk masuk</p>
        </div>

        <!-- Role Cards -->
        <div class="card !p-5 mb-5" id="roleSelection">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Login Sebagai</p>
            
            <div class="space-y-2.5">
                <button onclick="selectRole('kesiswaan')" class="w-full flex items-center gap-4 p-3.5 rounded-xl border-2 border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 transition-all text-left group">
                    <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                        <i class="bi bi-building-fill text-blue-600 text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-slate-800">Kesiswaan</p>
                        <p class="text-xs text-slate-500">Kelola data, role & poin</p>
                    </div>
                    <i class="bi bi-chevron-right text-slate-300 group-hover:text-blue-500 transition-colors"></i>
                </button>

                <button onclick="selectRole('walikelas')" class="w-full flex items-center gap-4 p-3.5 rounded-xl border-2 border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 transition-all text-left group">
                    <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                        <i class="bi bi-person-badge-fill text-emerald-600 text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-slate-800">Wali Kelas</p>
                        <p class="text-xs text-slate-500">Presensi & verifikasi selfie</p>
                    </div>
                    <i class="bi bi-chevron-right text-slate-300 group-hover:text-emerald-500 transition-colors"></i>
                </button>

                <button onclick="selectRole('bk')" class="w-full flex items-center gap-4 p-3.5 rounded-xl border-2 border-slate-200 hover:border-purple-400 hover:bg-purple-50/50 transition-all text-left group">
                    <div class="w-11 h-11 bg-purple-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                        <i class="bi bi-heart-pulse-fill text-purple-600 text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-slate-800">BK</p>
                        <p class="text-xs text-slate-500">Monitoring & pelanggaran</p>
                    </div>
                    <i class="bi bi-chevron-right text-slate-300 group-hover:text-purple-500 transition-colors"></i>
                </button>

                <button onclick="selectRole('guru-mapel')" class="w-full flex items-center gap-4 p-3.5 rounded-xl border-2 border-slate-200 hover:border-amber-400 hover:bg-amber-50/50 transition-all text-left group">
                    <div class="w-11 h-11 bg-amber-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                        <i class="bi bi-book-fill text-amber-600 text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-slate-800">Guru Mapel</p>
                        <p class="text-xs text-slate-500">Presensi tanpa selfie</p>
                    </div>
                    <i class="bi bi-chevron-right text-slate-300 group-hover:text-amber-500 transition-colors"></i>
                </button>

                <button onclick="selectRole('siswa')" class="w-full flex items-center gap-4 p-3.5 rounded-xl border-2 border-slate-200 hover:border-rose-400 hover:bg-rose-50/50 transition-all text-left group">
                    <div class="w-11 h-11 bg-rose-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                        <i class="bi bi-mortarboard-fill text-rose-600 text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-slate-800">Siswa</p>
                        <p class="text-xs text-slate-500">Presensi selfie & GPS</p>
                    </div>
                    <i class="bi bi-chevron-right text-slate-300 group-hover:text-rose-500 transition-colors"></i>
                </button>
            </div>
        </div>

        <!-- Login Form -->
        <div id="loginForm" class="card !p-6 hidden anim-fade">
            <div class="flex items-center gap-3 mb-6 p-3 bg-slate-50 rounded-xl">
                <div id="roleIcon" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0">
                    <i id="roleIconi" class="text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p id="roleName" class="font-bold text-sm text-slate-800"></p>
                    <p class="text-xs text-slate-500">Masukkan kredensial</p>
                </div>
                <button type="button" onclick="showRoleSelection()" class="p-1.5 hover:bg-slate-200 rounded-lg transition">
                    <i class="bi bi-arrow-left text-slate-500"></i>
                </button>
            </div>

            <form action="{{ route('auth.login.post') }}" method="POST">
                @csrf
                <input type="hidden" id="selectedRole" name="role" value="">
                
                <div class="mb-4">
                    <label for="username" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                        Username / NIP / NIS
                    </label>
                    <div class="relative">
                        <i class="bi bi-person-fill absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="username" name="username" required
                            class="input-field !pl-10"
                            placeholder="Masukkan username">
                    </div>
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <i class="bi bi-lock-fill absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password" id="password" name="password" required
                            class="input-field !pl-10 !pr-12"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 hover:bg-slate-100 rounded-lg transition">
                            <i id="eyeIcon" class="bi bi-eye text-slate-400"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-brand w-full py-3.5 text-base">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </form>

            <div class="mt-5 text-center">
                <a href="{{ route('landing') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    <i class="bi bi-arrow-left"></i> Kembali ke beranda
                </a>
            </div>
        </div>

        <!-- Register link -->
        <p class="text-center text-xs text-slate-500 mt-6">
            Belum punya akun?
            <a href="{{ route('auth.register') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Daftar di sini</a>
        </p>

    </div>
</div>

@push('scripts')
<script>
    let currentRole = '';

    const roleConfig = {
        kesiswaan:  { name: 'Kesiswaan',   color: 'blue',    icon: 'bi-building-fill' },
        bk:         { name: 'BK',           color: 'purple',  icon: 'bi-heart-pulse-fill' },
        walikelas:  { name: 'Wali Kelas',   color: 'emerald', icon: 'bi-person-badge-fill' },
        'guru-mapel': { name: 'Guru Mapel',  color: 'amber',   icon: 'bi-book-fill' },
        siswa:      { name: 'Siswa',        color: 'rose',    icon: 'bi-mortarboard-fill' }
    };

    function selectRole(role) {
        currentRole = role;
        document.getElementById('selectedRole').value = role;
        
        const c = roleConfig[role];
        document.getElementById('roleName').textContent = c.name;
        document.getElementById('roleIcon').className = `w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-${c.color}-100`;
        document.getElementById('roleIconi').className = `text-lg bi ${c.icon} text-${c.color}-600`;
        
        document.getElementById('loginForm').classList.remove('hidden');
        document.getElementById('roleSelection').classList.add('hidden');
    }

    function showRoleSelection() {
        currentRole = '';
        document.getElementById('loginForm').classList.add('hidden');
        document.getElementById('roleSelection').classList.remove('hidden');
    }

    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text'; icon.classList.replace('bi-eye','bi-eye-slash');
        } else {
            input.type = 'password'; icon.classList.replace('bi-eye-slash','bi-eye');
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        const role = new URLSearchParams(window.location.search).get('role');
        if (role && roleConfig[role]) selectRole(role);
    });
</script>
@endpush