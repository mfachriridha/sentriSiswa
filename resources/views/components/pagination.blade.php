<?php $currentPerPage = request('per_page', 25); ?>
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 pt-3 border-t border-[#c3c6d1]/30">
    <div class="flex items-center gap-2 text-xs text-muted">
        <span>Tampilkan</span>
        <select onchange="window.location.href = '?' + this.value" class="input-field !w-auto !py-1 !px-2 !text-xs">
            @foreach([25, 50, 100] as $opt)
            <option value="{{ http_build_query(array_merge(request()->except(['per_page', 'page']), ['per_page' => $opt])) }}" {{ $currentPerPage == $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
        <span>per halaman &middot; Total {{ $data->total() }} data</span>
    </div>
    <div class="flex items-center gap-1">
        @if($data->onFirstPage())
            <span class="px-2 py-1 text-xs text-muted opacity-40 rounded"><i class="bi bi-chevron-left"></i></span>
        @else
            <a href="{{ $data->previousPageUrl() }}" class="px-2 py-1 text-xs font-semibold rounded hover:bg-[#edeeef] transition"><i class="bi bi-chevron-left"></i></a>
        @endif

        @php
            $start = max(1, $data->currentPage() - 2);
            $end = min($data->lastPage(), $data->currentPage() + 2);
        @endphp

        @if($start > 1)
            <a href="{{ $data->url(1) }}" class="px-2 py-1 text-xs font-semibold rounded hover:bg-[#edeeef] transition">1</a>
            @if($start > 2)<span class="px-1 text-xs text-muted">...</span>@endif
        @endif

        @for($p = $start; $p <= $end; $p++)
            <a href="{{ $data->url($p) }}" class="px-2 py-1 text-xs font-semibold rounded transition {{ $data->currentPage() == $p ? 'bg-[#001e40] text-white' : 'hover:bg-[#edeeef]' }}">{{ $p }}</a>
        @endfor

        @if($end < $data->lastPage())
            @if($end < $data->lastPage() - 1)<span class="px-1 text-xs text-muted">...</span>@endif
            <a href="{{ $data->url($data->lastPage()) }}" class="px-2 py-1 text-xs font-semibold rounded hover:bg-[#edeeef] transition">{{ $data->lastPage() }}</a>
        @endif

        @if($data->hasMorePages())
            <a href="{{ $data->nextPageUrl() }}" class="px-2 py-1 text-xs font-semibold rounded hover:bg-[#edeeef] transition"><i class="bi bi-chevron-right"></i></a>
        @else
            <span class="px-2 py-1 text-xs text-muted opacity-40 rounded"><i class="bi bi-chevron-right"></i></span>
        @endif
    </div>
</div>
