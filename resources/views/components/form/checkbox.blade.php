@props([
    'name',
    'label' => null,
    'checked' => false,
])

<label class="inline-flex items-center gap-2 cursor-pointer select-none">
    <input
        type="checkbox"
        id="{{ $name }}"
        name="{{ $name }}"
        value="1"
        {{ old($name, $checked) ? 'checked' : '' }}
        {{ $attributes->merge(['class' => 'rounded-md border-slate-300 dark:border-slate-700 text-cyan-600 focus:ring-cyan-500/20 bg-slate-50 dark:bg-slate-800 transition-all']) }}
    />
    @if($label || $slot->isNotEmpty())
        <span class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-tight">
            {{ $label ?? $slot }}
        </span>
    @endif
</label>

