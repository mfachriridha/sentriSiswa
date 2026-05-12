@extends('layouts.app')

@section('title', 'Kehadiran - SentriSiswa')

@section('page-title', 'Kehadiran')

@section('content')
<div class="grid lg:grid-cols-5 gap-5">
    <div class="lg:col-span-3 space-y-5">

        @if($attendance)
        <div class="flex items-center gap-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl anim-up">
            <div class="w-11 h-11 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle-fill text-emerald-600 text-xl"></i>
            </div>
            <div class="flex-1">
                <p class="font-bold text-emerald-800">Sudah Presensi Hari Ini</p>
                <p class="text-sm text-emerald-700">{{ $attendance->created_at->format('H:i') }} WIB &middot;
                    @if($attendance->is_inside_geofence) Lokasi: valid @else Lokasi: di luar area @endif
                </p>
            </div>
            @if($attendance->selfie_path)
            <a href="{{ asset($attendance->selfie_path) }}" target="_blank" class="btn-outline !py-2 !px-3 text-xs">
                <i class="bi bi-image"></i> Lihat Foto
            </a>
            @endif
        </div>
        @endif

        <!-- Camera Selfie -->
        <div class="card anim-up" style="animation-delay:.08s">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center">
                        <i class="bi bi-camera-fill text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">Selfie Presensi</h3>
                        <p class="text-xs text-slate-500">Ambil foto selfie untuk bukti kehadiran</p>
                    </div>
                </div>
                <span class="badge badge-green">Aktif</span>
            </div>

            <div class="bg-slate-900 rounded-xl h-56 flex items-center justify-center mb-4 overflow-hidden" id="cameraPreview">
                <div id="noPhoto" class="text-center text-white/60">
                    <i class="bi bi-camera-fill text-5xl mb-3 block"></i>
                    <p class="text-sm font-medium">Kamera Siap</p>
                    <p class="text-xs text-white/40 mt-1">Pastikan wajah terlihat jelas &amp; pencahayaan cukup</p>
                </div>
                <img id="photoPreview" class="hidden w-full h-full object-cover">
            </div>

            <input type="file" id="selfieInput" accept="image/*" capture="user" class="hidden" onchange="handleSelfie(event)">

            <button onclick="document.getElementById('selfieInput').click()" class="btn-brand w-full !py-3" id="takePhotoBtn" {{ $attendance ? 'disabled' : '' }}>
                @if($attendance)
                    <i class="bi bi-check-circle"></i> Sudah Presensi
                @else
                    <i class="bi bi-camera"></i> Ambil Foto Selfie
                @endif
            </button>
        </div>

        <!-- Location -->
        <div class="card anim-up" style="animation-delay:.16s">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="bi bi-geo-alt-fill text-blue-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Deteksi Lokasi</h3>
                    <p class="text-xs text-slate-500">Geofencing &mdash; validasi area sekolah</p>
                </div>
            </div>

            <div id="locationStatus">
                <div class="flex items-center gap-3 p-4 bg-slate-50 border border-slate-200 rounded-xl mb-4">
                    <div class="w-9 h-9 bg-slate-100 rounded-full flex items-center justify-center shrink-0">
                        <i class="bi bi-hourglass-split text-slate-400 animate-spin"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-slate-600">Mendeteksi Lokasi...</p>
                        <p class="text-xs text-slate-500 mt-0.5">Mohon izinkan akses lokasi di browser</p>
                    </div>
                </div>
            </div>

            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Latitude</span>
                    <span id="latValue" class="font-mono font-semibold text-slate-800">—</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Longitude</span>
                    <span id="lngValue" class="font-mono font-semibold text-slate-800">—</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Akurasi</span>
                    <span id="accValue" class="font-semibold">—</span>
                </div>
            </div>
        </div>

        @if(!$attendance)
        <button onclick="submitPresensi()" id="submitBtn" disabled class="btn-primary w-full !py-3.5 text-base anim-up opacity-50 cursor-not-allowed" style="animation-delay:.24s">
            <span class="btn-text"><i class="bi bi-check-circle"></i> Kirim Presensi</span>
            <span class="btn-loading hidden"><i class="bi bi-hourglass-split animate-spin"></i> Mengirim...</span>
        </button>
        @endif
    </div>

    <!-- Right: Map -->
    <div class="lg:col-span-2 space-y-4">
        <div class="card anim-up" style="animation-delay:.12s">
            <h3 class="font-bold text-slate-900 mb-3">Area Sekolah</h3>
            <div class="bg-slate-100 rounded-xl h-48 flex items-center justify-center relative overflow-hidden">
                <svg width="200" height="160" viewBox="0 0 200 160" class="opacity-80">
                    <polygon points="30,20 70,140 170,120 150,30 80,15 30,20"
                        fill="rgba(0,98,160,0.15)" stroke="#0062a0" stroke-width="2" stroke-dasharray="4,3"/>
                    <text x="100" y="85" text-anchor="middle" fill="#0062a0" font-size="10" font-weight="600">Area Absen</text>
                    <circle id="userDot" cx="100" cy="70" r="6" fill="#059669" stroke="white" stroke-width="2" style="display:none"/>
                    <text id="userLabel" x="100" y="58" text-anchor="middle" fill="#059669" font-size="8" font-weight="700" style="display:none">ANDA</text>
                </svg>
            </div>
            <div class="mt-3 text-xs text-slate-500 text-center">
                <i class="bi bi-geo-alt text-emerald-600"></i> Titik lokasi Anda saat ini
            </div>
        </div>

        <div class="card anim-up" style="animation-delay:.2s">
            <h3 class="font-bold text-slate-900 mb-3">Batas Area Absen</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Status</span>
                    <span id="geoStatus" class="badge badge-green">—</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Titik Polygon</span>
                    <span class="font-semibold">6 titik</span>
                </div>
            </div>
            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700">
                <i class="bi bi-info-circle-fill mr-1"></i> Presensi hanya dapat dilakukan di dalam area yang telah ditentukan.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentLat = null, currentLng = null, currentAccuracy = null;
