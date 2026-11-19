<x-veltra::layout.guest :title="__('Log in')">
    <div class="vlt-auth-card">
        <!-- Logo & Introduction -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mx-auto mb-3 shadow-xs">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Log in') }}</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('Enter your credentials to access your account.') }}</p>
        </div>


        <!-- Session Status Alert -->
        @if (session('status'))
            <x-veltra::alert type="success" :dismissible="true">
                {{ session('status') }}
            </x-veltra::alert>
        @endif

        <!-- Passkey Login Option (Conditional) -->
        @if (Route::has('passkey.login-options') || Route::has('passkeys.login.options') || Route::has('passkey.login'))
            <div class="mb-5">
                <a href="{{ Route::has('passkey.login') ? route('passkey.login') : '/passkeys/login' }}" class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center justify-center gap-2 shadow-xs transition-colors">
                    <i data-lucide="key" class="w-4 h-4 text-cyan-600 dark:text-cyan-400"></i>
                    <span>{{ __('Sign in with a passkey') }}</span>
                </a>

                <div class="relative my-5">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
                    </div>
                    <div class="relative flex justify-center text-[10px] uppercase font-bold tracking-wider">
                        <span class="bg-white dark:bg-slate-900 px-3 text-slate-400 dark:text-slate-500">
                            {{ __('Or continue with email') }}
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ Route::has('login') ? route('login') : '/login' }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="login-email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    {{ __('Email address') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5 pointer-events-none"></i>
                    <input
                        id="login-email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="email@example.com"
                        class="w-full pl-10 pr-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 @if(isset($errors) && $errors->has('email')) border-rose-500 @endif"
                    />
                </div>
                @if(isset($errors) && $errors->has('email'))
                    <p class="text-[11px] font-semibold text-rose-500 mt-1 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        {{ $errors->first('email') }}
                    </p>
                @endif
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="login-password" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        {{ __('Password') }} <span class="text-rose-500">*</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5 pointer-events-none"></i>
                    <input
                        id="login-password"
                        type="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full pl-10 pr-10 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 @if(isset($errors) && $errors->has('password')) border-rose-500 @endif"
                    />
                    <button type="button" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer" onclick="togglePasswordVisibility('login-password', this)">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
                @if(isset($errors) && $errors->has('password'))
                    <p class="text-[11px] font-semibold text-rose-500 mt-1 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        {{ $errors->first('password') }}
                    </p>
                @endif
            </div>

            <!-- Remember me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 dark:text-slate-400 select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 accent-cyan-600 rounded cursor-pointer" />
                    <span>{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-sky-600 hover:from-cyan-600 hover:to-sky-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md shadow-cyan-500/20 cursor-pointer transition-all mt-2">
                <span>{{ __('Log in') }}</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </button>
        </form>

        <!-- Footer Card -->
        @if (Route::has('register'))
            <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" class="font-bold text-cyan-600 dark:text-cyan-400 hover:underline ml-1">
                    {{ __('Sign up') }}
                </a>
            </div>
        @endif
    </div>

    <x-slot:scripts>
        <script>
            function togglePasswordVisibility(inputId, btn) {
                const input = document.getElementById(inputId);
                if (input.type === 'password') {
                    input.type = 'text';
                    btn.innerHTML = '<i data-lucide="eye-off" class="w-4 h-4"></i>';
                } else {
                    input.type = 'password';
                    btn.innerHTML = '<i data-lucide="eye" class="w-4 h-4"></i>';
                }
                if (window.lucide) window.lucide.createIcons();
            }
        </script>
    </x-slot:scripts>
</x-veltra::layout.guest>
