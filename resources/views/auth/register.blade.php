@extends('layouts.guest')

@section('title', 'Daftar - SentriSiswa')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg">

        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/20">
                <i class="bi bi-person-plus-fill text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Daftar Akun</h2>
            <p class="text-sm text-slate-500 mt-1">Buat akun SentriSiswa baru</p>
        </div>

        <!-- Step 1: Validasi NIP/NIS -->
        <div id="step1" class="card !p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-6 h-6 bg-blue-600 text-white rounded-full text-xs font-bold flex items-center justify-center">1</span>
                <h3 class="font-bold text-slate-900">Validasi Data</h3>
            </div>
            <p class="text-xs text-slate-500 mb-5">Masukkan NIP (Guru) atau NIS/NISN (Siswa) untuk memulai pendaftaran</p>

            <div class="mb-4">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Pilih Role</label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="role_type" value="guru" class="peer hidden" checked>
                        <div class="border-2 border-slate-200 rounded-xl p-4 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                            <i class="bi bi-person-badge-fill text-2xl text-slate-400 peer-checked:text-blue-600 block mb-1"></i>
                            <p class="font-semibold text-sm">Guru</p>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="role_type" value="siswa" class="peer hidden">
                        <div class="border-2 border-slate-200 rounded-xl p-4 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                            <i class="bi bi-mortarboard-fill text-2xl text-slate-400 peer-checked:text-blue-600 block mb-1"></i>
                            <p class="font-semibold text-sm">Siswa</p>
                        </div>
                    </label>
                </div>
            </div>

            <div id="nipField">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">NIP</label>
                <input type="text" id="nip" class="input-field" placeholder="Masukkan NIP">
            </div>

            <div id="nisField" class="hidden">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">NIS / NISN</label>
                <input type="text" id="nis" class="input-field" placeholder="Masukkan NIS atau NISN">
            </div>

            <button id="validateBtn" class="btn-primary w-full mt-5 !py-3">
                <i class="bi bi-search"></i> Validasi Data
            </button>

            <div id="validationResult" class="hidden mt-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                <div class="flex items-center gap-3">
                    <i class="bi bi-check-circle-fill text-emerald-500 text-xl"></i>
                    <div>
                        <p class="font-bold text-emerald-800 text-sm">Data Ditemukan!</p>
                        <p id="foundName" class="text-xs text-emerald-700"></p>
                    </div>
                </div>
            </div>

            <div id="validationError" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-center gap-3">
                    <i class="bi bi-x-circle-fill text-red-500 text-xl"></i>
                    <div>
                        <p class="font-bold text-red-800 text-sm">Data Tidak Ditemukan</p>
                        <p class="text-xs text-red-600">Pastikan NIP/NIS yang dimasukkan benar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Form Lengkap -->
        <div id="step2" class="card !p-6 hidden anim-fade">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-6 h-6 bg-blue-600 text-white rounded-full text-xs font-bold flex items-center justify-center">2</span>
                <h3 class="font-bold text-slate-900">Lengkapi Data</h3>
            </div>
            <p class="text-xs text-slate-500 mb-5">Data ditemukan: <span id="displayName" class="font-bold text-blue-600"></span></p>

            <form id="registerForm" class="space-y-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Username</label>
                    <input type="text" id="username" required class="input-field" placeholder="Pilih username">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Email</label>
                    <input type="email" id="email" required class="input-field" placeholder="email@domain.com">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Password</label>
                    <input type="password" id="regPassword" required class="input-field" placeholder="Minimal 8 karakter">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Konfirmasi Password</label>
                    <input type="password" id="confirmPassword" required class="input-field" placeholder="Ulangi password">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Nomor WhatsApp Aktif</label>
                    <input type="tel" id="whatsapp" required class="input-field" placeholder="08xx-xxxx-xxxx">
                </div>
                <div id="parentWhatsappField" class="hidden">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">WhatsApp Orang Tua/Wali</label>
                    <input type="tel" id="parentWhatsapp" class="input-field" placeholder="08xx-xxxx-xxxx">
                    <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk notifikasi presensi</p>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" id="backBtn" class="btn-outline flex-1 !py-3">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </button>
                    <button type="submit" class="btn-primary flex-1 !py-3">
                        <i class="bi bi-check-circle"></i> Daftar
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">
            Sudah punya akun?
            <a href="{{ route('auth.login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Masuk sekarang</a>
        </p>
    </div>
</div>

@push('scripts')
<script>
    const roleRadios = document.querySelectorAll('input[name="role_type"]');
    const nipField = document.getElementById('nipField');
    const nisField = document.getElementById('nisField');
    let currentRole = 'guru';

    roleRadios.forEach(r => r.addEventListener('change', function() {
        currentRole = this.value;
        if (currentRole === 'guru') { nipField.classList.remove('hidden'); nisField.classList.add('hidden'); }
        else { nipField.classList.add('hidden'); nisField.classList.remove('hidden'); }
    }));

    document.getElementById('validateBtn').addEventListener('click', function() {
        const mockData = {'1234567890':'Budi Santoso, S.Pd','0987654321':'Siti Nurhaliza, M.Pd','S001':'Ahmad Fauzi','S002':'Nurul Hidayah'};
        const input = currentRole==='guru' ? document.getElementById('nip').value : document.getElementById('nis').value;
        if (mockData[input]) {
            document.getElementById('validationResult').classList.remove('hidden');
            document.getElementById('validationError').classList.add('hidden');
            document.getElementById('foundName').textContent = mockData[input];
            setTimeout(() => {
                document.getElementById('step1').classList.add('hidden');
                document.getElementById('step2').classList.remove('hidden');
                document.getElementById('displayName').textContent = mockData[input];
                if (currentRole==='siswa') document.getElementById('parentWhatsappField').classList.remove('hidden');
            }, 800);
        } else {
            document.getElementById('validationResult').classList.add('hidden');
            document.getElementById('validationError').classList.remove('hidden');
        }
    });

    document.getElementById('backBtn').addEventListener('click', function() {
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step1').classList.remove('hidden');
        document.getElementById('validationResult').classList.add('hidden');
    });

    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('UI Only: Registrasi berhasil!');
    });
</script>
@endpush
@endsection