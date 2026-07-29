<header class="sticky top-0 z-30 shrink-0 border-b border-gray-200 bg-white">
    <div class="flex h-14 items-center justify-between gap-4 px-4 sm:px-5 lg:px-6">
        <div class="flex min-w-0 items-center gap-3">
            {{-- Logo cuma tampil di ponsel: di layar lebar sudah ada di kepala sidebar. --}}
            <img src="{{ asset('storage/assets/logo/logo-sidebar.png') }}" alt="Sentri Siswa"
                 class="h-8 w-auto shrink-0 rounded-lg border border-gray-200 lg:hidden">
            <h1 class="truncate text-base font-semibold text-gray-800">@yield('title', 'Sentri Siswa')</h1>
        </div>
        <span class="max-w-40 truncate text-sm text-gray-500 sm:max-w-64">{{ auth()->user()->nama }}</span>
    </div>
</header>
