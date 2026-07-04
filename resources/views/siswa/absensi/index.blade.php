@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Absensi Hari Ini</h1>
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
    <span>Jam absen: <strong class="text-gray-700">{{ $startTime }} – {{ $endTime }}</strong></span>
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
                    'terlambat' => ['bg-amber-50 text-amber-700 border-amber-200',   'Terlambat', 'bg-amber-100'],
                    'izin'      => ['bg-blue-50 text-blue-700 border-blue-200',      'Izin',      'bg-blue-100'],
                    'sakit'     => ['bg-purple-50 text-purple-700 border-purple-200','Sakit',     'bg-purple-100'],
                    'alpha'     => ['bg-red-50 text-red-700 border-red-200',         'Alpha',     'bg-red-100'],
                ];
                [$badgeClass, $statusLabel, $bgClass] = $statusConfig[$todayAttendance->status] ?? ['bg-gray-50 text-gray-700 border-gray-200', $todayAttendance->status, 'bg-gray-100'];
            @endphp

            <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                {{-- Selfie thumbnail --}}
                <div class="shrink-0">
                    @if($todayAttendance->path_selfie)
                        <img src="{{ asset('storage/'.$todayAttendance->path_selfie) }}"
                             alt="Selfie absensi"
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
                                <span>Absen pukul <strong class="text-gray-700">{{ $todayAttendance->waktu_masuk->format('H:i') }}</strong></span>
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
                                        Di dalam area absensi
                                    @else
                                        {{ number_format($todayAttendance->jarak_meter, 0) }} m dari area absensi
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-lg {{ $bgClass }} px-4 py-3 text-sm font-medium text-gray-700">
                        Absensi Anda hari ini sudah tercatat. Sampai jumpa besok!
                    </div>
                </div>
            </div>

        @elseif($canCheckIn)
            {{-- ABSEN FORM --}}
            <form method="POST"
                  action="{{ route('siswa.absensi.store') }}"
                  enctype="multipart/form-data"
                  x-data="attendanceForm({ geofenceActive: @js($geofenceActive), maxPhotoKb: 300 })"
                  @submit="validateBeforeSubmit($event)">
                @csrf
                <input x-ref="selfieInput" type="file" name="selfie" accept="image/jpeg,image/webp" class="hidden">
                <input type="hidden" name="latitude" x-model="latitude">
                <input type="hidden" name="longitude" x-model="longitude">
                <input type="hidden" name="accuracy" x-model="accuracy">

                <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                    {{-- Selfie preview --}}
                    <div class="shrink-0">
                        <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50" style="width:112px;">
                            <div class="relative aspect-[3/4] bg-gray-100">
                                <img x-cloak x-show="previewUrl" :src="previewUrl" alt="Preview selfie" class="h-full w-full object-cover">
                                <div x-cloak x-show="!previewUrl" class="flex h-full items-center justify-center p-3 text-center text-xs text-gray-400">
                                    Selfie akan muncul di sini
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- GPS + action --}}
                    <div class="flex-1 space-y-4">
                        @if($geofenceActive)
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-xs font-medium text-gray-700">Lokasi GPS</span>
                                        <span x-cloak x-show="gpsReady" class="text-xs" :class="accuracy <= 100 ? 'text-green-600' : accuracy <= 500 ? 'text-amber-600' : 'text-red-600'">
                                            ±<span x-text="Math.round(accuracy)"></span> m
                                        </span>
                                    </div>
                                    <button type="button"
                                            @click="getGpsLocation()"
                                            :disabled="gpsLoading"
                                            class="shrink-0 inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-60">
                                        <span x-text="gpsReady ? 'Refresh' : 'Aktifkan GPS'"></span>
                                    </button>
                                </div>
                                <div class="mt-2 space-y-1 text-xs">
                                    <p x-cloak x-show="gpsLoading" class="text-gray-500">Mengambil lokasi...</p>
                                    <p x-cloak x-show="gpsError" x-text="gpsError" class="text-red-600"></p>
                                    <template x-if="gpsReady">
                                        <div>
                                            <p x-cloak x-show="locationStatus" class="font-medium" :class="{
                                                'text-green-700': locationStatus === 'inside',
                                                'text-amber-700': locationStatus === 'tolerance',
                                                'text-red-700': locationStatus === 'outside'
                                            }">
                                                <span x-text="locationMessage"></span>
                                                <span x-cloak x-show="locationDistance !== null && locationDistance > 0" class="opacity-75">
                                                    (<span x-text="Math.round(locationDistance)"></span> m)
                                                </span>
                                            </p>
                                            <p x-cloak x-show="locationChecking" class="text-gray-500">Memeriksa lokasi...</p>
                                            <p x-cloak x-show="accuracy > 500" class="text-amber-600">Akurasi rendah, tekan Refresh.</p>
                                        </div>
                                    </template>
                                    <p x-cloak x-show="!gpsLoading && !gpsError && !gpsReady" class="text-gray-400">Lokasi belum diambil.</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2">
                                <svg class="h-4 w-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-xs font-medium text-blue-700">Lokasi GPS tidak diperlukan untuk sesi ini.</span>
                            </div>
                        @endif

                        <div>
                            <button type="button"
                                    @click="openSelfieModal()"
                                    :disabled="!canOpenSelfieModal"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white shadow-sm transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:cursor-not-allowed disabled:bg-gray-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Absen Sekarang
                            </button>
                            <p class="mt-2 text-xs" :class="canOpenSelfieModal ? 'text-green-700' : 'text-amber-700'" x-text="openDisabledMessage"></p>
                        </div>

                        <div class="space-y-1 text-sm">
                            <p x-cloak x-show="compressedSizeKb" class="text-gray-500">Ukuran foto: <span x-text="compressedSizeKb"></span> KB</p>
                            <p x-cloak x-show="error" x-text="error" class="text-red-600"></p>
                            @error('selfie')
                                <p class="text-red-600">{{ $message }}</p>
                            @enderror
                            @error('latitude')
                                <p class="text-red-600">{{ $message }}</p>
                            @enderror
                            @error('longitude')
                                <p class="text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Camera modal --}}
                <div x-cloak
                     x-show="modalOpen"
                     x-transition.opacity
                     class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4"
                     @keydown.escape.window="cancelSelfieModal()">
                    <div class="w-full max-w-3xl rounded-2xl bg-white shadow-xl" @click.outside="cancelSelfieModal()">
                        <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-4 py-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Ambil Selfie Absensi</h3>
                                <p class="mt-1 text-sm text-gray-500">Pastikan wajah terlihat jelas sebelum menekan Absen Sekarang.</p>
                            </div>
                            <button type="button"
                                    @click="cancelSelfieModal()"
                                    class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
                                <span class="sr-only">Tutup</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="grid gap-6 px-4 py-3 md:grid-cols-[260px_1fr]">
                            <div class="relative aspect-[3/4] overflow-hidden rounded-xl border border-gray-200 bg-gray-100">
                                <video x-ref="video"
                                       x-cloak
                                       x-show="cameraReady && !previewUrl"
                                       class="h-full w-full object-cover"
                                       playsinline
                                       muted></video>
                                <img x-cloak x-show="previewUrl"
                                     :src="previewUrl"
                                     alt="Preview selfie"
                                     class="h-full w-full object-cover">
                                <div x-cloak x-show="!cameraReady && !previewUrl" class="flex h-full items-center justify-center p-4 text-center text-sm text-gray-500">
                                    Kamera belum aktif
                                </div>
                            </div>

                            <div class="flex flex-col justify-between gap-5">
                                <div class="space-y-3">
                                    <p class="text-sm font-medium text-gray-700">Ambil selfie dari kamera perangkat ini.</p>
                                    <p class="text-sm text-gray-500">Foto akan dikompresi otomatis maksimal 300 KB.</p>
                                    <p x-cloak x-show="compressedSizeKb" class="text-sm text-gray-500">
                                        Ukuran foto: <span x-text="compressedSizeKb"></span> KB
                                    </p>
                                    <p x-cloak x-show="error" x-text="error" class="text-sm text-red-600"></p>
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <button type="button"
                                            x-cloak
                                            x-show="!cameraReady && !previewUrl"
                                            @click="startCamera()"
                                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                                        Nyalakan Kamera
                                    </button>
                                    <button type="button"
                                            x-cloak
                                            x-show="cameraReady"
                                            @click="captureSelfie()"
                                            :disabled="compressing"
                                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60">
                                        <span x-text="compressing ? 'Memproses...' : 'Ambil Selfie'"></span>
                                    </button>
                                    <button type="button"
                                            x-cloak
                                            x-show="previewUrl"
                                            @click="resetSelfie()"
                                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                                        Ulangi
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 px-4 py-3 sm:flex-row sm:justify-end">
                            <button type="button"
                                    @click="cancelSelfieModal()"
                                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="!canSubmit"
                                    class="inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:cursor-not-allowed disabled:bg-gray-300">
                                Absen Sekarang
                            </button>
                        </div>
                    </div>
                </div>

                <canvas x-ref="canvas" class="hidden"></canvas>
            </form>

        @else
            {{-- OUTSIDE HOURS --}}
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <svg class="mb-4 h-12 w-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-gray-500">
                    @if(! $isWeekday)
                        Absensi hanya tersedia pada hari Senin sampai Jumat.
                    @elseif(now()->format('H:i') < $startTime)
                        Belum waktunya absen. Absen dimulai pukul {{ $startTime }}.
                    @else
                        Waktu absen sudah berakhir pukul {{ $endTime }}.
                    @endif
                </p>
            </div>
        @endif
    </div>

    {{-- SIDEBAR: Monthly stats --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="mb-4 text-base font-semibold text-gray-900">Ringkasan Bulan Ini</h2>

        @php
            $totalDays = $stats['hadir'] + $stats['terlambat'] + $stats['izin'] + $stats['sakit'] + $stats['alpha'];
            $totalHadir = $stats['hadir'] + $stats['terlambat'];
            $pct = $totalDays > 0 ? round($totalHadir / $totalDays * 100) : 0;
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
            <div class="flex items-center justify-between rounded-lg border border-amber-100 bg-amber-50 px-3 py-2.5">
                <span class="text-sm font-medium text-amber-700">Terlambat</span>
                <span class="text-lg font-bold text-amber-700">{{ $stats['terlambat'] }}</span>
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

            get canSubmit() {
                return this.selfieReady && !this.compressing;
            },

            get canOpenSelfieModal() {
                if (!this.geofenceActive) {
                    return true;
                }

                if (!this.gpsReady || this.gpsLoading || this.locationChecking) {
                    return false;
                }

                return ['inside', 'tolerance'].includes(this.locationStatus);
            },

            get openDisabledMessage() {
                if (!this.geofenceActive) {
                    return 'Lokasi GPS tidak diwajibkan. Silakan lanjut absen.';
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
                this.locationStatus = '';
                this.locationMessage = '';
                this.locationDistance = null;

                if (!navigator.geolocation) {
                    this.gpsLoading = false;
                    this.gpsError = 'Browser tidak mendukung geolokasi.';
                    return;
                }

                navigator.geolocation.getCurrentPosition((position) => {
                    this.latitude = String(position.coords.latitude);
                    this.longitude = String(position.coords.longitude);
                    this.accuracy = String(position.coords.accuracy || 0);
                    this.gpsReady = true;
                    this.gpsLoading = false;
                    this.gpsError = '';
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
                            facingMode: 'user',
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
                const maxLongSide = 720;
                const scale = Math.min(1, maxLongSide / Math.max(sourceWidth, sourceHeight));

                canvas.width = Math.round(sourceWidth * scale);
                canvas.height = Math.round(sourceHeight * scale);
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

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
                    this.error = 'Ambil selfie dulu sebelum absen.';
                }
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
