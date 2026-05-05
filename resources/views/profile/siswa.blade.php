@extends('layouts.app')

@section('title', 'Profil Siswa')

@section('sidebar-menu')
<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-grid-1x2-fill"></i> Dashboard
</a>

<div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Absensi</div>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-camera-fill"></i> Absensi Masuk
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-box-arrow-left"></i> Absensi Pulang
</a>

<div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Akun</div>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-600 text-white font-medium transition">
    <i class="bi bi-person-circle"></i> Profil
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-bar-chart-fill"></i> Poin Saya
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-box-arrow-right"></i> Logout
</a>
@endsection

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Profil Siswa</h1>
    <p class="text-gray-600 mt-1">Lengkapi data profil Anda</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile Card -->
    <div class="card text-center">
        <div class="relative inline-block mb-4">
            <div class="w-32 h-32 bg-green-100 rounded-full flex items-center justify-center mx-auto border-4 border-white shadow-lg">
                <i class="bi bi-person-fill text-6xl text-green-600"></i>
            </div>
            <button class="absolute bottom-0 right-1/2 translate-x-1/2 translate-y-1/4 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-green-700 transition">
                <i class="bi bi-camera text-sm"></i>
            </button>
        </div>
        <h3 class="text-xl font-bold text-gray-900">Ahmad Fauzi</h3>
        <p class="text-gray-600">XII IPA 1</p>
        <div class="mt-4 flex justify-center gap-2">
            <span class="badge-success">Aktif</span>
            <span class="badge-info">NIS: S001</span>
        </div>
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-600">Poin Pelanggaran</span>
                <span class="font-medium text-green-600">95 Poin</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full" style="width: 95%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">Dari saldo awal 100</p>
        </div>
    </div>

    <!-- Profile Form -->
    <div class="lg:col-span-2 space-y-6">
        <div class="card">
            <h3 class="text-lg font-bold mb-4">Informasi Pribadi</h3>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" value="Ahmad Fauzi" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIS/NISN</label>
                        <input type="text" value="S001" class="input-field" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                        <input type="text" value="XII IPA 1" class="input-field" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select class="input-field">
                            <option>Laki-laki</option>
                            <option>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" placeholder="Masukkan tempat lahir" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" class="input-field">
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <h3 class="text-lg font-bold mb-4">Kontak</h3>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" placeholder="email@siswa.sch.id" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Siswa</label>
                        <input type="tel" placeholder="08xx-xxxx-xxxx" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Orang Tua/Wali</label>
                        <input type="tel" placeholder="08xx-xxxx-xxxx" class="input-field">
                        <p class="text-xs text-gray-500 mt-1">Digunakan untuk notifikasi absensi</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea class="input-field" rows="3" placeholder="Alamat lengkap"></textarea>
                    </div>
                </div>
                <div class="pt-4">
                    <button type="button" onclick="alert('UI Only: Profil berhasil diperbarui!')" class="btn-primary inline-flex items-center gap-2">
                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Raport -->
        <div class="card">
            <h3 class="text-lg font-bold mb-4">Data Raport</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-3 bg-blue-50 rounded-lg text-center">
                    <p class="text-sm text-blue-600">Semester 1</p>
                    <p class="text-xl font-bold text-blue-800">85.5</p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg text-center">
                    <p class="text-sm text-green-600">Semester 2</p>
                    <p class="text-xl font-bold text-green-800">88.0</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-lg text-center">
                    <p class="text-sm text-purple-600">Rata-rata</p>
                    <p class="text-xl font-bold text-purple-800">86.75</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
