@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $range = 2;
        $pages = [];

        $pages[] = 1;

        $start = max(2, $current - $range);
        $end = min($last - 1, $current + $range);

        if ($start > 2) {
            $pages[] = '...';
        }

        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }

        if ($end < $last - 1) {
            $pages[] = '...';
        }

        if ($last > 1) {
            $pages[] = $last;
        }
    @endphp

    <div class="flex items-center justify-center gap-1">
        @if ($paginator->onFirstPage())
            <span class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-300 cursor-not-allowed">Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Sebelumnya</a>
        @endif

        @foreach ($pages as $page)
            @if ($page === '...')
                <span class="px-2 text-sm text-gray-400">...</span>
            @else
                <a href="{{ $paginator->url($page) }}"
                   class="rounded-lg border px-4 py-2 text-sm transition-colors
                          {{ $page === $current ? 'border-primary bg-primary text-white' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Berikutnya</a>
        @else
            <span class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-300 cursor-not-allowed">Berikutnya</span>
        @endif
    </div>
@endif
