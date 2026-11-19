<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ config('veltra.theme.default', 'light') }}">
<head>
    @include('veltra::layouts.partials.head', ['title' => $title ?? null])
    {{ $head ?? '' }}
</head>
<body class="vlt-body {{ config('veltra.layout.sidebar_collapsed') ? 'sidebar-collapse' : '' }}">
    <div class="vlt-wrapper">
        <!-- Mobile Backdrop -->
        <div class="vlt-backdrop" data-vlt-toggle="sidebar"></div>

        <!-- Sidebar -->
        @include('veltra::layouts.partials.sidebar')

        <!-- Main Wrapper -->
        <div class="vlt-main">
            <!-- Header -->
            @include('veltra::layouts.partials.header')

            <!-- Page Header / Content Header -->
            @if(isset($header))
                {{ $header }}
            @elseif(isset($title))
                <section class="vlt-content-header">
                    <div>
                        <h1 class="vlt-page-title">{{ $title }}</h1>
                        @if(isset($subtitle))
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $subtitle }}</p>
                        @endif
                    </div>
                    @if(isset($actions))
                        <div class="flex items-center gap-2">
                            {{ $actions }}
                        </div>
                    @endif
                </section>
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
