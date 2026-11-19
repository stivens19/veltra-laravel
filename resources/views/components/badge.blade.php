@props([
    'color' => 'cyan',
    'dot' => false,
])

<span {{ $attributes->merge(['class' => "vlt-badge vlt-badge-{$color} inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-tight"]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
    @endif
    <span>{{ $slot }}</span>
</span>

