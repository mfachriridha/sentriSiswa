@props([
    'action',
    'search' => '',
    'placeholder' => 'Cari...',
    'filters' => [],
    'sort' => '',
    'direction' => '',
])

@php
$hasActiveFilters = filled($search) || collect($filters)->contains(fn ($f) => filled($f['value'] ?? ''));
@endphp

<form method="GET" action="{{ $action }}" class="mb-4 flex flex-wrap items-center gap-3"
      x-data x-ref="searchFilterForm">
    <div class="relative flex-1 min-w-[200px]">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text"
               name="search"
               value="{{ $search }}"
               placeholder="{{ $placeholder }}"
               x-ref="searchInput"
               @input.debounce.500ms="$refs.searchFilterForm.submit()"
               class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400
                      focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
    </div>

    @foreach ($filters as $filter)
        <select name="{{ $filter['name'] }}"
                @change="$refs.searchFilterForm.submit()"
                class="rounded-lg border border-gray-300 bg-white py-2.5 px-4 text-sm text-gray-700
                       focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
            @foreach ($filter['options'] as $value => $label)
                <option value="{{ $value }}" {{ ($filter['value'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    @endforeach

    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="direction" value="{{ $direction }}">

    @if($hasActiveFilters)
        <a href="{{ $action }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600
                  hover:bg-gray-50 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Reset
        </a>
    @endif
</form>