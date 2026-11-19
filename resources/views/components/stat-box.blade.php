@props([
    'color' => 'cyan',
    'title',
    'value',
    'meta' => null,
    'icon' => null,
    'badge' => null,
    'url' => '#',
    'footerText' => 'Más detalles',
])

<div {{ $attributes->merge(['class' => "vlt-stat-box vlt-stat-box-{$color}"]) }}>
    <div class="vlt-stat-box-inner">
        <div class="stat-top">
            <div class="stat-icon-wrapper">
                @if($icon)
                    <i data-lucide="{{ $icon }}"></i>
                @endif
            </div>
            @if($badge)
                <span class="vlt-pill vlt-pill-{{ $color }} py-0.5 px-2 text-[10px]">
                    <span class="pill-dot"></span>
                    <span>{{ $badge }}</span>
                </span>
            @endif
        </div>

        <div class="stat-value">{!! $value !!}</div>
        <div class="stat-title">{{ $title }}</div>

        @if($meta)
            <div class="stat-meta text-{{ $color }}-600 dark:text-{{ $color }}-400 flex items-center gap-1.5 text-xs font-semibold">
                <span>{{ $meta }}</span>
            </div>
        @endif
    </div>

    @if($url)
        <a href="{{ $url }}" class="vlt-stat-box-footer">
            <span>{{ $footerText }}</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5 footer-arrow"></i>
        </a>
    @endif
</div>

