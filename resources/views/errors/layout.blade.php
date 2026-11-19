<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Error') — {{ config('veltra.brand.name', config('app.name', 'VeltraUI')) }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Anti-FOUC Dark Mode Script -->
    <script>
        (function() {
            try {
                const saved = localStorage.getItem('appearance') || localStorage.getItem('veltra-theme') || 'dark';
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = saved === 'dark' || (saved === 'system' && prefersDark);
                document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    <!-- Tailwind / VeltraUI Assets -->
    @if(file_exists(public_path('vendor/veltra/css/veltra.css')))
        <link rel="stylesheet" href="{{ asset('vendor/veltra/css/veltra.css') }}">
    @endif

    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif

    <style>
        :root {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .vlt-error-grid {
            background-image: radial-gradient(rgba(6, 182, 212, 0.08) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        .dark .vlt-error-grid {
            background-image: radial-gradient(rgba(6, 182, 212, 0.12) 1px, transparent 1px);
            background-size: 28px 28px;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between bg-slate-50 dark:bg-[#060913] text-slate-800 dark:text-slate-100 antialiased relative overflow-x-hidden transition-colors duration-200 vlt-error-grid">

    <!-- Luces Ambientales Cyan -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[850px] h-[380px] bg-cyan-500/10 dark:bg-cyan-500/15 blur-[130px] rounded-full pointer-events-none -z-10"></div>
    <div class="fixed -bottom-28 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-cyan-600/5 dark:bg-cyan-600/10 blur-[120px] rounded-full pointer-events-none -z-10"></div>

    <!-- Barra Superior -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between z-10">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 group transition-transform hover:scale-105">
            <img src="{{ asset('vendor/veltra/img/logo.png') }}" alt="{{ config('veltra.brand.name', 'VeltraUI') }}" class="w-9 h-9 rounded-xl object-contain shadow-sm" onerror="this.style.display='none'" />
            <span class="text-xl font-black text-slate-900 dark:text-white tracking-tight">
                {{ config('veltra.brand.short_name', 'Veltra') }}<span class="text-cyan-500">{{ config('veltra.brand.suffix', 'UI') }}</span>
            </span>
        </a>

        <!-- Switch de Modo Oscuro Rápido -->
        <button type="button" onclick="toggleVeltraErrorTheme()" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:text-cyan-500 dark:hover:text-cyan-400 hover:border-cyan-500/30 transition-all shadow-xs cursor-pointer" aria-label="Cambiar tema">
            <svg id="theme-icon-sun" class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2"></path>
                <path d="M12 20v2"></path>
                <path d="m4.93 4.93 1.41 1.41"></path>
                <path d="m17.66 17.66 1.41 1.41"></path>
                <path d="M2 12h2"></path>
                <path d="M20 12h2"></path>
                <path d="m6.34 17.66-1.41 1.41"></path>
                <path d="m19.07 4.93-1.41 1.41"></path>
            </svg>
            <svg id="theme-icon-moon" class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
            </svg>
        </button>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-1 flex items-center justify-center px-4 py-12 z-10">
        <div class="w-full max-w-xl mx-auto p-8 sm:p-12 rounded-3xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 shadow-2xl shadow-cyan-500/5 relative text-center">
            
            <!-- Glow Interior Sutil -->
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 bg-cyan-500/10 dark:bg-cyan-500/20 blur-3xl rounded-full pointer-events-none"></div>

            <!-- Badge Superior -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold ring-1 ring-inset mb-6 @yield('badge_class', 'bg-cyan-50 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-300 ring-cyan-200 dark:ring-cyan-800/60')">
                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                <span>@yield('badge', 'Error ' . View::yieldContent('code', '404'))</span>
            </div>

            <!-- Código Grande -->
            <div class="text-7xl sm:text-8xl md:text-9xl font-black font-mono tracking-tighter bg-gradient-to-br from-cyan-400 via-cyan-500 to-teal-400 dark:from-cyan-300 dark:via-cyan-400 dark:to-teal-400 bg-clip-text text-transparent select-none drop-shadow-sm leading-none mb-4">
                @yield('code', '404')
            </div>

            <!-- Título -->
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">
                @yield('message', 'Página no encontrada')
            </h1>

            <!-- Descripción -->
            <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 leading-relaxed max-w-md mx-auto mb-8">
                @yield('description', 'La página que buscas no existe, ha sido movida o ya no está disponible.')
            </p>

            <!-- Acciones -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 text-white font-semibold text-xs uppercase tracking-wider shadow-lg shadow-cyan-500/25 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>{{ __('Ir al Dashboard') }}</span>
                </a>

                <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}'" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-300 font-semibold text-xs uppercase tracking-wider border border-slate-200 dark:border-slate-700/60 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path d="m12 19-7-7 7-7"></path>
                        <path d="M19 12H5"></path>
                    </svg>
                    <span>{{ __('Regresar') }}</span>
                </button>
            </div>
        </div>
    </main>

    <!-- Footer Corporativo -->
    <footer class="w-full max-w-7xl mx-auto px-6 py-6 text-center text-xs text-slate-400 dark:text-slate-600 z-10 flex flex-col sm:flex-row items-center justify-between gap-2 border-t border-slate-200/40 dark:border-slate-800/40">
        <div>
            <span>VeltraUI Suite</span> &bull; <span>{{ config('app.name', 'Laravel') }}</span>
        </div>
        <div>
            {{ config('veltra.layout.footer_text', '© ' . date('Y') . ' VeltraUI Suite. Todos los derechos reservados.') }}
        </div>
    </footer>

    <script>
        function toggleVeltraErrorTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            const next = isDark ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            if (next === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            try {
                localStorage.setItem('appearance', next);
                localStorage.setItem('veltra-theme', next);
            } catch (e) {}
        }
    </script>
</body>
</html>
