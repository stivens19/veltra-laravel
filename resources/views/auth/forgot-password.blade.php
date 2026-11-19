<x-veltra::layout.guest :title="__('Forgot password')">
    <div class="vlt-auth-card">
        <!-- Logo & Introduction -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto mb-3 shadow-xs">
                <i data-lucide="key-round" class="w-6 h-6"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Forgot your password?') }}</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('Enter your email address and we will email you a password reset link.') }}</p>
        </div>

        <!-- Session Status Alert -->
        @if (session('status'))
            <x-veltra::alert type="success" :dismissible="true">
                {{ session('status') }}
            </x-veltra::alert>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ Route::has('password.email') ? route('password.email') : '/forgot-password' }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <x-veltra::form.input
                name="email"
                type="email"
                :label="__('Email address')"
                icon="mail"
                placeholder="email@example.com"
                :required="true"
                autofocus
            />

            <!-- Submit Button -->
            <div class="pt-2">
                <x-veltra::button type="submit" variant="primary" class="w-full justify-center">
                    {{ __('Email password reset link') }}
                </x-veltra::button>
            </div>
        </form>

        <!-- Back to login -->
        @if (Route::has('login'))
            <div class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
                <a href="{{ route('login') }}" class="font-bold text-cyan-600 dark:text-cyan-400 hover:underline flex items-center justify-center gap-1.5">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> {{ __('Back to login') }}
                </a>
            </div>
        @endif
    </div>
</x-veltra::layout.guest>
