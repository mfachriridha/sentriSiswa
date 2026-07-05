@props(['label', 'column', 'sort' => 'dibuat_pada', 'direction' => 'desc'])

@php
    $isActive = $sort === $column;
    $newDirection = $isActive && $direction === 'asc' ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery(['sort' => $column, 'direction' => $newDirection, 'page' => null]);
@endphp

<a href="{{ $url }}" class="inline-flex items-center gap-1 font-semibold text-gray-600 hover:text-gray-900 transition-colors no-underline whitespace-nowrap">
    {{ $label }}
    <span class="flex flex-col -space-y-1 text-[10px] leading-none">
        <span class="{{ $isActive && $direction === 'asc' ? 'text-gray-900' : 'text-gray-300' }}">▲</span>
        <span class="{{ $isActive && $direction === 'desc' ? 'text-gray-900' : 'text-gray-300' }}">▼</span>
    </span>
</a>