@props([
    'name',
    'label' => null,
    'checked' => false,
    'color' => 'cyan', // cyan, emerald, amber, rose
    'size' => 'md',
])

<label class="vlt-switch {{ $color !== 'cyan' ? "vlt-switch-{$color}" : '' }} {{ $size === 'sm' ? 'vlt-switch-sm' : '' }} flex items-center gap-2.5">
    <input
        type="checkbox"
        id="{{ $name }}"
        name="{{ $name }}"
        value="1"
        {{ old($name, $checked) ? 'checked' : '' }}
        {{ $attributes }}
    />
    <div class="vlt-switch-track"></div>
    @if($label || $slot->isNotEmpty())
        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
            {{ $label ?? $slot }}
        </span>
    @endif
</label>

