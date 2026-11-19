<x-veltra::layout.guest :title="__('Two-Factor Authentication')">
    <div class="vlt-auth-card">
        <!-- Logo & Introduction -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mx-auto mb-3 shadow-xs">
                <i data-lucide="shield-alert" class="w-6 h-6"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                {{ __('Two-Factor Authentication') }}
            </h1>
            <p id="desc-totp" class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
            </p>
            <p id="desc-recovery" class="text-xs text-slate-500 dark:text-slate-400 mt-1 hidden">
                {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
            </p>
        </div>

        <!-- Session Status Alert -->
        @if (session('status'))
            <x-veltra::alert type="success" :dismissible="true">
                {{ session('status') }}
            </x-veltra::alert>
        @endif

        <!-- Two-Factor Challenge Form -->
        <form method="POST" action="{{ Route::has('two-factor.login') ? route('two-factor.login') : '/two-factor-challenge' }}" class="space-y-4">
            @csrf

            <!-- Mode 1: TOTP 6-Digit Code -->
            <div id="mode-totp">
                <label for="code" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-center">
                    {{ __('Authentication Code') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input
                        id="code"
                        type="text"
                        name="code"
                        inputmode="numeric"
                        maxlength="6"
                        autofocus
                        autocomplete="one-time-code"
                        placeholder="000000"
                        class="w-full py-2.5 text-center text-xl tracking-widest font-mono font-bold rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 @if(isset($errors) && $errors->has('code')) border-rose-500 @endif"
                    />
                </div>
                @if(isset($errors) && $errors->has('code'))
                    <p class="text-[11px] font-semibold text-rose-500 mt-1 flex items-center justify-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        {{ $errors->first('code') }}
                    </p>
                @endif
            </div>

            <!-- Mode 2: Emergency Recovery Code -->
            <div id="mode-recovery" class="hidden">
                <label for="recovery_code" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-center">
                    {{ __('Recovery Code') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input
                        id="recovery_code"
                        type="text"
                        name="recovery_code"
                        autocomplete="one-time-code"
                        placeholder="xxxx-xxxx"
                        class="w-full py-2.5 text-center text-sm font-mono tracking-wider rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 @if(isset($errors) && $errors->has('recovery_code')) border-rose-500 @endif"
                    />
                </div>
                @if(isset($errors) && $errors->has('recovery_code'))
                    <p class="text-[11px] font-semibold text-rose-500 mt-1 flex items-center justify-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        {{ $errors->first('recovery_code') }}
                    </p>
                @endif
            </div>

            <!-- Toggle Recovery / Code Mode -->
            <div class="flex items-center justify-center pt-1">
                <button
                    id="btn-use-recovery"
                    type="button"
                    class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 hover:underline cursor-pointer"
                    onclick="toggle2FAMode(true)">
                    {{ __('Use a recovery code') }}
                </button>

                <button
                    id="btn-use-totp"
                    type="button"
                    class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 hover:underline cursor-pointer hidden"
                    onclick="toggle2FAMode(false)">
                    {{ __('Use an authentication code') }}
                </button>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-sky-600 hover:from-cyan-600 hover:to-sky-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md shadow-cyan-500/20 cursor-pointer transition-all mt-2">
                <span>{{ __('Log in') }}</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </button>
        </form>

        <!-- Footer Card -->
        <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
            <a href="{{ route('login') }}" class="font-bold text-slate-600 dark:text-slate-300 hover:text-cyan-600 dark:hover:text-cyan-400 flex items-center justify-center gap-1.5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>{{ __('Cancel and return to login') }}</span>
            </a>
        </div>
    </div>

    <script>
        function toggle2FAMode(toRecovery) {
            const totpDiv = document.getElementById('mode-totp');
            const recDiv = document.getElementById('mode-recovery');
            const btnRec = document.getElementById('btn-use-recovery');
            const btnTotp = document.getElementById('btn-use-totp');
            const descTotp = document.getElementById('desc-totp');
            const descRec = document.getElementById('desc-recovery');

            if (toRecovery) {
                totpDiv.classList.add('hidden');
                recDiv.classList.remove('hidden');
                btnRec.classList.add('hidden');
                btnTotp.classList.remove('hidden');
                descTotp.classList.add('hidden');
                descRec.classList.remove('hidden');
                const recInput = document.getElementById('recovery_code');
                if (recInput) recInput.focus();
            } else {
                recDiv.classList.add('hidden');
                totpDiv.classList.remove('hidden');
                btnTotp.classList.add('hidden');
                btnRec.classList.remove('hidden');
                descRec.classList.add('hidden');
                descTotp.classList.remove('hidden');
                const codeInput = document.getElementById('code');
                if (codeInput) codeInput.focus();
            }
        }
    </script>
</x-veltra::layout.guest>
