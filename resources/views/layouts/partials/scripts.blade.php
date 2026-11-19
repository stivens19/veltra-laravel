<!-- VeltraUI Interactive Vanilla JS Runtime -->
@if(config('veltra.assets.mode', 'compiled') === 'compiled')
    <script src="{{ asset('vendor/veltra/js/veltra.umd.js') }}"></script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        if (typeof VeltraUI !== 'undefined') {
            VeltraUI.init();
        }
    });

    // Soporte para recargas en Livewire / Turbo si están presentes
    document.addEventListener('livewire:navigated', function() {
        if (typeof lucide !== 'undefined') lucide.createIcons();
        if (typeof VeltraUI !== 'undefined') VeltraUI.init();
    });
</script>

@if(class_exists(\Livewire\Livewire::class))
    @livewireScripts
@endif

