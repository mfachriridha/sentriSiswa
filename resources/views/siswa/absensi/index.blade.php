@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Absensi</h1>
    <p class="mt-1 text-sm text-gray-500">Catat kehadiran harian Anda</p>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<div class="grid gap-6 lg:grid-cols-[420px_1fr] items-start">
    <div class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6">
        <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Absen Hari Ini</h2>
            <div class="mt-8 space-y-1">
                <p class="text-sm font-medium text-gray-700">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                <p class="text-sm text-gray-400">Jam absen: {{ $startTime }} - {{ $endTime }}</p>
                <p class="text-sm text-gray-400">Waktu aplikasi sekarang: {{ $currentTimeLabel }}</p>
            </div>
        </div>

        <a href="{{ route('siswa.absensi.riwayat') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
            Riwayat
        </a>
    </div>

    @if($todayAttendance && $todayAttendance->status !== 'belum_absen')
        <div class="grid gap-6 lg:grid-cols-[280px_auto]">
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                @if($todayAttendance->selfie_path)
                    <img src="{{ asset('storage/'.$todayAttendance->selfie_path) }}"
                         alt="Selfie absensi"
                         class="aspect-[3/4] w-full rounded-lg border border-gray-200 object-cover">
                @else
                    <div class="flex aspect-[3/4] w-full items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white p-4 text-center text-sm text-gray-400">
                        Selfie belum tersedia
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                @php
                    $statusConfig = [
                        'hadir' => ['bg-green-50 text-green-700 border-green-200', 'Hadir'],
                        'terlambat' => ['bg-amber-50 text-amber-700 border-amber-200', 'Terlambat'],
                        'izin' => ['bg-blue-50 text-blue-700 border-blue-200', 'Izin'],
                        'sakit' => ['bg-purple-50 text-purple-700 border-purple-200', 'Sakit'],
                        'alpha' => ['bg-red-50 text-red-700 border-red-200', 'Alpha'],
                    ];
                    [$badgeClass, $statusLabel] = $statusConfig[$todayAttendance->status] ?? ['bg-gray-50 text-gray-700 border-gray-200', $todayAttendance->status];
                @endphp

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold text-gray-900">Status Absensi</h3>
                    <span class="inline-flex items-center rounded-full border {{ $badgeClass }} px-4 py-2 text-sm font-semibold">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="mt-4 space-y-2 text-sm text-gray-500">
                    @if($todayAttendance->check_in_time)
                        <p>Absen pukul {{ $todayAttendance->check_in_time->format('H:i') }}</p>
                    @endif
                    @if($todayAttendance->distance_meters !== null)
                        <p>
                            @if($todayAttendance->distance_meters == 0)
                                Lokasi di dalam area absensi
                            @else
                                Lokasi {{ number_format($todayAttendance->distance_meters, 0) }} m dari area
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @elseif($canCheckIn)
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

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                @if($geofenceActive)
                    <div class="mb-4 flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-xs font-medium text-gray-700">Lokasi GPS</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span x-cloak x-show="gpsReady" class="text-xs" :class="accuracy <= 100 ? 'text-green-600' : accuracy <= 500 ? 'text-amber-600' : 'text-red-600'">
                                ±<span x-text="Math.round(accuracy)"></span> m
                            </span>
                            <button type="button"
                                    @click="getGpsLocation()"
                                    :disabled="gpsLoading"
                                    class="shrink-0 inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60">
                                <span x-text="gpsReady ? 'Refresh' : 'Aktifkan'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3 space-y-1">
                        <p x-cloak x-show="gpsLoading" class="text-xs text-gray-500">Mengambil lokasi...</p>
                        <p x-cloak x-show="gpsError" x-text="gpsError" class="text-xs text-red-600"></p>

                        <template x-if="gpsReady">
                            <div class="space-y-0.5">
                                <p x-cloak x-show="locationStatus" class="text-xs font-medium" :class="{
                                    'text-green-700': locationStatus === 'inside',
                                    'text-amber-700': locationStatus === 'tolerance',
                                    'text-red-700': locationStatus === 'outside'
                                }">
                                    <span x-text="locationMessage"></span>
                                    <span x-cloak x-show="locationDistance !== null && locationDistance > 0" class="opacity-75">
                                        (<span x-text="Math.round(locationDistance)"></span> m)
                                    </span>
                                </p>
                                <p x-cloak x-show="locationChecking" class="text-xs text-gray-500">Memeriksa lokasi...</p>
                                <p x-cloak x-show="accuracy > 500" class="text-xs text-amber-600">Akurasi rendah, tekan Refresh.</p>
                            </div>
                        </template>
                        <p x-cloak x-show="!gpsLoading && !gpsError && !gpsReady" class="text-xs text-gray-400">Lokasi belum diambil.</p>
                    </div>
                @else
                    <div class="mb-4 flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2">
                        <svg class="h-4 w-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-medium text-blue-700">Lokasi GPS tidak diperlukan untuk sesi ini.</span>
                    </div>
                @endif

                <div class="mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Selfie Absensi</h3>
                    <p class="mt-0.5 text-xs text-gray-500">Selfie wajib untuk absen hari ini.</p>
                </div>

                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                    <div class="relative aspect-[3/4] w-full bg-gray-100">
                        <img x-cloak x-show="previewUrl"
                             :src="previewUrl"
                             alt="Preview selfie"
                             class="h-full w-full object-cover">
                        <div x-cloak x-show="!previewUrl" class="flex h-full w-full items-center justify-center p-6 text-center text-sm text-gray-400">
                            Belum ada selfie
                        </div>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <button type="button"
                            @click="openSelfieModal()"
                            :disabled="!canOpenSelfieModal"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:cursor-not-allowed disabled:bg-gray-300">
                        Absen Sekarang
                    </button>

                    <p class="text-sm" :class="canOpenSelfieModal ? 'text-green-700' : 'text-amber-700'" x-text="openDisabledMessage"></p>
                    <p x-cloak x-show="compressedSizeKb" class="text-sm text-gray-500">
                        Ukuran foto: <span x-text="compressedSizeKb"></span> KB
                    </p>
                    <p x-cloak x-show="error" x-text="error" class="text-sm text-red-600"></p>
                    @error('selfie')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('latitude')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('longitude')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

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
        <p class="text-sm text-gray-400">
            @if(! $isWeekday)
                Absensi hanya tersedia pada hari Senin sampai Jumat.
            @elseif(now()->format('H:i') < $startTime)
                Belum waktunya absen. Waktu aplikasi sekarang {{ $currentTimeLabel }}, absen dimulai pukul {{ $startTime }}.
            @else
                Waktu absen sudah berakhir. Waktu aplikasi sekarang {{ $currentTimeLabel }}, batas absen pukul {{ $endTime }}.
            @endif
        </p>
    @endif
