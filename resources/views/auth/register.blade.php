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
            <p class="text-xs text-slate-500 mb-5">Masukkan NIP (Guru) atau NIS / NISN (Siswa)</p>

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
                <input type="text" id="nip" class="input-field" placeholder="199209122022212018" maxlength="18" inputmode="numeric" pattern="\d*">
            </div>

            <div id="nisField" class="hidden">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">NIS atau NISN</label>
                <input type="text" id="nis" class="input-field" placeholder="Masukkan NIS atau NISN" maxlength="20" inputmode="numeric">
            </div>

            <button id="validateBtn" class="btn-primary w-full mt-5 !py-3">
                <span class="btn-text"><i class="bi bi-search"></i> Validasi Data</span>
                <span class="btn-loading hidden"><i class="bi bi-hourglass-split animate-spin"></i> Memvalidasi...</span>
            </button>

            <div id="validationResult" class="hidden mt-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                <div class="flex items-start gap-3">
                    <i class="bi bi-check-circle-fill text-emerald-500 text-xl mt-0.5"></i>
                    <div>
                        <p class="font-bold text-emerald-800 text-sm">Data Ditemukan!</p>
                        <p id="foundName" class="text-xs text-emerald-700 font-semibold"></p>
                        <p id="foundExtra" class="text-xs text-emerald-600 mt-0.5"></p>
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
        <div id="step2" class="hidden space-y-4 anim-up">
            <form id="registerForm" method="POST" action="{{ route('auth.register.post') }}">
                @csrf
                <input type="hidden" name="role" id="formRole">
                <input type="hidden" name="nip" id="formNip">
                <input type="hidden" name="nis_or_nisn" id="formNis">

                <!-- Data Valid Box -->
                <div id="dataValidBox" class="card !p-5 !bg-emerald-50 !border-emerald-200">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-6 h-6 bg-emerald-600 text-white rounded-full text-xs font-bold flex items-center justify-center"><i class="bi bi-check-lg"></i></span>
                        <h3 class="font-bold text-emerald-800">Data Divalidasi</h3>
                    </div>
                    <div id="dataGuru" class="space-y-1.5">
                        <div class="flex justify-between text-sm"><span class="text-slate-500">NIP</span><span id="dNip" class="font-mono font-bold text-slate-800"></span></div>
                        <div class="flex justify-between text-sm"><span class="text-slate-500">Nama</span><span id="dGuruName" class="font-bold text-slate-800"></span></div>
                    </div>
                    <div id="dataSiswa" class="hidden space-y-1.5">
                        <div class="flex justify-between text-sm"><span class="text-slate-500">NIS</span><span id="dNis" class="font-mono font-bold text-slate-800"></span></div>
                        <div class="flex justify-between text-sm"><span class="text-slate-500">NISN</span><span id="dNisn" class="font-mono font-bold text-slate-800"></span></div>
                        <div class="flex justify-between text-sm"><span class="text-slate-500">Nama</span><span id="dSiswaName" class="font-bold text-slate-800"></span></div>
                    </div>
                </div>

                <!-- Akun -->
                <div class="card !p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 bg-blue-600 text-white rounded-full text-xs font-bold flex items-center justify-center">2</span>
                        <h3 class="font-bold text-slate-900">Buat Akun</h3>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required class="input-field" placeholder="email@domain.com">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required class="input-field" placeholder="Minimal 8 karakter" minlength="8">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Konfirmasi Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" required class="input-field" placeholder="Ulangi password" minlength="8">
                        </div>
                    </div>
                </div>

                <!-- Kontak (Siswa only) -->
                <div id="kontakBlock" class="hidden card !p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 bg-purple-600 text-white rounded-full text-xs font-bold flex items-center justify-center">3</span>
                        <h3 class="font-bold text-slate-900">Kontak WhatsApp</h3>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Nomor WhatsApp digunakan untuk notifikasi presensi.</p>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">WhatsApp Siswa</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-semibold">+62</span>
                                <input type="tel" name="student_phone" class="input-field !pl-12" placeholder="81234567890" inputmode="numeric">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1"><i class="bi bi-check-circle text-emerald-500 mr-1"></i>Nomor aktif WhatsApp</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">WhatsApp Orang Tua/Wali</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-semibold">+62</span>
                                <input type="tel" name="parent_phone" class="input-field !pl-12" placeholder="81234567890" inputmode="numeric">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1"><i class="bi bi-check-circle text-emerald-500 mr-1"></i>Nomor aktif WhatsApp</p>
                        </div>
                    </div>
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
                <p><span class="font-semibold">Guru (NIP):</span> 196610162000121001 &rarr; Heru Suyana, S.Pd</p>
                <p><span class="font-semibold">Guru (NIP):</span> 199211262025212077 &rarr; Ayu Dewi Lestari, S.Pd</p>
                <hr class="border-[#0062a0]/20 my-1.5">
                <p><span class="font-semibold">Siswa (NIS atau NISN):</span> 3001 &rarr; Eka Putra</p>
                <p><span class="font-semibold">Siswa (NIS atau NISN):</span> 0003002 &rarr; Fitriani</p>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
@if(isset($registered) && $registered)
<div id="successModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl p-8 max-w-sm w-full shadow-2xl mx-4 anim-up text-center">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-check-circle-fill text-emerald-600 text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Pendaftaran Berhasil!</h3>
        <p class="text-sm text-slate-500 mb-6">Akun Anda sudah aktif. Silakan masuk untuk melanjutkan.</p>
        <a href="{{ route('auth.login') }}" class="btn-primary w-full !py-3">
            <i class="bi bi-box-arrow-in-right"></i> Kembali ke Masuk
        </a>
    </div>
</div>
@endif

<!-- Error Popup -->
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
        url = '{{ route("auth.register.validate-nis") }}';
        body = { nis_or_nisn: nisVal };
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
            document.getElementById('foundExtra').textContent = currentRole === 'siswa' ? 'NIS: ' + data.nis + '  ·  NISN: ' + data.nisn : '';
            document.getElementById('validationResult').classList.remove('hidden');
            document.getElementById('validationError').classList.add('hidden');

            document.getElementById('formRole').value = currentRole;
            if (currentRole === 'guru') {
                document.getElementById('formNip').value = data.nip;
                document.getElementById('formNis').value = '';
                document.getElementById('dNip').textContent = data.nip;
                document.getElementById('dGuruName').textContent = data.name;
                document.getElementById('dataGuru').classList.remove('hidden');
                document.getElementById('dataSiswa').classList.add('hidden');
                document.getElementById('kontakBlock').classList.add('hidden');
            } else {
                document.getElementById('formNis').value = data.nis;
                document.getElementById('formNip').value = '';
                document.getElementById('dNis').textContent = data.nis;
                document.getElementById('dNisn').textContent = data.nisn;
                document.getElementById('dSiswaName').textContent = data.name;
                document.getElementById('dataGuru').classList.add('hidden');
                document.getElementById('dataSiswa').classList.remove('hidden');
                document.getElementById('kontakBlock').classList.remove('hidden');
            }

            setTimeout(() => {
                document.getElementById('step1').classList.add('hidden');
                document.getElementById('step2').classList.remove('hidden');
            }, 400);
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
