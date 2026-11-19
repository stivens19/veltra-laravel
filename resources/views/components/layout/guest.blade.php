<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ config('veltra.theme.default', 'light') }}">
<head>
    @include('veltra::layouts.partials.head', ['title' => $title ?? null])
    {{ $head ?? '' }}
</head>
<body class="vlt-grid-bg text-slate-800 antialiased selection:bg-cyan-500 selection:text-white bg-slate-50 min-h-screen flex flex-col justify-between dark:bg-slate-950 dark:text-slate-100">

    <!-- Luz ambiental decorativa -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[800px] h-[300px] bg-gradient-to-b from-cyan-400/15 to-transparent blur-3xl pointer-events-none -z-10"></div>

    <!-- Switch de modo oscuro discreto -->
    @if(config('veltra.theme.allow_toggle', true))
        <div class="fixed top-5 right-5 z-20">
            <x-veltra::dark-mode-toggle />
        </div>
    @endif

    <!-- Contenedor Central Limpio y Centrado -->
    <main class="flex-1 flex flex-col items-center justify-center p-4 sm:p-6 my-auto">
        <!-- Logo e Identidad Centrado sobre la Tarjeta -->
        <a href="{{ config('veltra.brand.home_url', '/') }}" class="flex items-center gap-2.5 mb-6 group transition-transform hover:scale-105">
            <img src="{{ config('veltra.brand.logo', asset('vendor/veltra/img/logo.png')) }}" alt="{{ config('veltra.brand.name', 'VeltraUI') }}" class="w-10 h-10 rounded-2xl object-contain shadow-xs" />
            <span class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                {{ config('veltra.brand.short_name', 'Veltra') }}<span class="text-cyan-500">{{ config('veltra.brand.suffix', 'UI') }}</span>
            </span>
        </a>

        {{ $slot }}
    </main>

    <!-- Footer Mini -->
    <footer class="p-6 text-center text-xs text-slate-400 dark:text-slate-600">
        {{ config('veltra.layout.footer_text', '© ' . date('Y') . ' VeltraUI Suite. Todos los derechos reservados.') }}
    </footer>

    <!-- Scripts -->
    @include('veltra::layouts.partials.scripts')
    {{ $scripts ?? '' }}
</body>
</html>

