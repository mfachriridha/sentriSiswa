@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-lg">
        <!-- Logo -->
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="bi bi-person-plus text-white text-4xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Register</h2>
            <p class="mt-2 text-sm text-gray-600">Daftar akun Sentri Siswa</p>
        </div>

        <!-- Step 1: Validasi NIP/NIS -->
        <div id="step1" class="card">
            <h3 class="text-lg font-semibold mb-4">
                <i class="bi bi-check-circle text-blue-600"></i> Langkah 1: Validasi Data
            </h3>
            <p class="text-sm text-gray-600 mb-6">Masukkan NIP (Guru) atau NIS/NISN (Siswa) untuk memulai pendaftaran</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Role</label>
                <div class="flex gap-4">
                    <label class="flex-1">
                        <input type="radio" name="role_type" value="guru" class="peer hidden" checked>
                        <div class="border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer peer-checked:border-blue-600 peer-checked:bg-blue-50 transition">
                            <i class="bi bi-person-badge text-2xl text-gray-600 peer-checked:text-blue-600"></i>
                            <p class="mt-2 font-medium text-sm">Guru</p>
                        </div>
                    </label>
                    <label class="flex-1">
                        <input type="radio" name="role_type" value="siswa" class="peer hidden">
                        <div class="border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer peer-checked:border-blue-600 peer-checked:bg-blue-50 transition">
                            <i class="bi bi-mortarboard text-2xl text-gray-600 peer-checked:text-blue-600"></i>
                            <p class="mt-2 font-medium text-sm">Siswa</p>
                        </div>
                    </label>
                </div>
            </div>

            <div id="nipField">
                <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-person-badge mr-1"></i> NIP
                </label>
                <input type="text" id="nip" class="input-field" placeholder="Masukkan NIP">
            </div>

            <div id="nisField" class="hidden">
                <label for="nis" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-mortarboard mr-1"></i> NIS / NISN
                </label>
                <input type="text" id="nis" class="input-field" placeholder="Masukkan NIS atau NISN">
            </div>

            <button id="validateBtn" class="btn-primary w-full mt-6 py-3 inline-flex items-center justify-center gap-2">
                <i class="bi bi-search"></i> Validasi Data
            </button>

            <div id="validationResult" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="bi bi-check-circle-fill text-green-600 text-xl"></i>
                    <div>
                        <p class="font-medium text-green-800">Data Ditemukan!</p>
                        <p id="foundName" class="text-sm text-green-700"></p>
                    </div>
                </div>
            </div>

            <div id="validationError" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="bi bi-x-circle-fill text-red-600 text-xl"></i>
                    <div>
                        <p class="font-medium text-red-800">Data Tidak Ditemukan</p>
                        <p class="text-sm text-red-700">Pastikan NIP/NIS/NISN yang dimasukkan benar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Form Lengkap (Hidden initially) -->
        <div id="step2" class="card hidden">
            <h3 class="text-lg font-semibold mb-4">
                <i class="bi bi-pencil-square text-blue-600"></i> Langkah 2: Lengkapi Data
            </h3>
            <p class="text-sm text-gray-600 mb-6">Data ditemukan: <span id="displayName" class="font-semibold text-blue-600"></span></p>

            <form id="registerForm" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" id="username" required class="input-field" placeholder="Masukkan username">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" required class="input-field" placeholder="Masukkan email">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="regPassword" required class="input-field" placeholder="Minimal 8 karakter">
                </div>

                <div>
                    <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                    <input type="password" id="confirmPassword" required class="input-field" placeholder="Ulangi password">
                </div>

                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-whatsapp text-green-600"></i> Nomor WhatsApp Aktif
                    </label>
                    <input type="tel" id="whatsapp" required class="input-field" placeholder="08xx-xxxx-xxxx">
                </div>

                <!-- Field tambahan untuk siswa -->
                <div id="parentWhatsappField" class="hidden">
                    <label for="parentWhatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-whatsapp text-green-600"></i> Nomor WhatsApp Orang Tua/Wali
                    </label>
                    <input type="tel" id="parentWhatsapp" class="input-field" placeholder="08xx-xxxx-xxxx">
                    <p class="text-xs text-gray-500 mt-1">Nomor ini akan digunakan untuk notifikasi absensi</p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" id="backBtn" class="btn-secondary flex-1 py-3">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </button>
                    <button type="submit" class="btn-primary flex-1 py-3">
                        <i class="bi bi-check-circle"></i> Daftar
                    </button>
                </div>
            </form>
        </div>

        <!-- Login Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Sudah punya akun?
                <a href="#" class="font-medium text-blue-600 hover:text-blue-800">Login sekarang</a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Role toggle
    const roleRadios = document.querySelectorAll('input[name="role_type"]');
    const nipField = document.getElementById('nipField');
    const nisField = document.getElementById('nisField');
    const validateBtn = document.getElementById('validateBtn');
    let currentRole = 'guru';

    roleRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            currentRole = this.value;
            if (currentRole === 'guru') {
                nipField.classList.remove('hidden');
                nisField.classList.add('hidden');
            } else {
                nipField.classList.add('hidden');
                nisField.classList.remove('hidden');
            }
        });
    });

    // Validasi (UI only - mock data)
    validateBtn.addEventListener('click', function() {
        const validationResult = document.getElementById('validationResult');
        const validationError = document.getElementById('validationError');
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');

        // Mock validation
        const mockData = {
            '1234567890': 'Budi Santoso, S.Pd',
            '0987654321': 'Siti Nurhaliza, M.Pd',
            'S001': 'Ahmad Fauzi',
            'S002': 'Nurul Hidayah'
        };

        let inputValue = '';
        if (currentRole === 'guru') {
            inputValue = document.getElementById('nip').value;
        } else {
            inputValue = document.getElementById('nis').value;
        }

        if (mockData[inputValue]) {
            validationResult.classList.remove('hidden');
            validationError.classList.add('hidden');
            document.getElementById('foundName').textContent = mockData[inputValue];

            // Show step 2 after delay
            setTimeout(() => {
                step1.classList.add('hidden');
                step2.classList.remove('hidden');
                document.getElementById('displayName').textContent = mockData[inputValue];

                // Show parent whatsapp field if siswa
                if (currentRole === 'siswa') {
                    document.getElementById('parentWhatsappField').classList.remove('hidden');
                }
            }, 1000);
        } else {
            validationResult.classList.add('hidden');
            validationError.classList.remove('hidden');
        }
    });

    // Back button
    document.getElementById('backBtn').addEventListener('click', function() {
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step1').classList.remove('hidden');
        document.getElementById('validationResult').classList.add('hidden');
    });

    // Form submission (UI only)
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('UI Only: Registrasi berhasil! (Backend belum diimplementasi)');
    });
</script>
@endpush
@endsection
