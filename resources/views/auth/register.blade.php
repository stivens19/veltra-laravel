<x-veltra::layout.guest :title="__('Create an account')">
    <div class="vlt-auth-card">
        <!-- Logo & Introduction -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mx-auto mb-3 shadow-xs">
                <i data-lucide="user-plus" class="w-6 h-6"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Create an account') }}</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('Enter your details below to create your account.') }}</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ Route::has('register') ? route('register') : '/register' }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    {{ __('Name') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5 pointer-events-none"></i>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        placeholder="{{ __('Full name') }}"
                        class="w-full pl-10 pr-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 @if(isset($errors) && $errors->has('name')) border-rose-500 @endif"
                    />
                </div>
                @if(isset($errors) && $errors->has('name'))
                    <p class="text-[11px] font-semibold text-rose-500 mt-1 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        {{ $errors->first('name') }}
                    </p>
                @endif
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    {{ __('Email address') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5 pointer-events-none"></i>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
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
                <label for="reg-password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    {{ __('Password') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5 pointer-events-none"></i>
                    <input
                        id="reg-password"
                        type="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full pl-10 pr-10 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 @if(isset($errors) && $errors->has('password')) border-rose-500 @endif"
                    />
                    <button type="button" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer" onclick="togglePasswordVisibility('reg-password', this)">
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

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    {{ __('Confirm password') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <i data-lucide="shield-check" class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5 pointer-events-none"></i>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        placeholder="••••••••"
                        class="w-full pl-10 pr-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400"
                    />
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-sky-600 hover:from-cyan-600 hover:to-sky-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md shadow-cyan-500/20 cursor-pointer transition-all mt-2">
                <span>{{ __('Create account') }}</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </button>
        </form>

        <!-- Footer Card -->
        @if (Route::has('login'))
            <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}" class="font-bold text-cyan-600 dark:text-cyan-400 hover:underline ml-1">
                    {{ __('Log in') }}
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
