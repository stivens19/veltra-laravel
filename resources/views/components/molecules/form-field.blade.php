@props([
    'label' => null,
    'error' => null,
    'required' => false,
    'id' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    @if($label)
        <label @if($id) for="{{ $id }}" @endif class="block text-xs font-bold text-slate-700 dark:text-slate-300">
            {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
        </label>
    @endif
    {{ $slot }}
    @if($error)
        <p class="text-[11px] font-semibold text-rose-500 mt-1">{{ $error }}</p>
    @endif
</div>
