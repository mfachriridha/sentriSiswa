@props(['padding' => 'p-5'])

<div {{ $attributes->class(['ui-card', $padding]) }}>
    {{ $slot }}
</div>
