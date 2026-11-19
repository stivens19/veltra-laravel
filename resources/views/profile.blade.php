<x-veltra::layout.app title="Perfil &amp; Seguridad">
    <div class="space-y-6 max-w-5xl">
        <!-- Banner Resumen de Usuario -->
        <div class="vlt-card p-6 border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl shadow-xs">
            <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-5">
                <div class="flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
                    <div class="relative shrink-0">
                        <div class="w-16 h-16 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-black flex items-center justify-center text-xl ring-2 ring-cyan-500/30">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full" title="Usuario activo"></span>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <h2 class="text-lg font-black text-slate-900 dark:text-white">{{ $user->name }}</h2>
                            <span class="vlt-pill vlt-pill-emerald text-[11px] font-bold py-0.5 px-2">Activo</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $user->email }}</p>
                        <span class="inline-block mt-2 text-[10px] font-bold text-cyan-600 dark:text-cyan-400 uppercase tracking-wider font-mono">
                            Cuenta Verificada en VeltraUI
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="#security" class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors flex items-center gap-1.5">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-cyan-500"></i>
                        <span>Ir a Seguridad</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 1. Información del Perfil -->
        <div id="profile" class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-5">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-cyan-500"></i>
                        Información Personal
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Actualiza el nombre completo y la dirección de correo de tu cuenta.</p>
                </div>
                @if (session('status') === 'profile-updated')
                    <span class="vlt-pill vlt-pill-emerald text-xs font-semibold py-1 px-3 flex items-center gap-1">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Perfil Guardado
                    </span>
                @endif
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4 max-w-xl">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Nombre Completo <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5 pointer-events-none"></i>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                            class="w-full pl-10 pr-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400" />
                    </div>
                    @if(isset($errors) && $errors->has('name'))
                        <p class="text-[11px] font-semibold text-rose-500 mt-1 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Correo Electrónico <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5 pointer-events-none"></i>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                            class="w-full pl-10 pr-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400" />
                    </div>
                    @if(isset($errors) && $errors->has('email'))
                        <p class="text-[11px] font-semibold text-rose-500 mt-1 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>

                <div class="pt-2">
                    <button type="submit" class="py-2 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-sky-600 hover:from-cyan-600 hover:to-sky-700 text-white font-bold text-xs flex items-center gap-2 shadow-xs cursor-pointer transition-all">
                        <i data-lucide="save" class="w-3.5 h-3.5"></i>
                        <span>Guardar Cambios</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. Cambio de Contraseña -->
        <div id="password-section" class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-5">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="key" class="w-4 h-4 text-amber-500"></i>
                        Actualizar Contraseña
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Asegúrate de utilizar una contraseña segura y difícil de predecir.</p>
                </div>
                @if (session('status') === 'password-updated')
                    <span class="vlt-pill vlt-pill-emerald text-xs font-semibold py-1 px-3 flex items-center gap-1">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Contraseña Actualizada
                    </span>
                @endif
            </div>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4 max-w-xl">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Contraseña Actual <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5 pointer-events-none"></i>
                        <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                            class="w-full pl-10 pr-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100" />
                    </div>
                    @if(isset($errors) && $errors->has('current_password'))
                        <p class="text-[11px] font-semibold text-rose-500 mt-1 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            {{ $errors->first('current_password') }}
                        </p>
                    @endif
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Nueva Contraseña <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="shield-check" class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5 pointer-events-none"></i>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                            class="w-full pl-10 pr-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100" />
                    </div>
                    @if(isset($errors) && $errors->has('password'))
                        <p class="text-[11px] font-semibold text-rose-500 mt-1 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            {{ $errors->first('password') }}
                        </p>
                    @endif
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Confirmar Nueva Contraseña <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="check" class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5 pointer-events-none"></i>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                            class="w-full pl-10 pr-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100" />
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold text-xs flex items-center gap-2 shadow-xs cursor-pointer transition-all">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                        <span>Actualizar Contraseña</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. Autenticación en Dos Pasos (2FA) -->
        <div id="security" class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-5">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="shield-alert" class="w-4 h-4 text-emerald-500"></i>
                        Autenticación en Dos Pasos (2FA)
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Añade una capa adicional de protección a tu cuenta mediante autenticador móvil.</p>
                </div>
                <span class="vlt-pill {{ $is2faEnabled ? 'vlt-pill-emerald' : 'vlt-pill-amber' }} text-xs font-semibold py-1 px-3">
                    <span class="pill-dot"></span>
                    {{ $is2faEnabled ? '2FA Activado' : '2FA Desactivado' }}
                </span>
            </div>

            <div class="space-y-5">
                <div class="p-4 rounded-xl {{ $is2faEnabled ? 'bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/40' : 'bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-700' }}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                {{ $is2faEnabled ? 'Tu cuenta está protegida con verificación en dos pasos' : 'Aún no has activado la verificación en dos pasos' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                Cada vez que inicies sesión se te solicitará un código generado por tu aplicación de autenticación (Google Authenticator, Authy o 1Password).
                            </p>
                        </div>

                        <form method="POST" action="{{ route('profile.2fa') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl {{ $is2faEnabled ? 'bg-rose-500 hover:bg-rose-600 text-white' : 'bg-cyan-500 hover:bg-cyan-600 text-white shadow-xs shadow-cyan-500/20' }} transition-colors cursor-pointer shrink-0">
                                {{ $is2faEnabled ? 'Desactivar 2FA' : 'Activar 2FA Ahora' }}
                            </button>
                        </form>
                    </div>
                </div>

                @if($is2faEnabled)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <!-- Clave Secreta -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Clave Secreta de Configuración</span>
                            <div class="flex items-center justify-between bg-white dark:bg-slate-900 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800">
                                <code class="font-mono text-xs font-bold text-cyan-600 dark:text-cyan-400">VLT-SEC-7492-9182-ABCD</code>
                                <button type="button" class="text-slate-400 hover:text-cyan-500 text-xs cursor-pointer" onclick="navigator.clipboard.writeText('VLT-SEC-7492-9182-ABCD'); alert('Clave secreta copiada.');" title="Copiar Clave">
                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Códigos de Emergencia -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Códigos de Recuperación</span>
                            <p class="text-[11px] text-slate-400 font-mono">
                                4829-1092 &bull; 9182-4412 &bull; 7381-0029 &bull; 5612-8831
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-veltra::layout.app>

