@include('partials.head')
<body class="relative min-h-screen overflow-x-hidden bg-slate-50 font-sans antialiased flex items-center justify-center py-12 px-4">
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-primary/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-teal-500/5 blur-3xl"></div>
    </div>

    @php
        $statusMap = [
            'hadir' => ['Hadir', 'bg-green-50 text-green-700 border-green-200'],
            'izin' => ['Izin', 'bg-indigo-50 text-indigo-700 border-indigo-200'],
            'sakit' => ['Sakit', 'bg-blue-50 text-blue-700 border-blue-200'],
            'dispensasi' => ['Dispensasi', 'bg-orange-50 text-orange-700 border-orange-200'],
            'alpha' => ['Tidak Hadir', 'bg-red-50 text-red-700 border-red-200'],
            'belum_absen' => ['Belum Absen', 'bg-slate-100 text-slate-600 border-slate-200'],
        ];
        [$statusLabel, $statusKelas] = $statusMap[$absensi?->status] ?? ['Belum Absen', 'bg-slate-100 text-slate-600 border-slate-200'];
        $warnaPoin = $poin > 75 ? 'text-green-600' : ($poin > 50 ? 'text-amber-600' : 'text-red-600');
    @endphp

    <div class="relative z-10 w-full max-w-md mx-auto space-y-4">
        {{-- Status hari ini: yang paling dicari orang tua saat membuka link. --}}
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="bg-primary px-6 py-5 text-white">
                <p class="text-primary-content/70 text-xs font-medium uppercase tracking-wider mb-1">Absensi Hari Ini</p>
                <h1 class="text-lg font-bold">{{ $aksesToken->tanggal->translatedFormat('l, d F Y') }}</h1>
            </div>

            <div class="p-6">
                <div class="flex items-center justify-between gap-3 mb-5">
                    <div>
                        <p class="font-bold text-slate-800 leading-tight">{{ $siswa->pengguna->nama }}</p>
                        <p class="text-slate-500 text-sm mt-0.5">{{ $siswa->kelas->nama ?? '-' }}</p>
                    </div>
                    <span class="shrink-0 rounded-full border px-4 py-1.5 text-sm font-semibold {{ $statusKelas }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                @if ($absensi?->path_selfie)
                    <img src="{{ asset('storage/' . $absensi->path_selfie) }}"
                         alt="Foto absensi {{ $siswa->pengguna->nama }}"
                         class="w-full rounded-2xl border border-slate-100 object-cover aspect-[4/5] max-h-96">
                    <p class="text-center text-xs text-slate-400 mt-2">
                        Foto diambil saat absen
                        @if ($absensi->waktu_masuk)
                            pukul {{ $absensi->waktu_masuk->format('H:i') }} WIB
                        @endif
                    </p>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center">
                        <p class="text-sm text-slate-500">
                            @if (! $absensi || $absensi->status === 'belum_absen')
                                Anak Anda belum melakukan absensi hari ini.
                            @elseif ($absensi->status === 'alpha')
                                Anak Anda tidak hadir tanpa keterangan hari ini.
                            @else
                                Tidak ada foto absensi karena anak Anda {{ strtolower($statusLabel) }} hari ini.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Data anak beserta sisa poin & jatah alpha. --}}
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 space-y-5">
            <div class="flex items-center gap-4 pb-5 border-b border-slate-100">
                @if ($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="{{ $siswa->pengguna->nama }}"
                         class="w-14 h-14 rounded-full object-cover border-2 border-slate-100">
                @else
                    <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xl">
                        {{ strtoupper(substr($siswa->pengguna->nama, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <p class="font-bold text-slate-800">{{ $siswa->pengguna->nama }}</p>
                    <p class="text-slate-500 text-sm">NIS: {{ $siswa->nis ?? '-' }} · NISN: {{ $siswa->nisn }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pb-5 border-b border-slate-100">
                <div>
                    <p class="text-sm font-medium text-slate-500">Sisa Poin Disiplin</p>
                    <p class="mt-1 text-2xl font-bold {{ $warnaPoin }}">
                        {{ $poin }}<span class="text-xs font-normal text-slate-400">/100</span>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">Akumulasi Alpha</p>
                    <p class="mt-1 text-2xl font-bold text-red-600">
                        {{ $alphaCount }}<span class="text-xs font-normal text-slate-400">/{{ $maxAlpha }}x</span>
                    </p>
                    <p class="text-xs text-red-600 font-medium">Sisa Jatah: {{ $sisaAlpha }}x</p>
                </div>
            </div>

            @if($statusAlpha['kode'] !== 'normal')
                <div class="rounded-xl border p-3.5 text-xs {{ match($statusAlpha['kode']) {
                    'wakasis' => 'border-red-200 bg-red-50 text-red-800',
                    'sp2' => 'border-rose-200 bg-rose-50 text-rose-800',
                    'sp1' => 'border-amber-200 bg-amber-50 text-amber-800',
                    default => 'border-yellow-200 bg-yellow-50 text-yellow-800',
                } }}">
                    <p class="font-bold flex items-center gap-1.5 mb-1">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-2.99L13.74 4a2 2 0 00-3.48 0L3.33 16.01A2 2 0 005.07 19z"/>
                        </svg>
                        Status Peringatan: {{ $statusAlpha['label'] }}
                    </p>
                    <p class="leading-relaxed">
                        @if($statusAlpha['kode'] === 'wakasis')
                            Batas maksimal Alpha {{ $maxAlpha }}x telah tercapai. Kasus ini diproses oleh Wakasis & pemanggilan orang tua.
                        @elseif($statusAlpha['kode'] === 'sp2')
                            Alpha telah mencapai {{ $alphaCount }}x. Diproses oleh Guru BK dengan Surat Peringatan 2 (SP2).
                        @elseif($statusAlpha['kode'] === 'sp1')
                            Alpha telah mencapai {{ $alphaCount }}x. Diproses oleh Wali Kelas & BK dengan Surat Peringatan 1 (SP1).
                        @else
                            Perhatian: Anak Anda telah mencatat {{ $alphaCount }}x Alpha pada periode ini.
                        @endif
                    </p>
                </div>
            @endif

            <p class="text-xs text-slate-400">
                Hitungan Alpha berlaku untuk Tahun Pelajaran {{ $tahunAjaran }}.
            </p>
        </div>

        <p class="text-center text-xs text-slate-400">
            Link ini hanya berlaku sampai akhir hari ini.
        </p>
    </div>
</body>
</html>
