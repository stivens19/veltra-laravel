<?php

namespace Veltra\VeltraLaravel;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Veltra\VeltraLaravel\Commands\AuthCommand;
use Veltra\VeltraLaravel\Commands\BuildCommand;
use Veltra\VeltraLaravel\Commands\ComponentCommand;
use Veltra\VeltraLaravel\Commands\CrudCommand;
use Veltra\VeltraLaravel\Commands\DemoCommand;
use Veltra\VeltraLaravel\Commands\DoctorCommand;
use Veltra\VeltraLaravel\Commands\IconsCommand;
use Veltra\VeltraLaravel\Commands\InstallCommand;
use Veltra\VeltraLaravel\Commands\MakePageCommand;
use Veltra\VeltraLaravel\Commands\McpCommand;
use Veltra\VeltraLaravel\Commands\MenuCommand;
use Veltra\VeltraLaravel\Commands\PublishCommand;
use Veltra\VeltraLaravel\Commands\ReferenceCommand;
use Veltra\VeltraLaravel\Commands\SkillCommand;
use Veltra\VeltraLaravel\Commands\ThemeCommand;

class VeltraLaravelServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/veltra.php',
            'veltra'
        );

        $this->app->singleton('veltra', function ($app) {
            return new VeltraManager($app['config']['veltra'] ?? []);
        });

        $this->app->singleton(\Veltra\VeltraLaravel\Services\TwoFactorService::class, function () {
            return new \Veltra\VeltraLaravel\Services\TwoFactorService();
        });
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // 1. Cargar Vistas
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'veltra');

        // 2. Registrar componentes anónimos Blade con prefijo <x-veltra::*>
        Blade::anonymousComponentPath(__DIR__ . '/../resources/views/components', 'veltra');

        // Aliases globales para layouts tipo Starter Kit (<x-app-layout> y <x-guest-layout>)
        Blade::component(view()->exists('layouts.app') ? 'layouts.app' : 'veltra::layouts.app', 'app-layout');
        Blade::component(view()->exists('layouts.guest') ? 'layouts.guest' : 'veltra::layouts.guest', 'guest-layout');

        // 3. Registrar rutas opcionales de demostración
        $this->registerRoutes();

        // 4. Operaciones de consola (Publicaciones y Comandos)
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
            $this->registerCommands();
        }
    }

    /**
     * Register package routes.
     */
    protected function registerRoutes(): void
    {
        if (config('veltra.routes.enabled', true)) {
            Route::group([
                'prefix' => config('veltra.routes.prefix', 'veltra'),
                'middleware' => config('veltra.routes.middleware', ['web']),
            ], function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });
        }
    }

    /**
     * Register publishable resources.
     */
    protected function registerPublishing(): void
    {
        // Configuración
        $this->publishes([
            __DIR__ . '/../config/veltra.php' => config_path('veltra.php'),
        ], 'veltra-config');

        // Assets públicos precompilados
        $this->publishes([
            __DIR__ . '/../resources/dist' => public_path('vendor/veltra'),
        ], 'veltra-assets');

        // Vistas y Componentes Blade completos
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/veltra'),
        ], 'veltra-views');

        // Vistas de Errores HTTP oficiales (404, 403, 500, 419, 503, 401)
        $this->publishes([
            __DIR__ . '/../resources/views/errors' => resource_path('views/errors'),
        ], 'veltra-errors');

        // Vistas de Autenticación y Perfil listas para Breeze / Fortify / Limpio
        $this->publishes([
            __DIR__ . '/../resources/views/auth' => resource_path('views/auth'),
            __DIR__ . '/../resources/views/profile.blade.php' => resource_path('views/profile.blade.php'),
            __DIR__ . '/../resources/views/layouts/guest.blade.php' => resource_path('views/layouts/guest.blade.php'),
        ], 'veltra-auth-views');

        // Stubs para generación de páginas
        $this->publishes([
            __DIR__ . '/../stubs' => base_path('stubs/veltra'),
        ], 'veltra-stubs');

        // Skills de IA oficiales para agentes (UI, Seguridad OWASP, Rendimiento y Testing) y protocolo MCP
        $this->publishes([
            __DIR__ . '/../resources/skills/veltra-ui' => base_path('.agents/skills/veltra-ui'),
            __DIR__ . '/../resources/skills/veltra-security' => base_path('.agents/skills/veltra-security'),
            __DIR__ . '/../resources/skills/veltra-performance' => base_path('.agents/skills/veltra-performance'),
            __DIR__ . '/../resources/skills/veltra-testing' => base_path('.agents/skills/veltra-testing'),
            __DIR__ . '/../resources/mcp' => base_path('.agents'),
        ], 'veltra-skill');
    }

    /**
     * Register Artisan commands.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            InstallCommand::class,
            AuthCommand::class,
            PublishCommand::class,
            MakePageCommand::class,
            DoctorCommand::class,
            CrudCommand::class,
            ComponentCommand::class,
            MenuCommand::class,
            ThemeCommand::class,
            IconsCommand::class,
            DemoCommand::class,
            ReferenceCommand::class,
            BuildCommand::class,
            SkillCommand::class,
            McpCommand::class,
        ]);
    }
}

