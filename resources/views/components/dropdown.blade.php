@props([
    'align' => 'right',
    'width' => 'w-56',
])

@php
    $alignClass = $align === 'left' ? 'left-0' : 'right-0';
@endphp

<div {{ $attributes->merge(['class' => 'vlt-dropdown relative inline-block text-left']) }}>
    <div>
        {{ $trigger }}
    </div>

    <div class="vlt-dropdown-menu {{ $width }} {{ $alignClass }}" data-vlt-dropdown-menu>
        {{ $content ?? $slot }}
    </div>
</div>

