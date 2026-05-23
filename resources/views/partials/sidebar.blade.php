<aside class="fixed inset-y-0 left-0 z-10 flex w-64 shrink-0 flex-col bg-white border-r border-gray-200">
    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200">
        <img src="{{ asset('storage/assets/logo/logo-website.png') }}" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">
        <span class="text-lg font-semibold text-gray-900">Sentri Siswa</span>
    </div>

    <nav class="flex-1 overflow-y-auto px-4 py-4">
        <ul class="space-y-1">
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
                <a href="{{ route('admin.teachers.index') }}"
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                          {{ request()->routeIs('admin.teachers.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Guru
                </a>
            </li>
            <li>
                <a href="{{ route('admin.students.index') }}"
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                          {{ request()->routeIs('admin.students.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Siswa
                </a>
            </li>
            <li>
                <a href="{{ route('admin.classes.index') }}"
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors
                          {{ request()->routeIs('admin.classes.*') ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Kelas
                </a>
            </li>
        </ul>
    </nav>
</aside>
