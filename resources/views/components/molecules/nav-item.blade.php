@props([
    'href' => '#',
    'active' => false,
    'badge' => null,
])

<li {{ $attributes->merge(['class' => 'vlt-nav-item']) }}>
    <a 
        href="{{ $href }}" 
        class="vlt-nav-link flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-colors {{ $active ? 'active bg-cyan-500 !text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100' }}"
    >
        <div class="flex items-center gap-2.5 min-w-0">
            @if(isset($icon))
                <span class="vlt-nav-icon shrink-0 {{ $active ? '!text-white' : '' }}">{{ $icon }}</span>
            @endif
            <span class="vlt-nav-text truncate {{ $active ? '!text-white font-bold' : '' }}">{{ $slot }}</span>
        </div>
        @if($badge)
            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $active ? 'bg-white/20 !text-white' : 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400' }}">
                {{ $badge }}
            </span>
        @endif
    </a>
</li>
