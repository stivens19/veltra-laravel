<x-veltra::layout.guest title="Restablecer Contraseña">
    <div class="vlt-auth-card">
        <!-- Logo e Introducción -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mx-auto mb-3 shadow-xs">
                <i data-lucide="lock-keyhole" class="w-6 h-6"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Nueva Contraseña</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Escribe tu nueva contraseña segura para tu cuenta.</p>
        </div>

        <!-- Formulario -->
        <form method="POST" action="{{ Route::has('password.store') ? route('password.store') : '/reset-password' }}" class="space-y-4">
            @csrf

            <!-- Token de Reseteo -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Correo Electrónico -->
            <x-veltra::form.input
                name="email"
                type="email"
                label="Correo Electrónico"
                icon="mail"
                :value="old('email', $request->email)"
                :required="true"
                autofocus
            />

            <!-- Nueva Contraseña -->
            <x-veltra::form.input
                name="password"
                type="password"
                label="Nueva Contraseña"
                icon="lock"
                placeholder="Mínimo 8 caracteres"
                :required="true"
            />

            <!-- Confirmación de Contraseña -->
            <x-veltra::form.input
                name="password_confirmation"
                type="password"
                label="Confirmar Nueva Contraseña"
                icon="shield-check"
                placeholder="Repite tu contraseña"
                :required="true"
            />

            <!-- Botón Submit -->
            <div class="pt-2">
                <x-veltra::button type="submit" variant="primary" class="w-full justify-center">
                    Restablecer y Entrar
                </x-veltra::button>
            </div>
        </form>
    </div>
</x-veltra::layout.guest>

