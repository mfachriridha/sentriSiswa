@extends('layouts.guest')

@section('title', 'Ketentuan Layanan - Sentri Siswa')

@section('content')
<main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
    <a href="{{ route('home') }}" class="text-sm font-semibold text-primary hover:text-primary-dark">Kembali ke Beranda</a>

    <section class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="space-y-3">
            <p class="text-sm font-semibold uppercase tracking-wide text-primary">Sentri Siswa</p>
            <h1 class="text-3xl font-bold text-gray-950">Ketentuan Layanan</h1>
            <p class="text-sm text-gray-500">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>
        </div>

        <div class="mt-8 space-y-7 text-sm leading-7 text-gray-700">
            <section>
                <h2 class="text-lg font-semibold text-gray-950">1. Penggunaan Aplikasi</h2>
                <p class="mt-2">Sentri Siswa digunakan untuk mendukung administrasi absensi, monitoring siswa, pelanggaran, tata tertib, dan laporan sekolah. Pengguna wajib menggunakan aplikasi sesuai peran dan kewenangan yang diberikan.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-950">2. Akun Pengguna</h2>
                <p class="mt-2">Siswa dan guru hanya dapat mendaftar apabila datanya sudah dimasukkan oleh admin. Pengguna bertanggung jawab menjaga kerahasiaan email dan kata sandi akun masing-masing.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-950">3. Data dan Laporan</h2>
                <p class="mt-2">Data yang tampil di aplikasi digunakan untuk kebutuhan operasional sekolah. Perubahan data hanya boleh dilakukan oleh pengguna yang memiliki hak akses sesuai aturan sistem.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-950">4. Notifikasi WhatsApp</h2>
                <p class="mt-2">Jika fitur notifikasi WhatsApp diaktifkan, sistem dapat mengirim pesan laporan absensi kepada guru atau pihak sekolah yang ditentukan. Fitur ini menggunakan layanan gateway WhatsApp dan tunduk pada kebijakan penggunaan masing-masing penyedia.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-950">5. Pembatasan Tanggung Jawab</h2>
                <p class="mt-2">Sentri Siswa membantu pencatatan dan pelaporan, namun validasi akhir data sekolah tetap menjadi tanggung jawab pihak sekolah dan pengguna yang berwenang.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-950">6. Perubahan Ketentuan</h2>
                <p class="mt-2">Ketentuan layanan dapat diperbarui sesuai kebutuhan pengembangan sistem dan kebijakan sekolah.</p>
            </section>
        </div>
    </section>
</main>
@endsection
