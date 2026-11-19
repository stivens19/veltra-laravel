<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ config('veltra.theme.default', 'light') }}">
<head>
    @include('veltra::layouts.partials.head', ['title' => $title ?? null])
    {{ $head ?? '' }}
</head>
<body class="vlt-body {{ config('veltra.layout.sidebar_collapsed') ? 'sidebar-collapse' : '' }}">
    <div class="vlt-wrapper">
        <!-- Sidebar -->
        @include('veltra::layouts.partials.sidebar')

        <!-- Main Wrapper -->
        <div class="vlt-main">
            <!-- Header -->
            @include('veltra::layouts.partials.header')

            <!-- Page Header (Opcional) -->
            @if(isset($header) || isset($title))
                <div class="px-6 pt-6 pb-2">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            @if(isset($title))
                                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                    {{ $title }}
                                </h1>
                            @endif
                            @if(isset($subtitle))
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                                    {{ $subtitle }}
                                </p>
                            @endif
                        </div>
                        @if(isset($actions))
                            <div class="flex items-center gap-2">
                                {{ $actions }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Main Content Area -->
            <main class="vlt-content">
                {{ $slot }}
            </main>

            <!-- Footer -->
            @if(config('veltra.layout.show_footer', true))
                @include('veltra::layouts.partials.footer')
            @endif
        </div>
    </div>

    <!-- Scripts -->
    @include('veltra::layouts.partials.scripts')
    {{ $scripts ?? '' }}
</body>
</html>