let selfieFile = null;
let locationReady = false;

function handleSelfie(event) {
    const file = event.target.files[0];
    if (!file) return;
    selfieFile = file;
    document.getElementById('noPhoto').classList.add('hidden');
    const preview = document.getElementById('photoPreview');
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('hidden');
    document.getElementById('takePhotoBtn').innerHTML = '<i class="bi bi-check-circle"></i> Foto Diambil';
    updateSubmitBtn();
}

function getLocation() {
    if (!navigator.geolocation) {
        document.getElementById('locationStatus').innerHTML = '<div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl mb-4"><div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-x-circle-fill text-red-600"></i></div><div class="flex-1"><p class="text-sm font-bold text-red-800">Geolokasi tidak didukung</p><p class="text-xs text-red-700 mt-0.5">Browser Anda tidak mendukung geolokasi.</p></div></div>';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function(pos) {
            currentLat = pos.coords.latitude;
            currentLng = pos.coords.longitude;
            currentAccuracy = pos.coords.accuracy;
            locationReady = true;

            document.getElementById('latValue').textContent = currentLat.toFixed(7);
            document.getElementById('lngValue').textContent = currentLng.toFixed(7);
            document.getElementById('accValue').textContent = '~' + Math.round(currentAccuracy) + ' meter';
            document.getElementById('accValue').classList.add('text-emerald-600');

            document.getElementById('locationStatus').innerHTML = '<div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl mb-4"><div class="w-9 h-9 bg-emerald-100 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-check-circle-fill text-emerald-600"></i></div><div class="flex-1"><p class="text-sm font-bold text-emerald-800">Lokasi Terdeteksi</p><p class="text-xs text-emerald-700 mt-0.5">Akurasi ' + Math.round(currentAccuracy) + ' meter</p></div></div>';

            document.getElementById('geoStatus').textContent = '—';
            document.getElementById('geoStatus').className = 'badge badge-blue';

            document.getElementById('userDot').style.display = '';
            document.getElementById('userLabel').style.display = '';

            updateSubmitBtn();
        },
        function(err) {
            document.getElementById('locationStatus').innerHTML = '<div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl mb-4"><div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-geo-alt-fill text-red-600"></i></div><div class="flex-1"><p class="text-sm font-bold text-red-800">Lokasi tidak dapat diakses</p><p class="text-xs text-red-700 mt-0.5">Izinkan akses lokasi di pengaturan browser, lalu refresh.</p></div></div>';
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
}

function updateSubmitBtn() {
    const btn = document.getElementById('submitBtn');
    if (!btn) return;
    if (selfieFile && locationReady) {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

function setLoading(btn, loading) {
    const t = btn.querySelector('.btn-text'), l = btn.querySelector('.btn-loading');
    if (t) t.classList.toggle('hidden', loading);
    if (l) l.classList.toggle('hidden', !loading);
    btn.disabled = loading;
}

function submitPresensi() {
    const btn = document.getElementById('submitBtn');
    if (!selfieFile || !locationReady) {
        showToast('Ambil foto selfie dan tunggu lokasi terdeteksi.', 'warning');
        return;
    }

    setLoading(btn, true);

    const formData = new FormData();
    formData.append('selfie', selfieFile);
    formData.append('latitude', currentLat);
    formData.append('longitude', currentLng);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("siswa.kehadiran.store") }}', {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.warning) {
                document.getElementById('geoStatus').textContent = 'Di Luar Area';
                document.getElementById('geoStatus').className = 'badge badge-amber';
            } else {
                document.getElementById('geoStatus').textContent = 'Valid';
                document.getElementById('geoStatus').className = 'badge badge-green';
            }
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
            setLoading(btn, false);
        }
    })
    .catch(() => {
        showToast('Gagal mengirim presensi. Coba lagi.', 'error');
        setLoading(btn, false);
    });
}

getLocation();
</script>
@endpush
@endsection
