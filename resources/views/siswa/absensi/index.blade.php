@extends('layouts.app')

@section('title', 'Presensi')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Presensi Hari Ini</h1>
        <p class="mt-1 text-sm text-gray-500">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
    </div>
    <a href="{{ route('siswa.absensi.riwayat') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Riwayat
    </a>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

{{-- Time info bar --}}
<div class="mb-5 flex flex-wrap items-center gap-x-4 gap-y-1 rounded-lg border border-gray-100 bg-gray-50 px-4 py-2.5 text-sm text-gray-500">
    <span>Jam presensi: <strong class="text-gray-700">{{ $startTime }} – {{ $endTime }}</strong></span>
    <span class="hidden sm:inline text-gray-300">|</span>
    <span>Waktu sekarang: <strong class="text-gray-700">{{ $currentTimeLabel }}</strong></span>
</div>

<div class="grid gap-6 lg:grid-cols-[1fr_300px] items-start">

    {{-- MAIN COLUMN: status / form --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6">

        @if($todayAttendance && $todayAttendance->status !== 'belum_absen')
            {{-- POST-ATTENDANCE: success card --}}
            @php
                $statusConfig = [
                    'hadir'     => ['bg-green-50 text-green-700 border-green-200',   'Hadir',     'bg-green-100'],
                    'izin'      => ['bg-blue-50 text-blue-700 border-blue-200',      'Izin',      'bg-blue-100'],
                    'sakit'     => ['bg-purple-50 text-purple-700 border-purple-200','Sakit',     'bg-purple-100'],
                    'dispensasi'=> ['bg-orange-50 text-orange-700 border-orange-200','Dispensasi','bg-orange-100'],
                    'alpha'     => ['bg-red-50 text-red-700 border-red-200',         'Alpha',     'bg-red-100'],
                ];
                [$badgeClass, $statusLabel, $bgClass] = $statusConfig[$todayAttendance->status] ?? ['bg-gray-50 text-gray-700 border-gray-200', $todayAttendance->status, 'bg-gray-100'];
            @endphp

            <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                {{-- Selfie thumbnail --}}
                <div class="shrink-0">
                    @if($todayAttendance->path_selfie)
                        <img src="{{ asset('storage/'.$todayAttendance->path_selfie) }}"
                             alt="Selfie presensi"
                             class="aspect-[3/4] w-28 rounded-xl border border-gray-200 object-cover shadow-sm">
                    @else
                        <div class="flex aspect-[3/4] w-28 items-center justify-center rounded-xl border border-dashed border-gray-200 bg-gray-50 text-center text-xs text-gray-400">
                            Selfie belum tersedia
                        </div>
                    @endif
                </div>

                {{-- Status info --}}
                <div class="flex-1 space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status kehadiran Anda hari ini</p>
                        <span class="mt-2 inline-flex items-center rounded-full border {{ $badgeClass }} px-5 py-2 text-base font-bold">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="space-y-1.5 text-sm text-gray-500">
                        @if($todayAttendance->waktu_masuk)
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Presensi pukul <strong class="text-gray-700">{{ $todayAttendance->waktu_masuk->format('H:i') }}</strong></span>
                            </div>
                        @endif
                        @if($todayAttendance->jarak_meter !== null)
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>
                                    @if($todayAttendance->jarak_meter == 0)
                                        Di dalam area presensi
                                    @else
                                        {{ number_format($todayAttendance->jarak_meter, 0) }} m dari area presensi
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-lg {{ $bgClass }} px-4 py-3 text-sm font-medium text-gray-700">
                        Presensi Anda hari ini sudah tercatat. Sampai jumpa besok!
                    </div>
                </div>
            </div>

        @elseif($canCheckIn)
            {{-- PRESENSI FORM --}}
            <form method="POST"
                  action="{{ route('siswa.absensi.store') }}"
                  enctype="multipart/form-data"
                  x-data="attendanceForm({ geofenceActive: @js($geofenceActive), maxPhotoKb: 1024 })"
                  @submit="validateBeforeSubmit($event)">
                @csrf
                <input type="hidden" name="latitude" :value="latitude">
                <input type="hidden" name="longitude" :value="longitude">
                <input type="hidden" name="accuracy" :value="accuracy">
                <input type="file" x-ref="selfieInput" name="selfie" class="hidden" accept="image/jpeg,image/png,image/webp">

                <div class="space-y-6">

                    {{-- 1. Status selector --}}
                    <div>
                        <label class="mb-2.5 block text-sm font-semibold text-gray-900">
                            Pilih Status Kehadiran <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <label class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 p-3 text-center transition-all"
                                   :class="selectedStatus === 'hadir' ? 'border-primary bg-primary/5 text-primary shadow-sm' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'">
                                <input type="radio" name="status" value="hadir" x-model="selectedStatus" class="sr-only">
                                <span class="text-sm font-bold">Hadir</span>
                                <span class="mt-0.5 text-[11px] text-gray-500">Ada di lokasi</span>
                            </label>
                            <label class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 p-3 text-center transition-all"
                                   :class="selectedStatus === 'sakit' ? 'border-purple-600 bg-purple-50 text-purple-700 shadow-sm' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'">
                                <input type="radio" name="status" value="sakit" x-model="selectedStatus" class="sr-only">
                                <span class="text-sm font-bold">Sakit</span>
                                <span class="mt-0.5 text-[11px] text-gray-500">Ada surat sakit</span>
                            </label>
                            <label class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 p-3 text-center transition-all"
                                   :class="selectedStatus === 'izin' ? 'border-blue-600 bg-blue-50 text-blue-700 shadow-sm' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'">
                                <input type="radio" name="status" value="izin" x-model="selectedStatus" class="sr-only">
                                <span class="text-sm font-bold">Izin</span>
                                <span class="mt-0.5 text-[11px] text-gray-500">Izin keperluan</span>
                            </label>
                            <label class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 p-3 text-center transition-all"
                                   :class="selectedStatus === 'dispensasi' ? 'border-orange-600 bg-orange-50 text-orange-700 shadow-sm' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'">
                                <input type="radio" name="status" value="dispensasi" x-model="selectedStatus" class="sr-only">
                                <span class="text-sm font-bold">Dispensasi</span>
                                <span class="mt-0.5 text-[11px] text-gray-500">Tugas sekolah</span>
                            </label>
                        </div>
                    </div>

                    {{-- 2. Verification Steps (GPS & Photo) --}}
                    <div class="space-y-4 rounded-xl border border-gray-200 bg-gray-50/50 p-4 sm:p-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Verifikasi Kehadiran</h3>
                            <span class="text-xs text-gray-500" x-text="selectedStatus === 'hadir' ? 'Lokasi + Foto Selfie' : 'Foto Bukti Alasan'"></span>
                        </div>

                        {{-- Step A: GPS Area Check (Hanya Wajib jika Status = Hadir) --}}
                        <template x-if="selectedStatus === 'hadir'">
                            <div class="space-y-3 rounded-lg border bg-white p-3.5 shadow-sm transition-colors"
                                 :class="!geofenceActive ? 'border-blue-200' : (locationStatus === 'inside' || locationStatus === 'tolerance' ? 'border-green-200' : (locationStatus === 'outside' ? 'border-red-200' : 'border-gray-200'))">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">1. Area Sekitar Sekolah</p>
                                        <template x-if="!geofenceActive">
                                            <p class="mt-0.5 text-xs text-blue-600">Verifikasi radius sekolah tidak diaktifkan.</p>
                                        </template>
                                        <template x-if="geofenceActive">
                                            <p class="mt-0.5 text-xs font-medium"
                                               :class="locationStatus === 'inside' || locationStatus === 'tolerance' ? 'text-green-600' : (locationStatus === 'outside' ? 'text-red-600' : 'text-gray-500')"
                                               x-text="locationMessage || 'Lokasi belum diverifikasi'"></p>
                                        </template>
                                    </div>
                                    <template x-if="geofenceActive">
                                        <button type="button"
                                                @click="getGpsLocation()"
                                                :disabled="gpsLoading || locationChecking"
                                                class="inline-flex items-center gap-1.5 self-start rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 disabled:opacity-50 sm:self-center">
                                            <svg class="h-3.5 w-3.5 text-gray-500" :class="{ 'animate-spin': gpsLoading || locationChecking }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            <span x-text="gpsLoading ? 'Mengambil GPS...' : (locationChecking ? 'Memeriksa...' : 'Aktifkan / Refresh GPS')"></span>
                                        </button>
                                    </template>
                                </div>
                                <p x-cloak x-show="gpsError" x-text="gpsError" class="text-xs font-medium text-red-600"></p>
                            </div>
                        </template>

                        {{-- Step B: Photo/Selfie Button Trigger --}}
                        <div class="rounded-lg border bg-white p-3.5 shadow-sm transition-colors"
                             :class="selfieReady ? 'border-green-200' : 'border-gray-200'">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500"
                                       x-text="selectedStatus === 'hadir' ? '2. Foto Selfie Presensi' : '2. Foto Bukti Surat / Keterangan'"></p>
                                    <p class="mt-0.5 text-xs"
                                       :class="selfieReady ? 'font-semibold text-green-600' : 'text-gray-500'"
                                       x-text="selfieReady ? 'Foto siap digunakan' : (selectedStatus === 'hadir' ? 'Ambil selfie langsung dari kamera' : 'Ambil foto bukti dari kamera')"></p>
                                </div>
                                <button type="button"
                                        @click="openSelfieModal()"
                                        :disabled="!canOpenSelfieModal"
                                        class="inline-flex items-center gap-1.5 self-start rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:bg-primary-dark disabled:cursor-not-allowed disabled:bg-gray-300 disabled:shadow-none sm:self-center">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span x-text="selfieReady ? 'Foto Ulang' : (selectedStatus === 'hadir' ? 'Ambil Selfie' : 'Ambil Foto Bukti')"></span>
                                </button>
                            </div>
                            <p x-cloak x-show="!canOpenSelfieModal" x-text="openDisabledMessage" class="mt-2 text-xs text-amber-700 font-medium"></p>
                        </div>
                    </div>

                    {{-- Preview Thumbnail Before Final Submit --}}
                    <div x-cloak x-show="previewUrl" class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50/50 p-3">
                        <img :src="previewUrl" alt="Preview foto" class="h-14 w-11 rounded-lg border border-green-200 object-cover">
                        <div class="flex-1 text-xs">
                            <p class="font-bold text-green-900" x-text="selectedStatus === 'hadir' ? 'Foto selfie berhasil diambil' : 'Foto bukti berhasil diambil'"></p>
                            <p class="text-green-700">Tekan tombol di bawah untuk menyelesaikan presensi.</p>
                        </div>
                    </div>

                </div>

                {{-- CAMERA MODAL (FULLSCREEN RESPONSIVE) --}}
                <div x-cloak
                     x-show="modalOpen"
                     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4">
                    <div x-show="modalOpen"
                         x-transition.opacity
                         @click="cancelSelfieModal()"
                         class="fixed inset-0 bg-black/60 backdrop-blur-xs"></div>

                    <div x-show="modalOpen"
                         x-transition.scale.origin.center
                         class="relative z-10 flex max-h-[90vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-4 py-3">
                            <h3 class="text-sm font-bold text-gray-900"
                                x-text="selectedStatus === 'hadir' ? 'Kamera Selfie Presensi' : 'Kamera Foto Bukti'"></h3>
                            <button type="button"
                                    @click="cancelSelfieModal()"
                                    class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
                                <span class="sr-only">Tutup</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto bg-gray-50 p-4">
                            <div class="relative mx-auto aspect-[3/4] w-full max-w-sm overflow-hidden rounded-xl border-4 border-white shadow-lg bg-gray-200">
                                <video x-ref="video" class="h-full w-full object-cover" :style="facingMode === 'user' ? 'transform: scaleX(-1)' : ''" playsinline></video>
                                <img x-cloak x-show="previewUrl" :src="previewUrl" class="absolute inset-0 h-full w-full object-cover">
                            </div>
                        </div>

                        <div class="shrink-0 border-t border-gray-200 bg-gray-50 px-4 py-3">
                            <p x-cloak x-show="previewUrl" class="mb-2 text-xs font-medium text-amber-700">
                                Setelah ditekan, presensi hari ini tidak bisa diubah sendiri. Kalau salah, hubungi wali kelas.
                            </p>
                            <div class="flex gap-2 sm:justify-end">
                                <button type="button"
                                        @click="cancelSelfieModal()"
                                        class="inline-flex flex-1 items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-xs font-medium text-gray-700 transition-colors hover:bg-white sm:flex-none sm:text-sm">
                                    Batal
                                </button>
                                <button type="submit"
                                        :disabled="!canSubmit"
                                        class="inline-flex flex-1 items-center justify-center rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:cursor-not-allowed disabled:bg-gray-300 sm:flex-none sm:text-sm">
                                    Presensi Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        @else
            {{-- OUTSIDE HOURS --}}
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <svg class="mb-4 h-12 w-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-gray-500">
                    @if(! $isWeekday)
                        Presensi hanya tersedia pada hari {{ $activeDaysLabel }}.
                    @elseif(now()->format('H:i') < $startTime)
                        Belum waktunya presensi. Presensi dimulai pukul {{ $startTime }}.
                    @else
                        Waktu presensi sudah berakhir pukul {{ $endTime }}.
                    @endif
                </p>
            </div>
        @endif
    </div>

    {{-- SIDEBAR: Monthly stats --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="mb-4 text-base font-semibold text-gray-900">Ringkasan Bulan Ini</h2>

        @php
            $totalDays = $stats['hadir'] + $stats['izin'] + $stats['sakit'] + $stats['alpha'];
            $pct = $totalDays > 0 ? round($stats['hadir'] / $totalDays * 100) : 0;
        @endphp

        <div class="mb-4 rounded-lg bg-gray-50 p-3 text-center">
            <p class="text-3xl font-bold {{ $pct >= 80 ? 'text-green-600' : ($pct >= 60 ? 'text-amber-600' : 'text-red-600') }}">{{ $pct }}%</p>
            <p class="mt-0.5 text-xs text-gray-500">Kehadiran ({{ $totalDays }} hari tercatat)</p>
        </div>

        <div class="grid grid-cols-1 gap-2">
            <div class="flex items-center justify-between rounded-lg border border-green-100 bg-green-50 px-3 py-2.5">
                <span class="text-sm font-medium text-green-700">Hadir</span>
                <span class="text-lg font-bold text-green-700">{{ $stats['hadir'] }}</span>
            </div>
            <div class="flex items-center justify-between rounded-lg border border-blue-100 bg-blue-50 px-3 py-2.5">
                <span class="text-sm font-medium text-blue-700">Izin</span>
                <span class="text-lg font-bold text-blue-700">{{ $stats['izin'] }}</span>
            </div>
            <div class="flex items-center justify-between rounded-lg border border-purple-100 bg-purple-50 px-3 py-2.5">
                <span class="text-sm font-medium text-purple-700">Sakit</span>
                <span class="text-lg font-bold text-purple-700">{{ $stats['sakit'] }}</span>
            </div>
            <div class="flex items-center justify-between rounded-lg border border-red-100 bg-red-50 px-3 py-2.5">
                <span class="text-sm font-medium text-red-700">Alpha</span>
                <span class="text-lg font-bold text-red-700">{{ $stats['alpha'] }}</span>
            </div>
        </div>
    </div>

</div>{{-- end main grid --}}

@push('scripts')
<script>
    function attendanceForm(config) {
        return {
            cameraReady: false,
            modalOpen: false,
            error: '',
            previewUrl: '',
            selfieReady: false,
            stream: null,
            compressing: false,
            compressedSizeKb: null,
            geofenceActive: config.geofenceActive,
            maxPhotoKb: config.maxPhotoKb,
            gpsReady: false,
            gpsLoading: false,
            gpsError: '',
            latitude: '',
            longitude: '',
            accuracy: '',
            locationStatus: '',
            locationMessage: '',
            locationDistance: null,
            locationChecking: false,
            selectedStatus: 'hadir',
            facingMode: 'user',

            init() {
                this.$watch('selectedStatus', (newStatus) => {
                    if (newStatus !== 'hadir') {
                        this.gpsError = '';
                    }
                });
            },

            get isMobileDevice() {
                return /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
            },

            get canSubmit() {
                return this.selfieReady && !this.compressing;
            },

            get canOpenSelfieModal() {
                if (this.selectedStatus === 'hadir' && !this.isMobileDevice) {
                    return false;
                }

                if (this.selectedStatus !== 'hadir') {
                    return true; // Sakit, Izin, Dispensasi boleh dari mana saja / peranti apa saja
                }

                if (!this.geofenceActive) {
                    return true;
                }

                if (!this.gpsReady || this.gpsLoading || this.locationChecking) {
                    return false;
                }

                return ['inside', 'tolerance'].includes(this.locationStatus);
            },

            get openDisabledMessage() {
                if (this.selectedStatus === 'hadir' && !this.isMobileDevice) {
                    return 'Absensi Hadir wajib dilakukan dari HP / Smartphone.';
                }

                if (!this.geofenceActive) {
                    return 'Lokasi GPS tidak diwajibkan. Silakan lanjut absen.';
                }

                if (this.selectedStatus !== 'hadir') {
                    return 'Status ' + this.selectedStatus + ' diperbolehkan tanpa verifikasi lokasi sekolah.';
                }

                if (this.gpsLoading) {
                    return 'Mengambil lokasi GPS...';
                }

                if (this.locationChecking) {
                    return 'Memeriksa lokasi Anda...';
                }

                if (!this.gpsReady) {
                    return 'Aktifkan lokasi GPS terlebih dahulu.';
                }

                if (this.locationStatus === 'outside') {
                    return 'Lokasi Anda di luar area absensi.';
                }

                if (['inside', 'tolerance'].includes(this.locationStatus)) {
                    return 'Lokasi valid, silakan lanjut absen.';
                }

                return 'Tekan Aktifkan GPS untuk memeriksa lokasi.';
            },

            openSelfieModal() {
                if (!this.canOpenSelfieModal) {
                    return;
                }

                this.error = '';
                this.modalOpen = true;
            },

            cancelSelfieModal() {
                this.modalOpen = false;
                this.stopCamera();
                this.resetSelfieData();
            },

            getGpsLocation() {
                this.gpsError = '';
                this.gpsLoading = true;
                this.gpsReady = false;
                this.locationStatus = '';
                this.locationMessage = '';
                this.locationDistance = null;

                if (!navigator.geolocation) {
                    this.gpsLoading = false;
                    this.gpsError = 'Browser tidak mendukung geolokasi.';
                    return;
                }

                // Cek 1: Deteksi Ekstensi Pemalsu Lokasi (seperti Location Guard / Geolocation Tamper JS)
                try {
                    const fnStr = navigator.geolocation.getCurrentPosition.toString();
                    if (!fnStr.includes('[native code]')) {
                        this.gpsLoading = false;
                        this.gpsReady = false;
                        this.gpsError = 'Ekstensi pemalsu lokasi terdeteksi di browser! Harap matikan ekstensi lokasi dan gunakan browser standar.';
                        return;
                    }
                } catch (e) {}

                // Ambil data posisi & telemetri hardware dari browser peranti
                navigator.geolocation.getCurrentPosition((position) => {
                    const coords = position.coords;
                    const lat = coords.latitude;
                    const lng = coords.longitude;
                    const acc = coords.accuracy || 0;
                    const alt = coords.altitude;
                    const altAcc = coords.altitudeAccuracy;
                    const speed = coords.speed;

                    // Cek 2: Akurasi ekstrim 0 atau persis 1.0 (khas mock provider tertentu)
                    if (acc === 0 || acc === 1) {
                        this.gpsLoading = false;
                        this.gpsReady = false;
                        this.gpsError = 'Sinyal GPS tidak valid. Pastikan perangkat menggunakan GPS asli.';
                        return;
                    }

                    // Cek 3: Telemetri Hardware Satelit (Altitude & Speed Check)
                    // Aplikasi Fake GPS 2D di Android menyuntikkan titik buatan tanpa metadata altitude, altitudeAccuracy, & speed (semuanya null).
                    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
                    if (isMobile && alt === null && altAcc === null && speed === null) {
                        this.gpsLoading = false;
                        this.gpsReady = false;
                        this.gpsError = 'Indikasi Fake GPS terdeteksi! (Metadata telemetri satelit tidak ditemukan). Harap matikan aplikasi pemalsu lokasi dan gunakan GPS asli peranti.';
                        return;
                    }

                    this.latitude = String(lat);
                    this.longitude = String(lng);
                    this.accuracy = String(acc);
                    this.gpsReady = true;
                    this.gpsLoading = false;
                    this.gpsError = '';

                    // Lanjutkan ke verifikasi geofence area sekolah di server
                    this.checkLocation();
                }, (error) => {
                    this.gpsLoading = false;
                    this.gpsReady = false;

                    if (error.code === error.PERMISSION_DENIED) {
                        this.gpsError = 'Izin lokasi ditolak. Aktifkan izin lokasi di pengaturan browser.';
                    } else if (error.code === error.TIMEOUT) {
                        this.gpsError = 'Waktu habis saat mengambil lokasi. Tekan Refresh Lokasi.';
                    } else {
                        this.gpsError = 'Gagal mengambil lokasi. Pastikan GPS dan izin lokasi aktif.';
                    }
                }, {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0,
                });
            },

            async checkLocation() {
                if (!this.geofenceActive) {
                    return;
                }

                this.locationChecking = true;

                try {
                    const response = await fetch('{{ route("siswa.absensi.cek-lokasi") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            latitude: this.latitude,
                            longitude: this.longitude,
                            accuracy: this.accuracy,
                        }),
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.locationStatus = data.status;
                        this.locationMessage = data.message;
                        this.locationDistance = data.distance_meters;
                    } else {
                        this.locationStatus = 'outside';
                        this.locationMessage = 'Gagal memeriksa lokasi.';
                        this.locationDistance = null;
                    }
                } catch {
                    this.locationStatus = 'outside';
                    this.locationMessage = 'Gagal memeriksa lokasi. Coba lagi.';
                    this.locationDistance = null;
                } finally {
                    this.locationChecking = false;
                }
            },

            async startCamera() {
                this.error = '';

                if (!navigator.mediaDevices?.getUserMedia) {
                    this.error = 'Browser tidak mendukung akses kamera.';
                    return;
                }

                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: this.facingMode,
                            width: { ideal: 640 },
                            height: { ideal: 853 },
                        },
                        audio: false,
                    });
                    this.$refs.video.srcObject = this.stream;
                    await this.$refs.video.play();
                    this.cameraReady = true;
                } catch (error) {
                    this.error = 'Akses kamera ditolak atau kamera tidak tersedia.';
                }
            },

            async captureSelfie() {
                this.error = '';

                if (!this.cameraReady) {
                    this.error = 'Nyalakan kamera dulu.';
                    return;
                }

                this.compressing = true;

                try {
                    const blob = await this.captureCompressedBlob();

                    if (!blob) {
                        this.error = 'Gagal mengambil selfie.';
                        return;
                    }

                    const sizeKb = Math.ceil(blob.size / 1024);
                    if (sizeKb > this.maxPhotoKb) {
                        this.error = 'Foto masih terlalu besar (' + sizeKb + ' KB). Ulangi selfie dengan pencahayaan lebih baik.';
                        return;
                    }

                    if (this.previewUrl) {
                        URL.revokeObjectURL(this.previewUrl);
                    }

                    const file = new File([blob], 'selfie-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    this.$refs.selfieInput.files = transfer.files;
                    this.previewUrl = URL.createObjectURL(blob);
                    this.compressedSizeKb = sizeKb;
                    this.selfieReady = true;
                    this.stopCamera();
                } finally {
                    this.compressing = false;
                }
            },

            async captureCompressedBlob() {
                const video = this.$refs.video;
                const canvas = this.$refs.canvas;
                const sourceWidth = video.videoWidth || 640;
                const sourceHeight = video.videoHeight || 853;
                const maxLongSide = 1280;
                const scale = Math.min(1, maxLongSide / Math.max(sourceWidth, sourceHeight));

                canvas.width = Math.round(sourceWidth * scale);
                canvas.height = Math.round(sourceHeight * scale);
                
                const ctx = canvas.getContext('2d');
                if (this.facingMode === 'user') {
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                }
                
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                const qualities = [0.62, 0.55, 0.48, 0.42];

                for (const quality of qualities) {
                    const blob = await this.canvasToBlob(canvas, quality);
                    if (blob && blob.size <= this.maxPhotoKb * 1024) {
                        return blob;
                    }
                }

                return this.canvasToBlob(canvas, 0.38);
            },

            canvasToBlob(canvas, quality) {
                return new Promise((resolve) => {
                    canvas.toBlob((blob) => resolve(blob), 'image/jpeg', quality);
                });
            },

            resetSelfie() {
                this.resetSelfieData();
                this.startCamera();
            },

            resetSelfieData() {
                if (this.previewUrl) {
                    URL.revokeObjectURL(this.previewUrl);
                }

                this.previewUrl = '';
                this.selfieReady = false;
                this.compressedSizeKb = null;
                this.$refs.selfieInput.value = '';
            },

            stopCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach((track) => track.stop());
                    this.stream = null;
                    this.cameraReady = false;
                }
            },

            validateBeforeSubmit(event) {
                this.error = '';

                if (!this.selfieReady) {
                    event.preventDefault();
                    this.error = (this.facingMode === 'environment' || this.selectedStatus !== 'hadir') ? 'Ambil foto bukti dulu sebelum absen.' : 'Ambil selfie/foto bukti dulu sebelum absen.';
                }
            },
            
            toggleCamera() {
                this.facingMode = this.facingMode === 'user' ? 'environment' : 'user';
                this.stopCamera();
                this.startCamera();
            },
        };
    }

    // Polling: reload otomatis jika status absensi berubah
    @if(!$todayAttendance || $todayAttendance->status === 'belum_absen')
    (function () {
        const url = "{{ route('siswa.absensi.status') }}";
        setInterval(async function () {
            try {
                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                if (data.sudah_absen) location.reload();
            } catch {}
        }, 15000);
    })();
    @endif
</script>
@endpush
@endsection
