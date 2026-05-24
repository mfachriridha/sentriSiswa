@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Absensi</h1>
    <p class="mt-1 text-base text-gray-500">Catat kehadiran harian Anda</p>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

{{-- Today's attendance card --}}
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
            <p class="text-sm text-gray-400 mt-1">Jam absen: {{ $startTime }} — {{ $endTime }}</p>
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
                  class="space-y-4"
                  x-data="attendanceCamera()"
                  @submit="validateBeforeSubmit($event)">
                @csrf
                <input x-ref="selfieInput" type="file" name="selfie" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">

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
                            <p class="mt-1 text-sm text-gray-500">Nyalakan kamera, ambil selfie, lalu kirim absensi.</p>
                            <p x-show="error" x-text="error" class="mt-3 text-sm text-red-600"></p>
                            @error('selfie')
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
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                                Ambil Selfie
                            </button>
                            <button type="button"
                                    x-show="previewUrl"
                                    @click="resetSelfie()"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                                Ulangi
                            </button>
                            <button type="submit"
                                    :disabled="!selfieReady"
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
                    Belum waktunya absen. Absen dimulai pukul {{ $startTime }}.
                @else
                    Waktu absen sudah berakhir.
                @endif
            </p>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function attendanceCamera() {
        return {
            cameraReady: false,
            error: '',
            previewUrl: '',
            selfieReady: false,
            stream: null,

            async startCamera() {
                this.error = '';

                if (!navigator.mediaDevices?.getUserMedia) {
                    this.error = 'Browser tidak mendukung akses kamera.';
                    return;
                }

                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'user' },
                        audio: false,
                    });
                    this.$refs.video.srcObject = this.stream;
                    await this.$refs.video.play();
                    this.cameraReady = true;
                } catch (error) {
                    this.error = 'Akses kamera ditolak atau kamera tidak tersedia.';
                }
            },

            captureSelfie() {
                this.error = '';

                if (!this.cameraReady) {
                    this.error = 'Nyalakan kamera dulu.';
                    return;
                }

                const video = this.$refs.video;
                const canvas = this.$refs.canvas;
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 480;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                canvas.toBlob((blob) => {
                    if (!blob) {
                        this.error = 'Gagal mengambil selfie.';
                        return;
                    }

                    if (this.previewUrl) {
                        URL.revokeObjectURL(this.previewUrl);
                    }

                    const file = new File([blob], `selfie-${Date.now()}.jpg`, { type: 'image/jpeg' });
                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    this.$refs.selfieInput.files = transfer.files;
                    this.previewUrl = URL.createObjectURL(blob);
                    this.selfieReady = true;
                    this.stopCamera();
                }, 'image/jpeg', 0.9);
            },

            resetSelfie() {
                if (this.previewUrl) {
                    URL.revokeObjectURL(this.previewUrl);
                }

                this.previewUrl = '';
                this.selfieReady = false;
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
                if (!this.selfieReady) {
                    event.preventDefault();
                    this.error = 'Ambil selfie dulu sebelum absen.';
                }
            },
        };
    }
</script>
@endpush

{{-- Monthly stats --}}
<div class="mt-6 rounded-xl border border-gray-200 bg-white p-8">
    <h2 class="text-lg font-semibold text-gray-900 mb-6">Ringkasan Bulan Ini</h2>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-center">
            <p class="text-2xl font-bold text-green-700">{{ $stats['hadir'] }}</p>
            <p class="text-sm font-medium text-green-600 mt-1">Hadir</p>
        </div>
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-center">
            <p class="text-2xl font-bold text-amber-700">{{ $stats['terlambat'] }}</p>
            <p class="text-sm font-medium text-amber-600 mt-1">Terlambat</p>
        </div>
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-center">
            <p class="text-2xl font-bold text-blue-700">{{ $stats['izin'] }}</p>
            <p class="text-sm font-medium text-blue-600 mt-1">Izin</p>
        </div>
        <div class="rounded-lg border border-purple-200 bg-purple-50 p-4 text-center">
            <p class="text-2xl font-bold text-purple-700">{{ $stats['sakit'] }}</p>
            <p class="text-sm font-medium text-purple-600 mt-1">Sakit</p>
        </div>
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-center">
            <p class="text-2xl font-bold text-red-700">{{ $stats['alpha'] }}</p>
            <p class="text-sm font-medium text-red-600 mt-1">Alpha</p>
        </div>
    </div>
</div>
@endsection
