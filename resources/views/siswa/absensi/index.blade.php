@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Absensi</h1>
    <p class="mt-1 text-base text-gray-500">Catat kehadiran harian Anda</p>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<div class="rounded-xl border border-gray-200 bg-white p-8">
    <div class="mb-6 flex items-center justify-between gap-4">
        <h2 class="text-lg font-semibold text-gray-900">Absen Hari Ini</h2>
        <a href="{{ route('siswa.absensi.riwayat') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
            Riwayat
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-start">
        <div>
            <p class="text-base text-gray-600">{{ now()->translatedFormat('l, d F Y') }}</p>
            <p class="mt-1 text-sm text-gray-400">Jam absen: {{ $startTime }} — {{ $endTime }}</p>
            <p class="mt-1 text-sm text-gray-400">Waktu aplikasi sekarang: {{ $currentTimeLabel }}</p>
        </div>

        @if($todayAttendance)
            <div class="space-y-4 lg:text-right">
                @php
                    $statusConfig = [
                        'hadir' => ['bg-green-50 text-green-700', 'Hadir'],
                        'terlambat' => ['bg-amber-50 text-amber-700', 'Terlambat'],
                        'izin' => ['bg-blue-50 text-blue-700', 'Izin'],
                        'sakit' => ['bg-purple-50 text-purple-700', 'Sakit'],
                        'alpha' => ['bg-red-50 text-red-700', 'Alpha'],
                    ];
                    [$badgeClass, $statusLabel] = $statusConfig[$todayAttendance->status] ?? ['bg-gray-50 text-gray-700', $todayAttendance->status];
                @endphp
                <span class="inline-flex items-center rounded-full {{ $badgeClass }} px-4 py-2 text-base font-semibold">
                    {{ $statusLabel }}
                </span>
                @if($todayAttendance->check_in_time)
                    <p class="text-sm text-gray-500">Absen pukul {{ $todayAttendance->check_in_time->format('H:i') }}</p>
                @endif
                @if($todayAttendance->distance_meters !== null)
                    <p class="text-sm text-gray-500">
                        @if($todayAttendance->distance_meters == 0)
                            Lokasi di dalam area absensi
                        @else
                            Lokasi {{ number_format($todayAttendance->distance_meters, 0) }} m dari area
                        @endif
                    </p>
                @endif
                @if($todayAttendance->selfie_path)
                    <img src="{{ asset('storage/'.$todayAttendance->selfie_path) }}"
                         alt="Selfie absensi"
                         class="h-32 w-32 rounded-lg border border-gray-200 object-cover lg:ml-auto">
                @endif
            </div>
        @elseif($canCheckIn)
            <form method="POST"
                  action="{{ route('siswa.absensi.store') }}"
                  enctype="multipart/form-data"
                  class="space-y-5 lg:col-span-2"
                  x-data="attendanceForm({ geofenceActive: @js($geofenceActive), maxPhotoKb: 300 })"
                  @submit="validateBeforeSubmit($event)">
                @csrf
                <input x-ref="selfieInput" type="file" name="selfie" accept="image/jpeg,image/webp" class="hidden">
                <input type="hidden" name="latitude" x-model="latitude">
                <input type="hidden" name="longitude" x-model="longitude">
                <input type="hidden" name="accuracy" x-model="accuracy">

                @if($geofenceActive)
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Lokasi GPS</h3>
                                <p class="mt-1 text-sm text-gray-500">Aktifkan lokasi sebelum absen. Jika akurasi masih rendah, tekan refresh.</p>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <button type="button"
                                        @click="getGpsLocation()"
                                        :disabled="gpsLoading"
                                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span x-text="gpsReady ? 'Refresh Lokasi' : 'Aktifkan Lokasi'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 rounded-lg border border-gray-200 bg-white p-3">
                            <p x-show="gpsLoading" class="text-sm text-gray-600">Mengambil lokasi...</p>
                            <p x-show="gpsError" x-text="gpsError" class="text-sm text-red-600"></p>
                            <template x-if="gpsReady">
                                <div>
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium" :class="accuracy <= 100 ? 'text-green-700' : accuracy <= 500 ? 'text-amber-700' : 'text-red-700'">
                                            Lokasi ditemukan, akurasi ±<span x-text="Math.round(accuracy)"></span> m
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <span x-text="Number(latitude).toFixed(7)"></span>, <span x-text="Number(longitude).toFixed(7)"></span>
                                        </p>
                                        <p x-show="accuracy > 500" class="text-xs text-amber-600">Akurasi masih rendah. Coba tekan Refresh Lokasi sebelum absen.</p>
                                    </div>
                                    <div x-show="locationStatus" class="mt-2 rounded-lg border px-3 py-2 text-sm font-medium"
                                         :class="{
                                             'border-green-200 bg-green-50 text-green-700': locationStatus === 'inside',
                                             'border-amber-200 bg-amber-50 text-amber-700': locationStatus === 'tolerance',
                                             'border-red-200 bg-red-50 text-red-700': locationStatus === 'outside'
                                         }">
                                        <span x-text="locationMessage"></span>
                                        <span x-show="locationDistance !== null && locationDistance > 0"
                                              class="ml-1 text-xs opacity-75">(<span x-text="Math.round(locationDistance)"></span> m dari area)</span>
                                    </div>
                                    <p x-show="locationChecking" class="mt-2 text-sm text-gray-500">Memeriksa lokasi...</p>
                                </div>
                            </template>
                            <p x-show="!gpsLoading && !gpsError && !gpsReady" class="text-sm text-gray-400">Lokasi belum diambil.</p>
                        </div>
                    </div>
                @endif

                <div class="grid gap-4 sm:grid-cols-[220px_1fr]">
                    <div class="relative aspect-[3/4] overflow-hidden rounded-lg border border-gray-200 bg-gray-100">
                        <video x-ref="video"
                               x-show="cameraReady && !previewUrl"
                               class="h-full w-full object-cover"
                               playsinline
                               muted></video>
                        <img x-show="previewUrl"
                             :src="previewUrl"
                             alt="Preview selfie"
                             class="h-full w-full object-cover">
                        <div x-show="!cameraReady && !previewUrl" class="flex h-full items-center justify-center p-4 text-center text-sm text-gray-500">
                            Kamera belum aktif
                        </div>
                    </div>

                    <div class="flex flex-col justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Selfie wajib untuk absen.</p>
                            <p x-show="compressedSizeKb" class="mt-2 text-sm text-gray-500">
                                Ukuran foto: <span x-text="compressedSizeKb"></span> KB
                            </p>
                            <p x-show="error" x-text="error" class="mt-3 text-sm text-red-600"></p>
                            @error('selfie')
                                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('latitude')
                                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('longitude')
                                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button type="button"
                                    x-show="!cameraReady"
                                    @click="startCamera()"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                                Nyalakan Kamera
                            </button>
                            <button type="button"
                                    x-show="cameraReady"
                                    @click="captureSelfie()"
                                    :disabled="compressing"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60">
                                <span x-text="compressing ? 'Memproses...' : 'Ambil Selfie'"></span>
                            </button>
                            <button type="button"
                                    x-show="previewUrl"
                                    @click="resetSelfie()"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                                Ulangi
                            </button>
                            <button type="submit"
                                    :disabled="!canSubmit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:cursor-not-allowed disabled:bg-gray-300">
                                Absen Sekarang
                            </button>
                        </div>
                    </div>
                </div>
                <canvas x-ref="canvas" class="hidden"></canvas>
            </form>
        @else
            <p class="text-base text-gray-400">
                @if(now()->format('H:i') < $startTime)
                    Belum waktunya absen. Waktu aplikasi sekarang {{ $currentTimeLabel }}, absen dimulai pukul {{ $startTime }}.
                @else
                    Waktu absen sudah berakhir. Waktu aplikasi sekarang {{ $currentTimeLabel }}, batas absen pukul {{ $endTime }}.
                @endif
            </p>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function attendanceForm(config) {
        return {
            cameraReady: false,
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
                if (!this.selfieReady || this.compressing) {
                    return false;
                }

                if (this.geofenceActive && !this.gpsReady) {
                    return false;
                }

                if (this.geofenceActive && this.locationStatus === 'outside') {
                    return false;
                }

                return true;
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
                if (this.previewUrl) {
                    URL.revokeObjectURL(this.previewUrl);
                }

                this.previewUrl = '';
                this.selfieReady = false;
                this.compressedSizeKb = null;
                this.$refs.selfieInput.value = '';
                this.startCamera();
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

                if (this.geofenceActive && !this.gpsReady) {
                    event.preventDefault();
                    this.error = 'Aktifkan lokasi GPS terlebih dahulu.';
                    return;
                }

                if (this.geofenceActive && this.locationStatus === 'outside') {
                    event.preventDefault();
                    this.error = 'Lokasi Anda di luar area absensi.';
                    return;
                }

                if (!this.selfieReady) {
                    event.preventDefault();
                    this.error = 'Ambil selfie dulu sebelum absen.';
                }
            },
        };
    }
</script>
@endpush

<div class="mt-6 rounded-xl border border-gray-200 bg-white p-8">
    <h2 class="mb-6 text-lg font-semibold text-gray-900">Ringkasan Bulan Ini</h2>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
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
@endsection
