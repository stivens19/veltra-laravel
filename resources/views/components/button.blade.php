@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconRight' => null,
    'type' => 'button',
])

@php
    $variantClass = match($variant) {
        'secondary' => 'vlt-btn-secondary',
        'success' => 'vlt-btn-success',
        'danger', 'rose' => 'vlt-btn-danger',
        'warning', 'amber' => 'vlt-btn-warning',
        'outline' => 'vlt-btn-outline',
        'ghost' => 'vlt-btn-ghost',
        default => 'vlt-btn-primary',
    };

    $sizeClass = match($size) {
        'sm' => 'text-xs py-1.5 px-3',
        'lg' => 'text-base py-3 px-6',
        default => 'py-2 px-4 text-xs font-bold',
    };
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "vlt-btn {$variantClass} {$sizeClass} inline-flex items-center justify-center gap-2 transition-all"]) }}>
    @if($icon)
        <i data-lucide="{{ $icon }}" class="w-4 h-4 shrink-0"></i>
    @endif

    <span>{{ $slot }}</span>

    @if($iconRight)
        <i data-lucide="{{ $iconRight }}" class="w-4 h-4 shrink-0"></i>
    @endif
</button>

