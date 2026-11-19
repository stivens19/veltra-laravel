@props([
    'name' => 'Usuario',
    'email' => 'user@example.com',
    'role' => 'Administrator',
])

<div {{ $attributes->merge(['class' => 'vlt-sidebar-user flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60']) }}>
    <x-veltra::atoms.avatar :name="$name" :online="true" size="md" />
    <div class="user-info min-w-0 flex-1">
        <p class="user-name text-xs font-bold text-slate-900 dark:text-white truncate">
            {{ $name }}
        </p>
        <span class="user-role text-[10px] font-semibold text-cyan-600 dark:text-cyan-400 uppercase tracking-wider font-mono">
            {{ $role }}
        </span>
    </div>
</div>
