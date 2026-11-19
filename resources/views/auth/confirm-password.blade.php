<x-veltra::layout.guest title="Confirmar Contraseña">
    <div class="vlt-auth-card">
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto mb-3 shadow-xs">
                <i data-lucide="shield-alert" class="w-6 h-6"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Zona Segura</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                Esta es un área protegida de la aplicación. Por favor, confirma tu contraseña antes de continuar.
            </p>
        </div>

        <form method="POST" action="{{ Route::has('password.confirm') ? route('password.confirm') : '/confirm-password' }}" class="space-y-4">
            @csrf

            <x-veltra::form.input
                name="password"
                type="password"
                label="Contraseña Actual"
                icon="lock"
                placeholder="••••••••"
                :required="true"
                autofocus
            />

            <div class="pt-2">
                <x-veltra::button type="submit" variant="primary" class="w-full justify-center">
                    Confirmar Identidad
                </x-veltra::button>
            </div>
        </form>
    </div>
</x-veltra::layout.guest>

