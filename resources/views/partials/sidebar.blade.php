@php
    $menu = \App\Services\MenuNavigasi::untuk(auth()->user());
    $menuPerGrup = collect($menu)->groupBy('grup');
@endphp

{{-- Sidebar khusus layar lebar. Di ponsel navigasinya pindah ke bilah bawah. --}}
<aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">
    <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">
        <img src="{{ asset('storage/assets/logo/logo-sidebar.png') }}" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">
        <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-3">
        <ul class="space-y-1">
            @foreach ($menuPerGrup as $namaGrup => $itemGrup)
                @if (! $loop->first)
                    <li class="pt-5 pb-2">
                        <span class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $namaGrup }}</span>
                    </li>
                @endif

                @foreach ($itemGrup as $item)
                    @php $aktif = request()->routeIs(...$item['aktif']); @endphp
                    <li>
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
                                  {{ $aktif ? 'bg-gradient-to-r from-primary to-primary-dark text-white shadow-md shadow-primary/10 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <x-nav-icon :name="$item['icon']" />
                            <span class="flex-1">{{ $item['label'] }}</span>
                            @if (($item['badge'] ?? 0) > 0)
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-xs font-bold leading-none text-white">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    </li>
                @endforeach
            @endforeach
        </ul>
    </nav>

    <div class="border-t border-slate-100 bg-white/50 px-4 py-4">
        <div class="flex items-center gap-3 px-2">
            @if($fotoPengguna = \App\Services\FotoPengguna::url(auth()->user()))
                <img src="{{ $fotoPengguna }}" alt="{{ auth()->user()->nama }}" class="h-9 w-9 rounded-full object-cover">
            @else
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">
                    {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-gray-900">{{ auth()->user()->nama }}</p>
                <p class="truncate text-xs text-gray-500">{{ auth()->user()->roleLabel() }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 transition-colors hover:bg-red-100 hover:text-red-800">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>
