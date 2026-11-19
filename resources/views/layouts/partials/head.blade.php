@props([
    'title' => null,
])

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $title ? "$title — " : '' }}{{ config('veltra.brand.name', 'VeltraUI') }}</title>

<!-- Tipografías Oficiales -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    .font-mono {
        font-family: 'JetBrains Mono', monospace;
    }
</style>

<!-- Script Anti-Parpadeo (Zero-FOUC) para Modo Oscuro -->
<script>
    (function() {
        try {
            const key = '{{ config('veltra.theme.storage_key', 'veltra-theme') }}';
            const defaultTheme = '{{ config('veltra.theme.default', 'light') }}';
            const savedTheme = localStorage.getItem(key) || defaultTheme;
            
            if (savedTheme === 'system') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
                if (prefersDark) document.documentElement.classList.add('dark');
                else document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.setAttribute('data-theme', savedTheme);
                if (savedTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        } catch (e) {}
    })();
</script>

<!-- Carga de Estilos (VeltraUI Core + Vite/Tailwind) -->
<link rel="stylesheet" href="{{ asset('vendor/veltra/css/veltra.css') }}">
@if(config('veltra.assets.mode', 'compiled') === 'vite' || file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')) || file_exists(resource_path('css/app.css')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif

<!-- Lucide Icons CDN -->
@if(config('veltra.assets.cdn_lucide', true))
    <script src="https://unpkg.com/lucide{{ '@' . config('veltra.assets.lucide_version', 'latest') }}"></script>
@endif

@if(class_exists(\Livewire\Livewire::class))
    @livewireStyles
@endif

