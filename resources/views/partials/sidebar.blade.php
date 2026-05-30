<aside class="fixed inset-y-0 left-0 z-10 flex w-64 shrink-0 flex-col bg-white border-r border-gray-200">
    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200">
        <img src="{{ asset('storage/assets/logo/logo-sidebar.png') }}" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">
        <span class="text-lg font-semibold text-gray-900">Sentri Siswa</span>
    </div>

    <nav class="flex-1 overflow-y-auto px-4 py-4">
        <ul class="space-y-1">
            @if(auth()->user()->isAdmin())
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                              {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>
                        </svg>
                        Dashboard
                    </a>
                </li>

                <li class="pt-5 pb-2">
                    <span class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Manajemen Data</span>
                </li>

                <li>
                    <a href="{{ route('admin.guru.index') }}"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                               {{ request()->routeIs('admin.guru.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Guru
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.siswa.index') }}"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                               {{ request()->routeIs('admin.siswa.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Siswa
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kelas.index') }}"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                               {{ request()->routeIs('admin.kelas.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Kelas
                    </a>
                </li>

                <ul class="mt-2 space-y-1">
                    <li class="pt-5 pb-2">
                        <span class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Konfigurasi</span>
                    </li>

                    <li>
                        <a href="{{ route('admin.settings.attendance-time.index') }}"
                           class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                                  {{ request()->routeIs('admin.settings.attendance-time.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Waktu Absen
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.settings.attendance-location.index') }}"
                           class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                                  {{ request()->routeIs('admin.settings.attendance-location.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Lokasi Absen
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.settings.whatsapp.index') }}"
                           class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                                  {{ request()->routeIs('admin.settings.whatsapp.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            WhatsApp API
                        </a>
                    </li>
                </ul>
            @elseif(auth()->user()->isTeacher())
                @if(auth()->user()->isStudentAffairs())
                    <li class="pt-5 pb-2">
                        <span class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Kesiswaan</span>
                    </li>

                    <li>
                        <a href="{{ route('guru.monitoring.index') }}"
                           class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                                  {{ request()->routeIs('guru.monitoring.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Monitoring Siswa
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('guru.pelanggaran-siswa.index') }}"
                           class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                                  {{ request()->routeIs('guru.pelanggaran-siswa.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-2.99L13.74 4a2 2 0 00-3.48 0L3.33 16.01A2 2 0 005.07 19z"/>
                            </svg>
                            Pelanggaran Siswa
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('guru.jenis-pelanggaran.index') }}"
                           class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                                  {{ request()->routeIs('guru.jenis-pelanggaran.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Jenis Pelanggaran
                        </a>
                    </li>
                @endif

                <li class="pt-5 pb-2">
                    <span class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Akun</span>
                </li>

                <li>
                    <a href="{{ route('guru.profil') }}"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                              {{ request()->routeIs('guru.profil', 'guru.profil.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil Saya
                    </a>
                </li>
            @elseif(auth()->user()->isStudent())
                <li>
                    <a href="{{ route('siswa.dashboard') }}"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                              {{ request()->routeIs('siswa.dashboard') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>
                        </svg>
                        Dashboard
                    </a>
                </li>

                <li class="pt-5 pb-2">
                    <span class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Akademik</span>
                </li>

                <li>
                    <a href="{{ route('siswa.absensi') }}"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                              {{ request()->routeIs('siswa.absensi', 'siswa.absensi.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Absensi
                    </a>
                </li>

                <li>
                    <a href="{{ route('siswa.poin') }}"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                              {{ request()->routeIs('siswa.poin') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Poin Saya
                    </a>
                </li>

                <li class="pt-5 pb-2">
                    <span class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Akun</span>
                </li>

                <li>
                    <a href="{{ route('siswa.profil') }}"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                              {{ request()->routeIs('siswa.profil', 'siswa.profil.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil
                    </a>
                </li>
            @endif
        </ul>
    </nav>

    <div class="border-t border-gray-200 px-4 py-4">
        <div class="flex items-center gap-3 px-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-gray-500">
                    @if(auth()->user()->isAdmin())
                        Admin
                    @elseif(auth()->user()->isHomeroom())
                        Wali Kelas
                    @elseif(auth()->user()->isCounselor())
                        BK
                    @elseif(auth()->user()->isStudentAffairs())
                        Kesiswaan
                    @elseif(auth()->user()->isStudent())
                        Siswa
                    @else
                        Guru
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors" title="Keluar">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
