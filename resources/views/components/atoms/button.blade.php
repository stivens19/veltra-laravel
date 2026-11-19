@props([
    'variant' => 'primary',
    'size' => 'md',
])

@php
$variants = [
    'primary' => 'bg-cyan-500 hover:bg-cyan-600 text-white font-bold shadow-md shadow-cyan-500/20 active:scale-95',
    'secondary' => 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 font-semibold',
    'ghost' => 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800',
    'danger' => 'bg-rose-500 hover:bg-rose-600 text-white font-bold shadow-md shadow-rose-500/20',
];
$sizeClass = $size === 'sm' ? 'px-3 py-1.5 text-xs rounded-xl' : 'px-4 py-2 text-sm rounded-xl';
$variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

<button {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 transition-all duration-200 {$sizeClass} {$variantClass}"]) }}>
    {{ $slot }}
</button>
