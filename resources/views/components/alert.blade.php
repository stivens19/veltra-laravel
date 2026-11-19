@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => true,
])

@php
    $typeClass = match($type) {
        'success' => 'vlt-alert-success',
        'warning', 'amber' => 'vlt-alert-warning',
        'danger', 'error', 'rose' => 'vlt-alert-danger',
        default => 'vlt-alert-info',
    };

    $iconName = match($type) {
        'success' => 'check-circle-2',
        'warning', 'amber' => 'alert-triangle',
        'danger', 'error', 'rose' => 'alert-octagon',
        default => 'info',
    };
@endphp

<div {{ $attributes->merge(['class' => "vlt-alert {$typeClass} flex items-start gap-3 p-4 rounded-2xl mb-4 relative transition-all"]) }} role="alert">
    <i data-lucide="{{ $iconName }}" class="w-5 h-5 shrink-0 mt-0.5"></i>
    
    <div class="flex-1 text-xs sm:text-sm">
        @if($title)
            <div class="font-bold mb-0.5">{{ $title }}</div>
        @endif
        <div class="leading-relaxed">{{ $slot }}</div>
    </div>

    @if($dismissible)
        <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-1" onclick="this.closest('.vlt-alert').remove()" aria-label="Cerrar">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    @endif
</div>

