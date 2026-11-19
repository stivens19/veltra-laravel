@props([
    'id',
    'title' => null,
    'size' => 'md', // sm, md, lg, xl
])

@php
    $sizeClass = match($size) {
        'sm' => 'vlt-modal-sm',
        'lg' => 'vlt-modal-lg',
        'xl' => 'vlt-modal-xl',
        default => '',
    };
@endphp

<div id="{{ $id }}" {{ $attributes->merge(['class' => "vlt-modal {$sizeClass}"]) }} data-vlt-modal tabindex="-1" role="dialog" aria-hidden="true">
    <div class="vlt-modal-dialog">
        @if($title || isset($header))
            <div class="vlt-modal-header">
                @if(isset($header))
                    {{ $header }}
                @else
                    <h3 class="vlt-modal-title">{{ $title }}</h3>
                @endif
                <button type="button" class="vlt-modal-close" data-vlt-dismiss="modal" aria-label="Cerrar">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        <div class="vlt-modal-body">
            {{ $slot }}
        </div>

        @if(isset($footer))
            <div class="vlt-modal-footer">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

