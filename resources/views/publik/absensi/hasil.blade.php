@include('partials.head')
<body class="relative min-h-screen overflow-x-hidden bg-slate-50 font-sans antialiased flex items-center justify-center py-12 px-4">
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-primary/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-teal-500/5 blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <!-- Header -->
            <div class="bg-primary px-8 py-6 text-white">
                <p class="text-primary-content/70 text-xs font-medium uppercase tracking-wider mb-1">Laporan Absensi</p>
                <h1 class="text-xl font-bold">{{ $aksesToken->tanggal->translatedFormat('l, d F Y') }}</h1>
                <p class="text-primary-content/80 text-sm mt-1">{{ $siswa->class->name ?? '-' }}</p>
            </div>

            <div class="p-8">
                <!-- Siswa info -->
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                    @if ($siswa->photo)
                        <img src="{{ asset('storage/' . $siswa->photo) }}" class="w-14 h-14 rounded-full object-cover border-2 border-slate-100">
                    @else
                        <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xl">
                            {{ strtoupper(substr($siswa->user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-bold text-slate-800">{{ $siswa->user->name }}</p>
                        <p class="text-slate-500 text-sm">NIS: {{ $siswa->nis }} | NISN: {{ $siswa->nisn }}</p>
                    </div>
                </div>

                @if ($absensi)
                    <!-- Status -->
                    <div class="text-center mb-6">
                        @php
                            $statusMap = [
                                'hadir'       => ['label' => 'Hadir', 'class' => 'badge-success'],
                                'terlambat'   => ['label' => 'Terlambat', 'class' => 'badge-warning'],
                                'izin'        => ['label' => 'Izin', 'class' => 'badge-info'],
                                'sakit'       => ['label' => 'Sakit', 'class' => 'badge-info'],
                                'alpha'       => ['label' => 'Tidak Hadir', 'class' => 'badge-error'],
                                'belum_absen' => ['label' => 'Belum Absen', 'class' => 'badge-ghost'],
                            ];
                            $s = $statusMap[$absensi->status] ?? ['label' => $absensi->status, 'class' => 'badge-ghost'];
                        @endphp
                        <span class="badge {{ $s['class'] }} badge-lg text-base px-6 py-3">{{ $s['label'] }}</span>
                    </div>

                    <!-- Detail -->
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-slate-50">
                            <span class="text-slate-500">Jam Check-in</span>
                            <span class="font-semibold text-slate-800">
                                {{ $absensi->check_in_time ? \Carbon\Carbon::parse($absensi->check_in_time)->format('H:i') . ' WIB' : '-' }}
                            </span>
                        </div>
                        @if ($absensi->selfie_path)
                        <div class="pt-2">
                            <p class="text-slate-500 mb-2">Foto Absensi</p>
                            <img src="{{ asset('storage/' . $absensi->selfie_path) }}"
                                 class="w-full rounded-xl object-cover max-h-64 border border-slate-100">
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-slate-500 text-sm">Belum ada data absensi untuk hari ini.</p>
                    </div>
                @endif
            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-4">
            Link ini hanya berlaku sampai akhir hari ini.
        </p>
    </div>
</body>
</html>
