@props(['label' => null, 'name', 'type' => 'text'])

<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    <input id="{{ $name }}"
           name="{{ $name }}"
           type="{{ $type }}"
           {{ $attributes->class(['ui-input', 'mt-1' => $label]) }}>

    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
