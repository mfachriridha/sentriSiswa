@extends('layouts.guest')

@section('title', 'Daftar - SentriSiswa')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg">

        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-gradient-to-br from-[#1c6880] to-[#155066] rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-[#1c6880]/20">
                <i class="bi bi-person-plus-fill text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Daftar Akun</h2>
            <p class="text-base text-slate-500 mt-1">Buat akun SentriSiswa baru</p>
        </div>

        <!-- Step 1 -->
        <div id="step1" class="card !p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-7 h-7 bg-[#1c6880] text-white rounded-full text-sm font-bold flex items-center justify-center">1</span>
                <h3 class="font-bold text-lg text-slate-900">Validasi Data</h3>
            </div>
            <p class="text-sm text-slate-500 mb-5">Masukkan NIP (Guru) atau NIS / NISN (Siswa)</p>

            <div class="mb-4">
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 block">Pilih Role</label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="role_type" value="guru" class="peer hidden" checked>
                        <div class="border-2 border-slate-200 rounded-xl p-4 text-center peer-checked:border-[#1c6880] peer-checked:bg-[#d4e8ee] transition-all">
                            <i class="bi bi-person-badge-fill text-2xl text-slate-400 peer-checked:text-[#1c6880] block mb-1"></i>
                            <p class="font-semibold text-base">Guru</p>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="role_type" value="siswa" class="peer hidden">
                        <div class="border-2 border-slate-200 rounded-xl p-4 text-center peer-checked:border-[#1c6880] peer-checked:bg-[#d4e8ee] transition-all">
                            <i class="bi bi-mortarboard-fill text-2xl text-slate-400 peer-checked:text-[#1c6880] block mb-1"></i>
                            <p class="font-semibold text-base">Siswa</p>
                        </div>
                    </label>
                </div>
            </div>

            <div id="nipField">
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 block">NIP</label>
                <input type="text" id="nip" class="input-field" placeholder="199209122022212018" maxlength="18" inputmode="numeric" pattern="\d*" onkeydown="if(event.key==='Enter')document.getElementById('validateBtn').click()">
            </div>

            <div id="nisField" class="hidden">
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 block">NIS atau NISN</label>
                <input type="text" id="nis" class="input-field" placeholder="Masukkan NIS atau NISN" maxlength="20" inputmode="numeric" onkeydown="if(event.key==='Enter')document.getElementById('validateBtn').click()">
            </div>

            <button id="validateBtn" class="btn-primary w-full mt-5 !py-3 !text-base">
                <span class="btn-text"><i class="bi bi-search"></i> Validasi Data</span>
                <span class="btn-loading hidden"><i class="bi bi-hourglass-split animate-spin"></i> Memvalidasi...</span>
            </button>

            <div id="validationError" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-center gap-3">
                    <i class="bi bi-x-circle-fill text-red-500 text-xl"></i>
                    <div>
                        <p class="font-bold text-red-800 text-base">Data Tidak Ditemukan</p>
                        <p id="errorMessage" class="text-sm text-red-600">Pastikan data yang dimasukkan benar.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2 -->
        <div id="step2" class="hidden space-y-4 anim-up">
            <form id="registerForm" method="POST" action="{{ route('auth.register.post') }}">
                @csrf
                <input type="hidden" name="role" id="formRole">
                <input type="hidden" name="nip" id="formNip">
                <input type="hidden" name="nis_or_nisn" id="formNis">

                <div class="card !p-5 !bg-emerald-50 !border-emerald-200">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="bi bi-check-circle-fill text-emerald-600"></i>
                        <span class="text-base font-bold text-emerald-800">Data tervalidasi</span>
                    </div>
                    <p id="validSummary" class="text-sm text-emerald-700"></p>
                </div>

                <div class="card !p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-7 h-7 bg-[#1c6880] text-white rounded-full text-sm font-bold flex items-center justify-center">2</span>
                        <h3 class="font-bold text-lg text-slate-900">Buat Akun</h3>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1 block">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required class="input-field" placeholder="email@domain.com">
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1 block">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required class="input-field" placeholder="Minimal 8 karakter" minlength="8">
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1 block">Konfirmasi Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" required class="input-field" placeholder="Ulangi password" minlength="8">
                        </div>
                    </div>
                </div>

                <div id="kontakBlock" class="hidden card !p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-7 h-7 bg-purple-600 text-white rounded-full text-sm font-bold flex items-center justify-center">3</span>
                        <h3 class="font-bold text-lg text-slate-900">Kontak</h3>
                    </div>
                    <p class="text-sm text-slate-500 mb-4">Nomor HP aktif WhatsApp untuk notifikasi presensi.</p>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1 block">Nomor HP Siswa</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-semibold">+62</span>
                                <input type="tel" name="student_phone" class="input-field !pl-12" placeholder="81234567890" inputmode="numeric">
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1 block">Nomor HP Orang Tua/Wali</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-semibold">+62</span>
                                <input type="tel" name="parent_phone" class="input-field !pl-12" placeholder="81234567890" inputmode="numeric">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" id="backBtn" class="btn-outline flex-1 !py-3 !text-base"><i class="bi bi-arrow-left"></i> Kembali</button>
                    <button type="submit" class="btn-primary flex-1 !py-3 !text-base">
                        <span class="btn-text"><i class="bi bi-check-circle"></i> Daftar</span>
                        <span class="btn-loading hidden"><i class="bi bi-hourglass-split animate-spin"></i> Mendaftarkan...</span>
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-sm text-slate-500 mt-6">
            Sudah punya akun? <a href="{{ route('auth.login') }}" class="text-[#1c6880] hover:text-[#155066] font-semibold">Masuk sekarang</a>
        </p>

        <div class="mt-4 p-4 bg-[#d4e8ee] border border-[#1c6880]/20 rounded-lg">
            <p class="text-xs font-bold text-[#155066] uppercase tracking-wider">Data Demo Registrasi</p>
            <div class="text-sm text-[#155066] mt-1 space-y-0.5">
                <p><span class="font-semibold">Guru (NIP):</span> 196610162000121001 &rarr; Heru Suyana, S.Pd</p>
                <p><span class="font-semibold">Guru (NIP):</span> 199211262025212077 &rarr; Ayu Dewi Lestari, S.Pd</p>
                <hr class="border-[#1c6880]/20 my-1.5">
                <p><span class="font-semibold">Siswa (NIS atau NISN):</span> 3001 &rarr; Eka Putra</p>
                <p><span class="font-semibold">Siswa (NIS atau NISN):</span> 0003002 &rarr; Fitriani</p>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
@if(isset($registered) && $registered)
<div class="fixed inset-0 z-[200] flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl p-8 max-w-sm w-full shadow-2xl mx-4 anim-up text-center">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-check-circle-fill text-emerald-600 text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Pendaftaran Berhasil!</h3>
        <p class="text-base text-slate-500 mb-6">Akun Anda sudah aktif. Silakan masuk untuk melanjutkan.</p>
        <a href="{{ route('auth.login') }}" class="btn-primary w-full !py-3 !text-base"><i class="bi bi-box-arrow-in-right"></i> Kembali ke Masuk</a>
    </div>
</div>
@endif

@if($errors->any())
<div class="fixed inset-0 z-[200] flex items-center justify-center bg-black/30">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl mx-4 anim-up text-center">
        <i class="bi bi-x-circle-fill text-red-500 text-4xl mb-3 block"></i>
        <h4 class="font-bold text-lg text-slate-900 mb-2">Gagal Mendaftar</h4>
        <p class="text-sm text-slate-600 mb-4">
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </p>
        <button onclick="this.closest('.fixed').remove()" class="btn-primary w-full !py-2.5">Tutup</button>
    </div>
</div>
@endif

<!-- Toast -->
<div id="toast" class="hidden fixed top-4 right-4 z-[200] px-4 py-3 rounded-lg shadow-lg text-sm font-semibold text-white anim-up"></div>

@push('scripts')
<script>
const roleRadios = document.querySelectorAll('input[name="role_type"]');
let currentRole = 'guru';

roleRadios.forEach(r => r.addEventListener('change', function() {
    currentRole = this.value;
    document.getElementById('nipField').classList.toggle('hidden', currentRole !== 'guru');
    document.getElementById('nisField').classList.toggle('hidden', currentRole !== 'siswa');
}));

function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'fixed top-4 right-4 z-[200] px-4 py-3 rounded-lg shadow-lg text-sm font-semibold text-white anim-up ' + (type === 'success' ? 'bg-emerald-600' : 'bg-red-600');
    t.classList.remove('hidden');
    setTimeout(() => t.classList.add('hidden'), 2500);
}

