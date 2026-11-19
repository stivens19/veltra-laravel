@props([
    'user' => auth()->user(),
    'currentRoute' => request()->path(),
])

<aside {{ $attributes->merge(['class' => 'vlt-sidebar border-r border-slate-200/80 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col']) }}>
    <div class="vlt-sidebar-brand p-4 border-b border-slate-200/60 dark:border-slate-800/60">
        <x-veltra::atoms.brand-logo href="/dashboard" />
    </div>

    <!-- User Panel Molecule -->
    <div class="p-3">
        <x-veltra::molecules.user-panel 
            :name="$user?->name ?? 'Usuario'" 
            :email="$user?->email ?? 'user@example.com'" 
            role="Administrator" 
        />
    </div>

    <!-- Sidebar Search Molecule -->
    <div class="px-3 py-1">
        <x-veltra::molecules.search-input placeholder="Buscar en menú..." />
    </div>

    <!-- Navigation List -->
    <ul class="vlt-nav px-3 py-2 space-y-1 flex-1 overflow-y-auto">
        <li class="vlt-nav-header text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-3 py-2">
            MAIN NAVIGATION
        </li>
        <x-veltra::molecules.nav-item href="/dashboard" :active="request()->is('dashboard')">
            <x-slot:icon>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m12 14 4-4m-4 4-4-4m4 4V3M3.34 19a10 10 0 1 1 17.32 0"/></svg>
            </x-slot:icon>
            Dashboard
        </x-veltra::molecules.nav-item>

        <li class="vlt-nav-header text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-3 py-2 mt-4">
            SISTEMA & CUENTA
        </li>
        @php
            $profileUrl = Route::has('profile.edit')
                ? route('profile.edit')
                : (Route::has('profile')
                    ? route('profile')
                    : (Route::has('profile.show') ? route('profile.show') : '/profile'));
            $isProfileActive = request()->is('profile*') || request()->is('settings*');
        @endphp
        <x-veltra::molecules.nav-item :href="$profileUrl" :active="$isProfileActive">
            <x-slot:icon>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
            </x-slot:icon>
            Perfil & 2FA
        </x-veltra::molecules.nav-item>
    </ul>

    <!-- Status Footer -->
    <div class="p-3 border-t border-slate-200/80 dark:border-slate-800 mt-auto">
        <div class="flex items-center justify-between text-xs text-slate-500">
            <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-medium text-slate-600 dark:text-slate-400">Online</span>
            </span>
            <span class="font-mono text-[11px] text-slate-400">Veltra v1.0</span>
        </div>
    </div>
</aside>
