@props(['class' => ''])

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table {{ $attributes->merge(['class' => 'min-w-full text-left text-sm '.$class]) }}>
        {{ $slot }}
    </table>
</div>
