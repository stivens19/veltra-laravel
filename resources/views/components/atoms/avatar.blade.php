@props([
    'name' => 'US',
    'size' => 'md',
    'online' => true,
])

@php
$sizeClasses = [
    'sm' => 'w-7 h-7 text-xs rounded-lg',
    'md' => 'w-9 h-9 text-xs rounded-xl',
    'lg' => 'w-12 h-12 text-sm rounded-2xl',
];
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];

$initials = 'US';
if ($name) {
    $parts = explode(' ', trim($name));
    $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1));
}
@endphp

<div {{ $attributes->merge(['class' => 'relative shrink-0']) }}>
    <div class="{{ $sizeClass }} bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-black flex items-center justify-center ring-2 ring-cyan-500/30">
        {{ $initials }}
    </div>
    @if($online)
        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full"></span>
    @endif
</div>
