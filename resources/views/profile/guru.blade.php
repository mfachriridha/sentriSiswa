@extends('layouts.app')

@section('title', 'Profil Guru')

@section('sidebar-menu')
<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-grid-1x2-fill"></i> Dashboard
</a>

<div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Menu Utama</div>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-camera-fill"></i> Presensi
</a>

<div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Akun</div>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-600 text-white font-medium transition">
    <i class="bi bi-person-circle"></i> Profil
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-box-arrow-right"></i> Logout
</a>
@endsection

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Profil Guru</h1>
    <p class="text-gray-600 mt-1">Lengkapi data profil Anda</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile Card -->
    <div class="card text-center">
        <div class="relative inline-block mb-4">
            <div class="w-32 h-32 bg-blue-100 rounded-full flex items-center justify-center mx-auto border-4 border-white shadow-lg">
                <i class="bi bi-person-fill text-6xl text-blue-600"></i>
            </div>
            <button class="absolute bottom-0 right-1/2 translate-x-1/2 translate-y-1/4 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-blue-700 transition">
                <i class="bi bi-camera text-sm"></i>
            </button>
        </div>
        <h3 class="text-xl font-bold text-gray-900">Budi Santoso, S.Pd</h3>
        <p class="text-gray-600">Wali Kelas XII IPA 1</p>
        <div class="mt-4 flex justify-center gap-2">
            <span class="badge-info">Wali Kelas</span>
            <span class="badge-success">Guru Mapel</span>
        </div>
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-600">NIP</span>
                <span class="font-medium">1234567890</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Bergabung</span>
                <span class="font-medium">Januari 2024</span>
            </div>
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
                        <input type="text" value="Budi Santoso, S.Pd" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                        <input type="text" value="1234567890" class="input-field" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" value="budi.santoso@sman11tng.sch.id" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                        <input type="tel" value="081234567890" class="input-field">
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
            <h3 class="text-lg font-bold mb-4">Informasi Kepegawaian</h3>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pangkat/Golongan</label>
                        <select class="input-field">
                            <option>Pilih Golongan</option>
                            <option>IIIa</option>
                            <option>IIIb</option>
                            <option>IVa</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                        <input type="text" value="Wali Kelas" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                        <input type="text" value="Matematika" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                        <input type="text" value="S1 Pendidikan Matematika" class="input-field">
                    </div>
                </div>
                <div class="pt-4">
                    <button type="button" onclick="alert('UI Only: Profil berhasil diperbarui!')" class="btn-primary inline-flex items-center gap-2">
                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
