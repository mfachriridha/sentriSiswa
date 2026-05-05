@extends('layouts.guest')

@section('title', 'Sentri Siswa')

@section('content')
<div class="relative overflow-hidden">
    <section class="relative bg-slate-950 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,122,63,0.35),_transparent_55%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,_rgba(14,165,233,0.25),_transparent_60%)]"></div>
        <div class="absolute inset-0 bg-[linear-gradient(120deg,_rgba(2,6,23,0.92),_rgba(15,23,42,0.9))]"></div>

        <div class="relative mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 lg:flex-row lg:items-center lg:gap-16 lg:py-24">
            <div class="flex-1">
                <div class="chip reveal-up">Sistem absensi modern</div>
                <h1 class="font-display mt-6 text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Sentri Siswa
                </h1>
                <p class="mt-6 max-w-xl text-base text-slate-200 sm:text-lg">
                    Absensi SMA yang menyatukan verifikasi lokasi, selfie, dan laporan otomatis dalam satu alur yang rapi.
                    Fokus pada kedisiplinan, transparansi, dan komunikasi cepat ke orang tua.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('auth.login') }}" class="btn-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk Portal
                    </a>
                    <a href="{{ route('auth.register') }}" class="btn-outline">
                        <i class="bi bi-person-plus"></i> Buat Akun
                    </a>
                </div>

                <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="stat-card reveal-up delay-1">
                        <p class="text-sm font-semibold">Absensi 30 detik</p>
                        <p class="mt-2 text-xs text-white/70">Selfie + lokasi otomatis</p>
                    </div>
                    <div class="stat-card reveal-up delay-2">
                        <p class="text-sm font-semibold">Notifikasi cepat</p>
                        <p class="mt-2 text-xs text-white/70">Orang tua terhubung</p>
                    </div>
                    <div class="stat-card reveal-up delay-3">
                        <p class="text-sm font-semibold">Data real-time</p>
                        <p class="mt-2 text-xs text-white/70">Rekap harian otomatis</p>
                    </div>
                </div>
            </div>

            <div class="relative flex-1">
                <div class="glass-panel reveal-up delay-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/70">Live monitor</p>
                        <span class="rounded-full bg-emerald-400/20 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-emerald-200">
                            Online
                        </span>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10">
                                <i class="bi bi-person-check text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold">Hadir terverifikasi</p>
                                <p class="text-xs text-white/70">07:12 - Kelas X-2</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10">
                                <i class="bi bi-geo-alt text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold">Geofence aktif</p>
                                <p class="text-xs text-white/70">Radius 300m dari sekolah</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10">
                                <i class="bi bi-whatsapp text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold">Broadcast terkirim</p>
                                <p class="text-xs text-white/70">6 orang tua menerima laporan</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="flex items-center justify-between text-xs text-white/70">
                            <span>Peta geofence</span>
                            <span>Area aman</span>
                        </div>
                        <div class="mt-3 h-24 rounded-xl bg-[radial-gradient(circle_at_top,_rgba(56,189,248,0.45),_transparent_60%)]"></div>
                    </div>
                </div>

                <div class="float-slow absolute -bottom-8 -left-6 hidden w-44 rounded-2xl border border-white/15 bg-white/10 p-4 text-xs text-white/80 shadow-lg lg:block">
                    <p class="text-sm font-semibold text-white">SMAN 11 Kab. Tangerang</p>
                    <p class="mt-2">Ringkasan hadir 92% hari ini.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16 lg:py-20">
        <div class="mx-auto max-w-6xl px-4">
            <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-primary-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.25em] text-primary-700">
                        Tata tertib
                    </div>
                    <h2 class="font-display mt-4 text-3xl font-semibold text-slate-900">
                        Tata tertib sekolah selalu mudah diakses
                    </h2>
                    <p class="mt-4 text-base text-slate-600">
                        Semua siswa dapat membaca, memahami, dan menandai tata tertib sebelum memulai absensi.
                        Dokumen tersimpan rapi dengan jejak unduh yang jelas.
                    </p>
                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="feature-card">
                            <p class="text-sm font-semibold text-slate-900">Persetujuan transparan</p>
                            <p class="mt-2 text-sm text-slate-500">Status baca dan unduh tercatat otomatis.</p>
                        </div>
                        <div class="feature-card">
                            <p class="text-sm font-semibold text-slate-900">Dokumen terpusat</p>
                            <p class="mt-2 text-sm text-slate-500">Satu sumber untuk semua pihak sekolah.</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Dokumen Tata Tertib</h3>
                            <p class="text-sm text-slate-500">Versi terbaru, selalu siap diunduh.</p>
                        </div>
                        <button class="btn-secondary inline-flex items-center gap-2">
                            <i class="bi bi-download"></i> Download PDF
                        </button>
                    </div>

                    <div class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6">
                        <div class="text-center">
                            <i class="bi bi-file-earmark-pdf text-5xl text-primary-500"></i>
                            <p class="mt-4 text-sm font-semibold text-slate-700">PDF Tata Tertib belum diupload</p>
                            <p class="mt-2 text-xs text-slate-500">Admin akan mengunggah dokumen resmi sekolah.</p>
                            <button class="btn-secondary mt-5 inline-flex items-center gap-2">
                                <i class="bi bi-upload"></i> Upload PDF (Admin)
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-2 text-xs text-slate-500">
                        <i class="bi bi-info-circle"></i>
                        Pastikan siswa memahami tata tertib sebelum absensi.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-16 lg:py-20">
        <div class="mx-auto max-w-6xl px-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-primary-600">Fitur inti</p>
                    <h2 class="font-display mt-3 text-3xl font-semibold text-slate-900">Rangkaian fitur yang sinkron</h2>
                </div>
                <p class="max-w-md text-sm text-slate-500">
                    Setiap langkah dirancang untuk mempercepat validasi dan memberikan laporan yang jelas ke pihak sekolah.
                </p>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-3">
                <div class="feature-card">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                        <i class="bi bi-geo-alt-fill text-2xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">Geofencing presisi</h3>
                    <p class="mt-2 text-sm text-slate-500">Validasi lokasi siswa dengan polygon area sekolah.</p>
                </div>
                <div class="feature-card">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <i class="bi bi-camera-fill text-2xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">Selfie terverifikasi</h3>
                    <p class="mt-2 text-sm text-slate-500">Foto absensi langsung terhubung ke profil siswa.</p>
                </div>
                <div class="feature-card">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-600">
                        <i class="bi bi-whatsapp text-2xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">Notifikasi WhatsApp</h3>
                    <p class="mt-2 text-sm text-slate-500">Orang tua menerima ringkasan kehadiran otomatis.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 lg:py-20">
        <div class="mx-auto max-w-6xl px-4">
            <div class="relative overflow-hidden rounded-3xl bg-slate-950 px-8 py-12 text-white sm:px-12">
                <div class="absolute -right-10 -top-16 h-48 w-48 rounded-full bg-primary-500/30 blur-3xl"></div>
                <div class="absolute -bottom-20 left-10 h-48 w-48 rounded-full bg-sky-400/20 blur-3xl"></div>

                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/70">Siap dipakai</p>
                        <h2 class="font-display mt-3 text-3xl font-semibold">Mulai absensi lebih cepat</h2>
                        <p class="mt-3 text-sm text-white/70">
                            Aktifkan Sentri Siswa untuk disiplin yang terukur dan komunikasi yang lebih baik.
                        </p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('auth.login') }}" class="btn-primary">
                            <i class="bi bi-lightning-charge"></i> Mulai Sekarang
                        </a>
                        <a href="{{ route('auth.register') }}" class="btn-outline">
                            <i class="bi bi-person-plus"></i> Daftarkan Akun
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
