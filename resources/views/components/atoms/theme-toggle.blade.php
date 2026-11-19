@props([
    'storageKey' => config('veltra.theme.storage_key', 'veltra-theme'),
    'defaultTheme' => config('veltra.theme.default', 'dark'),
])

<button 
    type="button" 
    id="veltra-theme-toggle"
    aria-label="Toggle theme"
    {{ $attributes->merge(['class' => 'w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 bg-slate-100/80 dark:bg-slate-800/80 transition-colors border border-slate-200/50 dark:border-slate-700/50']) }}
>
    <!-- Sol para Modo Oscuro -->
    <svg class="w-4 h-4 text-amber-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="4" stroke-width="2" />
        <path stroke-width="2" stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41m14.14-14.14-1.41 1.41" />
    </svg>
    <!-- Luna para Modo Claro -->
    <svg class="w-4 h-4 text-cyan-600 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
    </svg>
</button>

<script>
    (function() {
        const btn = document.getElementById('veltra-theme-toggle');
        if (btn && !btn._hasVeltraListener) {
            btn._hasVeltraListener = true;
            btn.addEventListener('click', function() {
                const current = document.documentElement.getAttribute('data-theme') || '{{ $defaultTheme }}';
                const next = current === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', next);
                if (next === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                try {
                    localStorage.setItem('{{ $storageKey }}', next);
                } catch(e) {}
            });
        }
    })();
</script>
