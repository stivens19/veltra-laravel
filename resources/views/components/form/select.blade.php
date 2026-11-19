@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'helper' => null,
])

<div class="vlt-form-group">
    @if($label)
        <label for="{{ $name }}" class="vlt-label {{ $required ? 'vlt-label-required' : '' }}">
            {{ $label }}
        </label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge([
            'class' => 'vlt-form-control pr-8 cursor-pointer' . 
                ((isset($errors) && $errors->has($name)) ? ' border-rose-500 focus:border-rose-500 ring-rose-500/20' : '')
        ]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @if(!empty($options))
            @foreach($options as $key => $text)
                <option value="{{ $key }}" {{ (string) old($name, $selected) === (string) $key ? 'selected' : '' }}>
                    {{ $text }}
                </option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>

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

