@props([
    'href' => '/dashboard',
    'showText' => true,
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'brand-link flex items-center gap-3']) }}>
    <img 
        src="{{ config('veltra.brand.logo', asset('vendor/veltra/img/logo.png')) }}" 
        alt="{{ config('veltra.brand.name', 'VeltraUI') }}" 
        class="brand-logo h-7 w-auto shrink-0 object-contain" 
    />
    @if($showText)
        <span class="brand-text font-black text-slate-900 dark:text-white tracking-tight">
            @php
                $bName = config('veltra.brand.name');
                $bShort = config('veltra.brand.short_name');
                $bSuffix = config('veltra.brand.suffix');
            @endphp
            @if($bName && $bName !== 'VeltraUI' && $bName !== 'Veltra')
                {{ $bName }}
            @elseif($bShort)
                {{ $bShort }}@if($bSuffix)<span class="text-cyan-500">{{ $bSuffix }}</span>@endif
            @else
                Veltra<span class="text-cyan-500">UI</span>
            @endif
        </span>
    @endif
</a>
