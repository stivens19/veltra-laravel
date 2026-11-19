<header class="vlt-header">
    <div class="flex items-center gap-3">
        <!-- Toggle Sidebar (Colapsar Menú en desktop y drawer en móvil) -->
        <button type="button" class="vlt-btn-icon" data-vlt-toggle="sidebar" title="Colapsar Menú">
            <i data-lucide="menu" class="w-4 h-4"></i>
        </button>

        <!-- Pestañas rápidas superiores estilo VeltraUI (Page Start) -->
        <div class="hidden sm:flex items-center gap-1 bg-slate-100 dark:bg-slate-800/80 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
            <a href="{{ route('dashboard') }}" class="px-3 py-1 text-xs rounded-lg font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/80 dark:hover:bg-slate-700 flex items-center gap-1.5 transition-colors cursor-pointer {{ request()->routeIs('dashboard') ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : '' }}">
                <i data-lucide="gauge" class="w-3.5 h-3.5 {{ request()->routeIs('dashboard') ? 'text-cyan-500' : 'text-slate-400' }}"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ Route::has('profile.edit') ? route('profile.edit') : (Route::has('profile') ? route('profile') : '/profile') }}" class="px-3 py-1 text-xs rounded-lg font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/80 dark:hover:bg-slate-700 flex items-center gap-1.5 transition-colors cursor-pointer {{ request()->is('profile*') ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : '' }}">
                <i data-lucide="user" class="w-3.5 h-3.5 {{ request()->is('profile*') ? 'text-cyan-500' : 'text-slate-400' }}"></i>
                <span>Perfil</span>
            </a>
        </div>

        <!-- Botón de Búsqueda -->
        <button type="button" class="vlt-btn-icon" data-vlt-toggle="search-modal" title="Buscar (Ctrl+K)">
            <i data-lucide="search" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- Menú derecho de acciones -->
    <div class="flex items-center gap-1.5 ml-auto">
        <!-- Notificaciones Dropdown -->
        <div class="vlt-dropdown">
            <button type="button" class="vlt-btn-icon relative" data-vlt-toggle="dropdown" title="Notificaciones">
                <i data-lucide="bell" class="w-4 h-4"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-amber-500 rounded-full"></span>
            </button>
            <div class="vlt-dropdown-menu vlt-dropdown-menu-md">
                <div class="vlt-dropdown-header">
                    <span class="font-bold text-slate-800 dark:text-slate-100">Notificaciones</span>
                </div>
                <div class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                    No tienes notificaciones pendientes.
                </div>
            </div>
        </div>

        <!-- Pantalla Completa -->
        <button type="button" class="vlt-btn-icon hidden sm:inline-flex" data-vlt-toggle="fullscreen" title="Pantalla Completa">
            <i data-lucide="maximize" class="w-4 h-4"></i>
        </button>

        <!-- Selector Modo Oscuro -->
        @if(config('veltra.theme.allow_toggle', true))
            <x-veltra::dark-mode-toggle />
        @endif

        <!-- Menú de Usuario -->
        <div class="vlt-dropdown">
            <button type="button" class="flex items-center gap-2 p-1 pl-1.5 pr-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors ml-1 cursor-pointer" data-vlt-toggle="dropdown">
                @auth
                    <div class="w-7 h-7 rounded-full bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 font-bold flex items-center justify-center text-xs ring-2 ring-cyan-500/40">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="hidden md:block text-left text-xs leading-tight">
                        <span class="font-bold text-slate-800 dark:text-slate-100 block truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400">Administrator</span>
                    </div>
                @else
                    <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold flex items-center justify-center text-xs ring-2 ring-slate-400/30">
                        IN
                    </div>
                    <div class="hidden md:block text-left text-xs leading-tight">
                        <span class="font-bold text-slate-800 dark:text-slate-100 block">Invitado</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400">Demo</span>
                    </div>
                @endauth
            </button>
            <div class="vlt-dropdown-menu w-56">
                @auth
                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Conectado como</p>
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ Route::has('profile.edit') ? route('profile.edit') : (Route::has('profile') ? route('profile') : '/profile') }}" class="vlt-dropdown-item">
                        <i data-lucide="user" class="w-4 h-4 mr-2 text-slate-400"></i>Perfil &amp; Seguridad
                    </a>
                    <div class="vlt-dropdown-divider"></div>
                    <form method="POST" action="{{ Route::has('logout') ? route('logout') : '/logout' }}">
                        @csrf
                        <button type="submit" class="vlt-dropdown-item w-full text-left text-rose-500 hover:text-rose-600 cursor-pointer">
                            <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>Cerrar Sesión
                        </button>
                    </form>
                @else
                    <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="vlt-dropdown-item"><i data-lucide="log-in" class="w-4 h-4 mr-2 text-slate-400"></i>Iniciar Sesión</a>
                    <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="vlt-dropdown-item"><i data-lucide="user-plus" class="w-4 h-4 mr-2 text-slate-400"></i>Crear Cuenta</a>
                @endauth
            </div>
        </div>
    </div>
</header>
