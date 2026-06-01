@extends('layouts.app')

@section('title', 'Lokasi Absen')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Lokasi Absen</h1>
    <p class="mt-1 text-sm text-gray-500">Impor area absensi dari file KML (Google My Maps).</p>
</div>

<x-alert type="success" :message="session('success')" />

@if($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4">
        <ul class="list-disc pl-5 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Impor Area dari KML</h2>
        <p class="mb-4 text-sm text-gray-500">
            Buat area di <a href="https://mymaps.google.com" target="_blank" rel="noopener" class="text-primary hover:underline">Google My Maps</a>,
            ekspor sebagai KML, lalu unggah file di bawah.
            Hanya polygon yang akan dipakai sebagai area absensi.
        </p>

        <form method="POST" action="{{ route('admin.settings.attendance-location.update') }}" enctype="multipart/form-data"
              x-data="{ loading: false }" @submit="loading = true">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="kml_file" class="mb-2 block text-sm font-medium text-gray-700">File KML</label>
                    <input type="file"
                           id="kml_file"
                           name="kml_file"
                           accept=".kml"
                           required
                           class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-dark">
                    <p class="mt-1 text-xs text-gray-400">Format: KML. Maksimal 5 MB.</p>
                    @error('kml_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        :disabled="loading"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                    <span x-show="loading">
                        <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </span>
                    Unggah KML
                </button>
            </div>
        </form>

        @if($geofenceData)
            <div class="mt-6 border-t border-gray-200 pt-6">
                <form method="POST" action="{{ route('admin.settings.attendance-location.destroy') }}"
                      onsubmit="return confirm('Hapus lokasi absen yang sudah disimpan?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Lokasi Absen
                    </button>
                </form>
            </div>
        @endif
    </div>

    @if($geofenceData)
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">Toleransi Jarak</h2>
            <p class="mb-4 text-sm text-gray-500">
                Tambahkan toleransi di luar area polygon. Siswa yang berada di luar area tetapi dalam jarak toleransi masih bisa absen.
            </p>

            <form method="POST" action="{{ route('admin.settings.attendance-location.tolerance') }}" class="flex flex-wrap items-end gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="tolerance_meters" class="mb-2 block text-sm font-medium text-gray-700">Toleransi (meter)</label>
                    <input type="number"
                           id="tolerance_meters"
                           name="tolerance_meters"
                           value="{{ $toleranceMeters }}"
                           min="0"
                           max="500"
                           required
                           class="w-40 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <p class="mt-1 text-xs text-gray-400">0–500 meter. Area toleransi ditampilkan sebagai warna merah di peta.</p>
                    @error('tolerance_meters')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
                    Simpan Toleransi
                </button>
            </form>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">Area Absensi</h2>
            <p class="mb-2 text-sm text-gray-500">
                <span class="inline-block h-3 w-3 rounded-sm bg-teal-500/70 mr-1 align-middle"></span> Area polygon
                @if($toleranceMeters > 0)
                    &nbsp;&middot;&nbsp;
                    <span class="inline-block h-3 w-3 rounded-sm bg-red-400/40 mr-1 align-middle"></span> Toleransi ({{ $toleranceMeters }} m)
                @endif
            </p>
            <div id="geofence-map" class="h-[450px] overflow-hidden rounded-xl border border-gray-200 bg-gray-100"></div>
            <p class="mt-2 text-xs text-gray-400">{{ count($geofenceData['coordinates'] ?? []) }} titik koordinat terdeteksi.</p>
        </div>
    @endif
</div>

@if($geofenceData)
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var checkL = setInterval(function() {
                if (typeof L !== 'undefined') {
                    clearInterval(checkL);
                    initMap();
                }
            }, 50);

            function initMap() {
                var coordinates = @json($geofenceData['coordinates']);
                var tolerance = {{ $toleranceMeters }};
                var map = L.map('geofence-map').setView([0, 0], 15);

                var streets = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://carto.com/">CARTO</a> &copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
                }).addTo(map);

                var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19,
                    attribution: '&copy; Esri &mdash; Source: Esri, Maxar, Earthstar Geographics',
                });

                L.control.layers(
                    { 'Peta Jalan': streets, 'Satelit': satellite },
                    {},
                    { position: 'topright' },
                ).addTo(map);

                var points = coordinates.map(function(c) { return [c.lat, c.lng]; });
                var polygon = L.polygon(points, {
                    color: '#0d9488',
                    fillColor: '#0d9488',
                    fillOpacity: 0.2,
                    weight: 2,
                }).addTo(map);

                map.fitBounds(polygon.getBounds().pad(0.2));

                if (tolerance > 0 && typeof turfPolygon === 'function' && typeof turfBuffer === 'function') {
                    try {
                        var ring = coordinates.map(function(c) { return [c.lng, c.lat]; });
                        ring.push(ring[0]);
                        var turfPoly = turfPolygon([ring]);
                        var buffered = turfBuffer(turfPoly, tolerance / 1000, { units: 'kilometers' });
                        if (buffered && buffered.geometry && buffered.geometry.coordinates && buffered.geometry.coordinates[0]) {
                            var bufferCoords = buffered.geometry.coordinates[0].map(function(c) { return [c[1], c[0]]; });
                            L.polygon(bufferCoords, {
                                color: '#ef4444',
                                fillColor: '#ef4444',
                                fillOpacity: 0.1,
                                weight: 2,
                                dashArray: '6 4',
                            }).addTo(map);
                        }
                    } catch(e) {
                        console.warn('Buffer computation failed:', e);
                    }
                }
            }
        });
    </script>
    @endpush
@endif
@endsection
