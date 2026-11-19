<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Brand Configuration
    |--------------------------------------------------------------------------
    |
    | Information displayed in the header, sidebar, login cards, and meta tags.
    |
    */
    'brand' => [
        'name' => env('VELTRA_BRAND_NAME', 'VeltraUI'),
        'short_name' => 'Veltra',
        'suffix' => 'UI',
        'logo' => env('VELTRA_LOGO_URL', '/vendor/veltra/img/logo.png'),
        'home_url' => env('VELTRA_HOME_URL', '/dashboard'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme & Appearance
    |--------------------------------------------------------------------------
    |
    | Configure the initial color scheme and dark mode switch options.
    | Supported defaults: 'light', 'dark', 'system'
    |
    */
    'theme' => [
        'default' => env('VELTRA_THEME_DEFAULT', 'light'),
        'allow_toggle' => true,
        'storage_key' => 'veltra-theme',
    ],

    /*
    |--------------------------------------------------------------------------
    | Assets Delivery Mode
    |--------------------------------------------------------------------------
    |
    | 'compiled': Loads pre-bundled assets from `public/vendor/veltra/`
    |             Zero configuration, no Node.js required.
    | 'vite':     Integrates with host application Vite build pipeline
    |             using @vite(['resources/css/app.css', 'resources/js/app.js'])
    |
    */
    'assets' => [
        'mode' => env('VELTRA_ASSETS_MODE', 'compiled'),
        'cdn_lucide' => true,
        'lucide_version' => 'latest',
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout Preferences
    |--------------------------------------------------------------------------
    |
    | Global UI behavior settings for the application shell.
    |
    */
    'layout' => [
        'boxed' => env('VELTRA_LAYOUT_BOXED', false),
        'sidebar_collapsed' => false,
        'fixed_sidebar' => true,
        'fixed_header' => true,
        'show_breadcrumbs' => true,
        'show_footer' => true,
        'footer_text' => '© ' . date('Y') . ' ' . env('VELTRA_BRAND_NAME', 'VeltraUI') . '. Todos los derechos reservados.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Views & Settings
    |--------------------------------------------------------------------------
    |
    | Settings used when rendering or scaffolding auth views (Breeze, Fortify).
    |
    */
    'auth' => [
        'layout' => 'veltra::layouts.guest',
        'login_route' => 'login',
        'register_route' => 'register',
        'password_request_route' => 'password.request',
        'home_route' => 'dashboard',
        'show_terms' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Package Routes (Demo & Health)
    |--------------------------------------------------------------------------
    |
    | Enable built-in demo routes to inspect all component showcase pages.
    | Disabled by default to keep host application routing clean and focused.
    |
    */
    'routes' => [
        'enabled' => env('VELTRA_ROUTES_ENABLED', false),
        'prefix' => 'veltra',
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sidebar Navigation
    |--------------------------------------------------------------------------
    |
    | Production navigation items rendered by <x-veltra::layout.sidebar>.
    | Customize this array to link to your application's real business modules.
    |
    | Supported keys per item:
    | - 'title': Display text
    | - 'icon': Lucide icon name (e.g. 'gauge', 'users', 'shopping-cart')
    | - 'url': Target URL (or 'route' => 'route.name')
    | - 'permission' or 'can': Gate/Permission string or array of permissions
    | - 'role' or 'roles': Required role name or array of roles (Spatie or $user->role)
    | - 'badge': ['text' => 'New', 'color' => 'cyan|emerald|amber|rose']
    | - 'children': Submenu array of navigation items
    |
    */
    'navigation' => [
        [
            'header' => 'MAIN NAVIGATION',
        ],
        [
            'title' => 'Dashboard',
            'icon' => 'gauge',
            'url' => '/dashboard',
        ],
        [
            'header' => 'SISTEMA & CUENTA',
        ],
        [
            'title' => 'Perfil & 2FA',
            'icon' => 'shield-check',
            'url' => '/profile',
            'route' => 'profile.edit',
        ],
    ],

];

