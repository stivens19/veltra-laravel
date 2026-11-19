@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'icon' => null,
    'required' => false,
    'helper' => null,
])

<div class="vlt-form-group">
    @if($label)
        <label for="{{ $name }}" class="vlt-label {{ $required ? 'vlt-label-required' : '' }}">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
            </div>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'vlt-form-control' . 
                    ($icon ? ' pl-10' : '') . 
                    ((isset($errors) && $errors->has($name)) ? ' border-rose-500 focus:border-rose-500 ring-rose-500/20' : '')
            ]) }}
        />
    </div>

    @if(isset($errors) && $errors->has($name))
        <p class="text-[11px] font-semibold text-rose-500 mt-1 flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
            {{ $errors->first($name) }}
        </p>
    @else
        @if($helper)
            <p class="text-[11px] text-slate-400 mt-1">{{ $helper }}</p>
        @endif
    @endif
</div>