function setLoading(btn, loading) {
    const t = btn.querySelector('.btn-text');
    const l = btn.querySelector('.btn-loading');
    if (t) t.classList.toggle('hidden', loading);
    if (l) l.classList.toggle('hidden', !loading);
    btn.disabled = loading;
}

document.getElementById('validateBtn').addEventListener('click', function() {
    const btn = this;
    setLoading(btn, true);
    document.getElementById('validationError').classList.add('hidden');

    let url, body;
    if (currentRole === 'guru') {
        const v = document.getElementById('nip').value.trim();
        if (!v) { setLoading(btn, false); return; }
        url = '{{ route("auth.register.validate-nip") }}';
        body = { nip: v };
    } else {
        const v = document.getElementById('nis').value.trim();
        if (!v) { setLoading(btn, false); return; }
        url = '{{ route("auth.register.validate-nis") }}';
        body = { nis_or_nisn: v };
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
            document.getElementById('formRole').value = currentRole;
            if (currentRole === 'guru') {
                document.getElementById('formNip').value = data.nip;
                document.getElementById('formNis').value = '';
                document.getElementById('validSummary').textContent = data.name + ' · NIP ' + data.nip;
                document.getElementById('kontakBlock').classList.add('hidden');
            } else {
                document.getElementById('formNis').value = data.nis;
                document.getElementById('formNip').value = '';
                document.getElementById('validSummary').textContent = data.name + ' · NIS ' + data.nis + ' · NISN ' + data.nisn;
                document.getElementById('kontakBlock').classList.remove('hidden');
            }
            showToast('Data berhasil ditemukan!', 'success');
            setTimeout(() => {
                document.getElementById('step1').classList.add('hidden');
                document.getElementById('step2').classList.remove('hidden');
            }, 300);
        } else {
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
    document.getElementById('validationError').classList.add('hidden');
});

document.getElementById('registerForm').addEventListener('submit', function() {
    setLoading(this.querySelector('[type="submit"]'), true);
});
</script>
@endpush
@endsection
