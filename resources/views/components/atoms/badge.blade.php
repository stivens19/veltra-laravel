@props([
    'variant' => 'cyan',
    'dot' => false,
])

@php
$variantClasses = [
    'cyan' => 'vlt-pill-cyan',
    'emerald' => 'vlt-pill-emerald',
    'amber' => 'vlt-pill-amber',
    'rose' => 'vlt-pill-rose',
    'slate' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
];
$classes = $variantClasses[$variant] ?? $variantClasses['cyan'];
@endphp

<span {{ $attributes->merge(['class' => "vlt-pill {$classes} text-[11px] font-bold py-0.5 px-2.5 inline-flex items-center gap-1.5"]) }}>
    @if($dot)
        <span class="pill-dot"></span>
    @endif
    {{ $slot }}
</span>
