<x-veltra::layout.guest title="Verificar Correo Electrónico">
    <div class="vlt-auth-card">
        <!-- Logo e Introducción -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mx-auto mb-3 shadow-xs">
                <i data-lucide="mail-check" class="w-6 h-6"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Verifica tu Correo</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                ¡Gracias por registrarte! Antes de comenzar, por favor verifica tu dirección de correo electrónico haciendo clic en el enlace que acabamos de enviarte.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <x-veltra::alert type="success" :dismissible="true">
                Se ha enviado un nuevo enlace de verificación a la dirección de correo que proporcionaste durante el registro.
            </x-veltra::alert>
        @endif

        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3">
            <form method="POST" action="{{ Route::has('verification.send') ? route('verification.send') : '/email/verification-notification' }}">
                @csrf
                <x-veltra::button type="submit" variant="primary">
                    Reenviar Enlace
                </x-veltra::button>
            </form>

            <form method="POST" action="{{ Route::has('logout') ? route('logout') : '/logout' }}">
                @csrf
                <button type="submit" class="text-xs font-semibold text-rose-500 hover:text-rose-600 underline">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</x-veltra::layout.guest>

