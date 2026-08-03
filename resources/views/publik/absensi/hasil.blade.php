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
                <p class="text-primary-content/70 text-xs font-medium uppercase tracking-wider mb-1">Presensi Hari Ini</p>
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
                         alt="Foto presensi {{ $siswa->pengguna->nama }}"
                         class="w-full rounded-2xl border border-slate-100 object-cover aspect-[4/5] max-h-96">
                    <p class="text-center text-xs text-slate-400 mt-2">
                        Foto diambil saat presensi
                        @if ($absensi->waktu_masuk)
                            pukul {{ $absensi->waktu_masuk->format('H:i') }} WIB
                        @endif
                    </p>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center">
                        <p class="text-sm text-slate-500">
                            @if (! $absensi || $absensi->status === 'belum_absen')
                                Anak Anda belum melakukan presensi hari ini.
                            @elseif ($absensi->status === 'alpha')
                                Anak Anda tidak hadir tanpa keterangan hari ini.
                            @else
                                Tidak ada foto presensi karena anak Anda {{ strtolower($statusLabel) }} hari ini.
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

            <div class="grid grid-cols-2 gap-3 pb-5 border-b border-slate-100">
                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3.5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Sisa Poin Disiplin</p>
                    <p class="mt-1 text-2xl font-bold {{ $warnaPoin }}">
                        {{ $poin }}<span class="text-xs font-normal text-slate-400">/100</span>
                    </p>
                </div>
                <div class="rounded-xl border border-red-200 bg-red-50/70 p-3.5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Akumulasi Alpha</p>
                    <p class="mt-1 text-2xl font-bold text-red-600">
                        {{ $alphaCount }} <span class="text-xs font-normal text-red-500">kali</span>
                    </p>
                </div>
            </div>

            @if($statusAlpha['kode'] !== 'normal')
                <div class="rounded-2xl border p-5 sm:p-6 text-sm shadow-sm transition-all {{ match($statusAlpha['kode']) {
                    'dikembalikan' => 'border-red-200 bg-red-50/80 text-red-900',
                    'sp2' => 'border-rose-200 bg-rose-50/80 text-rose-900',
                    'sp1' => 'border-amber-200 bg-amber-50/80 text-amber-900',
                    default => 'border-yellow-200 bg-yellow-50/80 text-yellow-900',
                } }}">
                    <p class="font-bold text-base flex items-center gap-2 mb-2">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-2.99L13.74 4a2 2 0 00-3.48 0L3.33 16.01A2 2 0 005.07 19z"/>
                        </svg>
                        @if($statusAlpha['kode'] === 'dikembalikan')
                            Pemberitahuan Penting Presensi
                        @elseif($statusAlpha['kode'] === 'sp2' || $statusAlpha['kode'] === 'sp1')
                            Perhatian Presensi Siswa
                        @else
                            Peringatan Dini Presensi
                        @endif
                    </p>
                    <p class="leading-relaxed">
                        @if($statusAlpha['kode'] === 'dikembalikan')
                            <strong class="font-semibold">{{ $siswa->pengguna->nama }}</strong> telah mencapai batas maksimal {{ $alphaCount }}x Alpha. Diharapkan Orang Tua/Wali dapat segera menghadap ke sekolah untuk proses pengembalian siswa kepada Orang Tua/Wali.
                        @elseif($statusAlpha['kode'] === 'sp2')
                            Akumulasi Alpha <strong class="font-semibold">{{ $siswa->pengguna->nama }}</strong> telah mencapai {{ $alphaCount }}x (SP 2). Diharapkan Orang Tua/Wali dapat menghadap ke sekolah untuk penandatanganan Surat Perjanjian 2 bersama Wali Kelas & Guru BK.
                        @elseif($statusAlpha['kode'] === 'sp1')
                            Akumulasi Alpha <strong class="font-semibold">{{ $siswa->pengguna->nama }}</strong> telah mencapai 3x (SP 1). Diharapkan Orang Tua/Wali dapat menghadap ke sekolah untuk penandatanganan Surat Perjanjian 1 bersama Wali Kelas & Guru BK.
                        @else
                            <strong class="font-semibold">{{ $siswa->pengguna->nama }}</strong> saat ini tercatat {{ $alphaCount }}x Alpha. Mohon bantu ingatkan <strong class="font-semibold">{{ $siswa->pengguna->nama }}</strong> agar selalu hadir tepat waktu.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        {{-- Card Riwayat Pelanggaran Siswa --}}
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-red-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Riwayat Pelanggaran</h2>
                        <p class="text-xs text-slate-500">Catatan kedisiplinan yang tercatat di sekolah</p>
                    </div>
                </div>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                    {{ $pelanggaran->count() }} Catatan
                </span>
            </div>

            @if ($pelanggaran->isEmpty())
                <div class="rounded-xl border border-dashed border-emerald-200 bg-emerald-50/60 p-4 text-center">
                    <div class="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-emerald-800">Tidak Ada Catatan Pelanggaran</p>
                    <p class="text-xs text-emerald-600 mt-0.5">Anak Anda memiliki catatan kedisiplinan yang sangat baik!</p>
                </div>
            @else
                <div class="space-y-2.5 max-h-80 overflow-y-auto pr-1">
                    @foreach ($pelanggaran as $item)
                        <div class="flex items-start justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/70 p-3.5 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-slate-800 text-sm break-words">{{ $item->nama_pelanggaran }}</span>
                                </div>
                                <p class="text-slate-500 font-medium">
                                    {{ $item->tanggal_pelanggaran?->translatedFormat('d M Y') }}
                                    @if ($item->kategori_pelanggaran)
                                        &middot; <span class="capitalize">{{ $item->kategori_pelanggaran }}</span>
                                    @endif
                                </p>
                                @if ($item->catatan)
                                    <p class="mt-1 text-slate-600 italic bg-white/80 p-2 rounded-lg border border-slate-100">
                                        "{{ $item->catatan }}"
                                    </p>
                                @endif
                            </div>
                            <span class="shrink-0 rounded-md bg-red-100 px-2 py-1 font-bold text-red-700 border border-red-200">
                                -{{ $item->pengurangan_poin }} Poin
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <p class="text-center text-xs text-slate-400">
            Link ini hanya berlaku sampai akhir hari ini.
        </p>
    </div>
</body>
</html>
