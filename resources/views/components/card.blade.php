@props([
    'title' => null,
    'subtitle' => null,
    'color' => null,
])

<div {{ $attributes->merge(['class' => 'vlt-card' . ($color ? " vlt-card-{$color}" : '')]) }}>
    @if($title || isset($header) || isset($actions))
        <div class="vlt-card-header flex items-center justify-between">
            @if(isset($header))
                {{ $header }}
            @else
                <div>
                    @if($title)
                        <h3 class="vlt-card-title">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="vlt-card-subtitle">{{ $subtitle }}</p>
                    @endif
                </div>
            @endif

            @if(isset($actions))
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="vlt-card-body">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="vlt-card-footer">
            {{ $footer }}
        </div>
    @endif
</div>