</div>{{-- left col --}}

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="mb-5 text-lg font-semibold text-gray-900">Ringkasan Bulan Ini</h2>
        <div class="grid grid-cols-2 gap-4">
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-center">
                <p class="text-2xl font-bold text-green-700">{{ $stats['hadir'] }}</p>
                <p class="mt-1 text-sm font-medium text-green-600">Hadir</p>
            </div>
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-center">
                <p class="text-2xl font-bold text-amber-700">{{ $stats['terlambat'] }}</p>
                <p class="mt-1 text-sm font-medium text-amber-600">Terlambat</p>
            </div>
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-center">
                <p class="text-2xl font-bold text-blue-700">{{ $stats['izin'] }}</p>
                <p class="mt-1 text-sm font-medium text-blue-600">Izin</p>
            </div>
            <div class="rounded-lg border border-purple-200 bg-purple-50 p-4 text-center">
                <p class="text-2xl font-bold text-purple-700">{{ $stats['sakit'] }}</p>
                <p class="mt-1 text-sm font-medium text-purple-600">Sakit</p>
            </div>
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-center">
                <p class="text-2xl font-bold text-red-700">{{ $stats['alpha'] }}</p>
                <p class="mt-1 text-sm font-medium text-red-600">Alpha</p>
            </div>
        </div>
    </div>
</div>{{-- end grid --}}

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

                return 'Tekan Refresh Lokasi untuk memeriksa lokasi.';
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
                    const response = await fetch('{{ route("siswa.absensi.check-location") }}', {
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
</script>
@endpush
@endsection
