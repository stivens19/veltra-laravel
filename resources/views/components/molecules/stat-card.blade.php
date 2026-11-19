@props([
    'title' => '',
    'badge' => null,
    'badgeVariant' => 'emerald',
    'badgeDot' => true,
    'linkText' => null,
    'linkHref' => '#',
    'linkColor' => 'text-cyan-600 dark:text-cyan-400',
])

<div {{ $attributes->merge(['class' => 'vlt-card p-5 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs']) }}>
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2.5">
            @if(isset($icon))
                <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                    {{ $icon }}
                </div>
            @endif
            <h3 class="text-xs font-bold text-slate-900 dark:text-white">{{ $title }}</h3>
        </div>
        @if($badge)
            <x-veltra::atoms.badge :variant="$badgeVariant" :dot="$badgeDot">
                {{ $badge }}
            </x-veltra::atoms.badge>
        @endif
    </div>

    <div class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-3">
        {{ $slot }}
    </div>

    @if($linkText)
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
            <a 
                href="{{ $linkHref }}" 
                class="text-xs font-bold hover:underline flex items-center justify-between {{ $linkColor }}"
            >
                <span>{{ $linkText }}</span>
                <span>&rarr;</span>
            </a>
        </div>
    @endif
</div>
