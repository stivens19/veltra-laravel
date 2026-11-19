@props([
    'user' => auth()->user(),
])

<header {{ $attributes->merge(['class' => 'vlt-header border-b border-slate-200/80 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md px-4 py-2.5 flex items-center justify-between']) }}>
    <div class="flex items-center gap-3">
        <!-- Botón Mobile Sidebar -->
        <button 
            type="button" 
            onclick="document.querySelector('.vlt-wrapper')?.classList.toggle('vlt-sidebar-mobile-open')"
            aria-label="Toggle mobile menu"
            class="md:hidden p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <!-- Botón Colapsar Sidebar Desktop -->
        <button 
            type="button" 
            onclick="document.body.classList.toggle('sidebar-collapse')"
            aria-label="Toggle sidebar collapse"
            class="hidden md:flex p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="M4 6h16M4 12h10M4 18h16"/></svg>
        </button>

        <!-- Quick Nav Pills -->
        @php
            $profileUrl = Route::has('profile.edit')
                ? route('profile.edit')
                : (Route::has('profile')
                    ? route('profile')
                    : (Route::has('profile.show') ? route('profile.show') : '/profile'));
            $isProfileActive = request()->is('profile*') || request()->is('settings*');
        @endphp
        <nav class="hidden sm:flex items-center gap-1.5 ml-2">
            <a 
                href="/dashboard" 
                class="px-3 py-1 text-xs font-bold rounded-lg {{ request()->is('dashboard') ? 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                Dashboard
            </a>
            <a 
                href="{{ $profileUrl }}" 
                class="px-3 py-1 text-xs font-medium rounded-lg {{ $isProfileActive ? 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                Perfil
            </a>
        </nav>
    </div>

    <!-- Right Controls -->
    <div class="flex items-center gap-3">
        <!-- Theme Toggle Atom -->
        <x-veltra::atoms.theme-toggle />

        <!-- User Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button 
                type="button"
                @click="open = !open"
                class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            >
                <x-veltra::atoms.avatar :name="$user?->name ?? 'Usuario'" size="sm" :online="true" />
                <span class="hidden md:block text-xs font-bold text-slate-800 dark:text-slate-200">
                    {{ $user?->name ?? 'Usuario' }}
                </span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
            </button>

            <div 
                x-show="open" 
                @click.away="open = false" 
                x-cloak
                class="absolute right-0 mt-2 w-48 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl py-1 z-50"
            >
                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800">
                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $user?->name ?? 'Usuario' }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ $user?->email ?? 'user@example.com' }}</p>
                </div>
                <a href="{{ $profileUrl }}" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    Perfil & 2FA
                </a>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
