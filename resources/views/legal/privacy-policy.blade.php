@extends('layouts.guest')

@section('title', 'Kebijakan Privasi - Sentri Siswa')

@section('content')
<main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
    <a href="{{ route('home') }}" class="text-sm font-semibold text-primary hover:text-primary-dark">Kembali ke Beranda</a>

    <section class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="space-y-3">
            <p class="text-sm font-semibold uppercase tracking-wide text-primary">Sentri Siswa</p>
            <h1 class="text-3xl font-bold text-gray-950">Kebijakan Privasi</h1>
            <p class="text-sm text-gray-500">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>
        </div>

        <div class="mt-8 space-y-7 text-sm leading-7 text-gray-700">
            <section>
                <h2 class="text-lg font-semibold text-gray-950">1. Informasi yang Dikumpulkan</h2>
                <p class="mt-2">Sentri Siswa mengelola data sekolah yang diperlukan untuk absensi, monitoring kelas, pelanggaran siswa, tata tertib, dan laporan internal. Data yang dapat diproses meliputi nama, NIS, NISN, kelas, biodata siswa, data guru, nomor telepon guru, status absensi, foto selfie absensi, serta riwayat pelanggaran.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-950">2. Penggunaan Data</h2>
                <p class="mt-2">Data digunakan untuk menjalankan fitur aplikasi, memverifikasi akun siswa dan guru, mencatat kehadiran, menampilkan laporan kepada pihak sekolah yang berwenang, serta mengirim notifikasi WhatsApp terkait laporan absensi.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-950">3. Integrasi WhatsApp</h2>
                <p class="mt-2">Jika fitur WhatsApp diaktifkan, nomor telepon guru dapat digunakan untuk menerima laporan absensi melalui WhatsApp Cloud API. Pesan yang dikirim terbatas pada kebutuhan operasional sekolah.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-950">4. Penyimpanan dan Keamanan</h2>
                <p class="mt-2">Data disimpan pada sistem yang dikelola sekolah atau pengelola aplikasi. Akses dibatasi berdasarkan peran pengguna seperti admin, guru, wali kelas, BK, kesiswaan, dan siswa.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-950">5. Pembagian Data</h2>
                <p class="mt-2">Data tidak dijual kepada pihak ketiga. Data hanya digunakan untuk kebutuhan internal sekolah dan layanan pendukung yang diperlukan untuk menjalankan aplikasi, termasuk layanan pengiriman pesan WhatsApp jika dikonfigurasi.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-950">6. Kontak</h2>
                <p class="mt-2">Pertanyaan terkait kebijakan privasi dapat disampaikan kepada pengelola Sentri Siswa atau pihak sekolah.</p>
            </section>
        </div>
    </section>
</main>
@endsection
