@extends('layouts.guest')

@section('title', 'Daftar - SentriSiswa')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg">

        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/20">
                <i class="bi bi-person-plus-fill text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Daftar Akun</h2>
            <p class="text-sm text-slate-500 mt-1">Buat akun SentriSiswa baru</p>
        </div>

        <!-- Step 1: Validasi -->
        <div id="step1" class="card !p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-6 h-6 bg-blue-600 text-white rounded-full text-xs font-bold flex items-center justify-center">1</span>
                <h3 class="font-bold text-slate-900">Validasi Data</h3>
            </div>
            <p class="text-xs text-slate-500 mb-5">Masukkan NIP (Guru) atau NIS &amp; NISN (Siswa)</p>

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
                <input type="text" id="nip" class="input-field" placeholder="19920912 202221 2 018" maxlength="22" inputmode="numeric">
            </div>

            <div id="nisField" class="hidden">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">NIS</label>
                <input type="text" id="nis" class="input-field" placeholder="Masukkan NIS" maxlength="20" inputmode="numeric">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block mt-3">NISN</label>
                <input type="text" id="nisn" class="input-field" placeholder="Masukkan NISN" maxlength="20" inputmode="numeric">
            </div>

            <button id="validateBtn" class="btn-primary w-full mt-5 !py-3">
                <span class="btn-text"><i class="bi bi-search"></i> Validasi Data</span>
                <span class="btn-loading hidden"><i class="bi bi-hourglass-split animate-spin"></i> Memvalidasi...</span>
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
                        <p id="errorMessage" class="text-xs text-red-600">Pastikan data yang dimasukkan benar.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Lengkapi Akun -->
        <div id="step2" class="card !p-6 hidden anim-up">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-6 h-6 bg-blue-600 text-white rounded-full text-xs font-bold flex items-center justify-center">2</span>
                <h3 class="font-bold text-slate-900">Lengkapi Akun</h3>
            </div>
            <p class="text-xs text-slate-500 mb-5">Data ditemukan: <span id="displayName" class="font-bold text-blue-600"></span></p>

            <form id="registerForm" method="POST" action="{{ route('auth.register.post') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="role" id="formRole">
                <input type="hidden" name="nip" id="formNip">
                <input type="hidden" name="nis" id="formNis">
                <input type="hidden" name="nisn" id="formNisn">

                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" required class="input-field" placeholder="email@domain.com">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required class="input-field" placeholder="Minimal 8 karakter" minlength="8">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="input-field" placeholder="Ulangi password" minlength="8">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" id="backBtn" class="btn-outline flex-1 !py-3">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </button>
                    <button type="submit" class="btn-primary flex-1 !py-3">
                        <span class="btn-text"><i class="bi bi-check-circle"></i> Daftar</span>
                        <span class="btn-loading hidden"><i class="bi bi-hourglass-split animate-spin"></i> Mendaftarkan...</span>
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">
            Sudah punya akun?
            <a href="{{ route('auth.login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Masuk sekarang</a>
        </p>

        <div class="mt-4 p-3 bg-[#d0e4ff] border border-[#0062a0]/20 rounded-lg">
            <p class="text-[11px] font-bold text-[#00497a] uppercase tracking-wider">Data Demo Registrasi</p>
            <div class="text-xs text-[#00497a] mt-1 space-y-0.5">
                <p><span class="font-semibold">Guru (NIP):</span> 19661016 200012 1 001 &rarr; Heru Suyana, S.Pd</p>
                <p><span class="font-semibold">Guru (NIP):</span> 19921126 202521 2 077 &rarr; Ayu Dewi Lestari, S.Pd</p>
                <hr class="border-[#0062a0]/20 my-1.5">
                <p><span class="font-semibold">Siswa (NIS/NISN):</span> 3001 / 0003001 &rarr; Eka Putra</p>
                <p><span class="font-semibold">Siswa (NIS/NISN):</span> 3002 / 0003002 &rarr; Fitriani</p>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
<div id="errorPopup" class="fixed inset-0 z-[200] flex items-center justify-center bg-black/30">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl mx-4 anim-up">
        <div class="text-center">
            <i class="bi bi-x-circle-fill text-red-500 text-4xl mb-3 block"></i>
            <h4 class="font-bold text-slate-900 mb-2">Gagal Mendaftar</h4>
            <p class="text-sm text-slate-600 mb-4">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </p>
            <button onclick="this.closest('#errorPopup').remove()" class="btn-primary w-full !py-2.5">Tutup</button>
        </div>
    </div>
</div>
@endif

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

function setLoading(btn, loading) {
    btn.querySelector('.btn-text').classList.toggle('hidden', loading);
    btn.querySelector('.btn-loading').classList.toggle('hidden', !loading);
    btn.disabled = loading;
}

document.getElementById('validateBtn').addEventListener('click', function() {
    const btn = this;
    setLoading(btn, true);
    document.getElementById('validationResult').classList.add('hidden');
    document.getElementById('validationError').classList.add('hidden');

    let url, body;

    if (currentRole === 'guru') {
        const nipVal = document.getElementById('nip').value.trim();
        url = '{{ route("auth.register.validate-nip") }}';
        body = { nip: nipVal };
    } else {
        const nisVal = document.getElementById('nis').value.trim();
        const nisnVal = document.getElementById('nisn').value.trim();
        url = '{{ route("auth.register.validate-nis") }}';
        body = { nis: nisVal, nisn: nisnVal };
    }

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        setLoading(btn, false);
        if (data.valid) {
            document.getElementById('foundName').textContent = data.name;
            document.getElementById('validationResult').classList.remove('hidden');
            document.getElementById('validationError').classList.add('hidden');

            document.getElementById('formRole').value = currentRole;
            if (currentRole === 'guru') {
                document.getElementById('formNip').value = data.nip;
                document.getElementById('formNis').value = '';
                document.getElementById('formNisn').value = '';
            } else {
                document.getElementById('formNis').value = data.nis;
                document.getElementById('formNisn').value = data.nisn;
                document.getElementById('formNip').value = '';
            }

            setTimeout(() => {
                document.getElementById('step1').classList.add('hidden');
                document.getElementById('step2').classList.remove('hidden');
                document.getElementById('displayName').textContent = data.name;
            }, 500);
        } else {
            document.getElementById('validationResult').classList.add('hidden');
            document.getElementById('errorMessage').textContent = data.message || 'Pastikan data yang dimasukkan benar.';
            document.getElementById('validationError').classList.remove('hidden');
        }
    })
    .catch(() => {
        setLoading(btn, false);
        document.getElementById('errorMessage').textContent = 'Gagal terhubung ke server.';
        document.getElementById('validationError').classList.remove('hidden');
    });
});

document.getElementById('backBtn').addEventListener('click', function() {
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step1').classList.remove('hidden');
    document.getElementById('validationResult').classList.add('hidden');
    document.getElementById('validationError').classList.add('hidden');
});

document.getElementById('registerForm').addEventListener('submit', function() {
    const btn = this.querySelector('[type="submit"]');
    setLoading(btn, true);
});
</script>
@endpush
@endsection
