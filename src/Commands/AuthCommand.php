<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

class AuthCommand extends Command
{
    use InteractsWithVeltraCli;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:auth
                            {--views-only : Solo publicar las vistas Blade sin controladores ni rutas}
                            {--force : Sobrescribir archivos existentes sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instala las vistas, controladores y rutas completas de autenticación de VeltraUI';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $this->displayVeltraBanner('Módulo de Autenticación & Control de Acceso');

        $force = (bool) $this->option('force');
        $viewsOnly = (bool) $this->option('views-only');

        // 1. Publicar Vistas Blade
        $this->displayStep('1/2', 'Publicando Vistas Blade de Autenticación');
        $this->publishViews($filesystem, $force);

        // 2. Si no es views-only, instalar controladores, rutas y migraciones funcionales
        if (! $viewsOnly) {
            $this->displayStep('2/3', 'Configurando Controladores y Rutas');
            $this->scaffoldControllersAndRoutes($filesystem, $force);
            $this->ensureUserModelHasTwoFactor($filesystem);

            $this->displayStep('3/3', 'Configurando Base de Datos para 2FA');
            $this->ensureTwoFactorMigration($filesystem);
        }

        $this->displaySuccessCard(
            'Módulo Veltra Auth Configurado Exitosamente',
            [
                'Iniciar Sesión' => '<fg=cyan;options=bold>' . url('/login') . '</>',
                'Registro de Cuenta' => '<fg=cyan;options=bold>' . url('/register') . '</>',
                'Recuperar Contraseña' => '<fg=cyan;options=bold>' . url('/forgot-password') . '</>',
                'Vistas Publicadas' => 'resources/views/auth/ (login, register, forgot-password, reset-password, 2fa-challenge)',
            ],
            'Las vistas incorporan tokens de diseño VeltraUI, modo oscuro y soporte para 2FA.'
        );

        return self::SUCCESS;
    }

    /**
     * Publish Blade authentication views.
     */
    protected function publishViews(Filesystem $filesystem, bool $force): void
    {
        $sourceDir = __DIR__ . '/../../resources/views/auth';
        $targetDir = resource_path('views/auth');

        $filesystem->ensureDirectoryExists($targetDir);

        $files = $filesystem->files($sourceDir);
        foreach ($files as $file) {
            $targetPath = $targetDir . DIRECTORY_SEPARATOR . $file->getFilename();

            if (! $filesystem->exists($targetPath) || $force) {
                $filesystem->copy($file->getPathname(), $targetPath);
                $this->line("  - <fg=green>Publicado:</fg=green> resources/views/auth/{$file->getFilename()}");
            }
        }

        // Layout guest
        $guestLayoutSource = __DIR__ . '/../../resources/views/components/layout/guest.blade.php';
        $guestLayoutTarget = resource_path('views/layouts/guest.blade.php');

        $filesystem->ensureDirectoryExists(resource_path('views/layouts'));

        if (! $filesystem->exists($guestLayoutTarget) || $force) {
            $filesystem->copy($guestLayoutSource, $guestLayoutTarget);
            $this->line("  - <fg=green>Publicado:</fg=green> resources/views/layouts/guest.blade.php");
        }

        // Vista de Perfil y Seguridad (2FA)
        $profileTarget = resource_path('views/profile.blade.php');
        $profileStub = __DIR__ . '/../../stubs/auth/profile.stub';
        if (! $filesystem->exists($profileTarget) || $force) {
            if ($filesystem->exists($profileStub)) {
                $filesystem->copy($profileStub, $profileTarget);
                $this->line("  - <fg=green>Publicado:</fg=green> resources/views/profile.blade.php");
            }
        }
    }

    /**
     * Scaffold controllers, requests and auth routes.
     */
    protected function scaffoldControllersAndRoutes(Filesystem $filesystem, bool $force): void
    {
        $this->components->info('Generando controladores y rutas de autenticación...');

        // Controladores de Auth
        $controllers = [
            'AuthenticatedSessionController.php' => app_path('Http/Controllers/Auth/AuthenticatedSessionController.php'),
            'RegisteredUserController.php' => app_path('Http/Controllers/Auth/RegisteredUserController.php'),
            'PasswordResetLinkController.php' => app_path('Http/Controllers/Auth/PasswordResetLinkController.php'),
            'TwoFactorChallengeController.php' => app_path('Http/Controllers/Auth/TwoFactorChallengeController.php'),
        ];

        $filesystem->ensureDirectoryExists(app_path('Http/Controllers/Auth'));

        foreach ($controllers as $name => $destination) {
            if (! $filesystem->exists($destination) || $force) {
                $stubName = str_replace('.php', '.stub', $name);
                $stubPath = __DIR__ . "/../../stubs/auth/{$stubName}";
                if ($filesystem->exists($stubPath)) {
                    $filesystem->copy($stubPath, $destination);
                    $this->line("  - <fg=green>Controlador:</fg=green> app/Http/Controllers/Auth/{$name}");
                }
            }
        }

        // Controlador de Perfil y 2FA
        $profileController = app_path('Http/Controllers/ProfileController.php');
        $profileStub = __DIR__ . '/../../stubs/auth/ProfileController.stub';
        if (! $filesystem->exists($profileController) || $force) {
            if ($filesystem->exists($profileStub)) {
                $filesystem->copy($profileStub, $profileController);
                $this->line("  - <fg=green>Controlador:</fg=green> app/Http/Controllers/ProfileController.php");
            }
        }

        // Request de Login
        $filesystem->ensureDirectoryExists(app_path('Http/Requests/Auth'));
        $loginRequestPath = app_path('Http/Requests/Auth/LoginRequest.php');
        $loginRequestStub = __DIR__ . '/../../stubs/auth/LoginRequest.stub';

        if ((! $filesystem->exists($loginRequestPath) || $force) && $filesystem->exists($loginRequestStub)) {
            $filesystem->copy($loginRequestStub, $loginRequestPath);
            $this->line("  - <fg=green>Request:</fg=green> app/Http/Requests/Auth/LoginRequest.php");
        }

        // Rutas de Auth
        $authRoutesPath = base_path('routes/auth.php');
        $authRoutesStub = __DIR__ . '/../../stubs/auth/routes.stub';

        if ((! $filesystem->exists($authRoutesPath) || $force) && $filesystem->exists($authRoutesStub)) {
            $filesystem->copy($authRoutesStub, $authRoutesPath);
            $this->line("  - <fg=green>Rutas:</fg=green> routes/auth.php");
        }

        // Vincular en routes/web.php si no está incluido
        $webRoutesPath = base_path('routes/web.php');
        if ($filesystem->exists($webRoutesPath)) {
            $webContent = $filesystem->get($webRoutesPath);
            if (! str_contains($webContent, "require __DIR__.'/auth.php'")) {
                $filesystem->append($webRoutesPath, "\nrequire __DIR__.'/auth.php';\n");
                $this->line("  - <fg=green>Vinculado:</fg=green> require __DIR__.'/auth.php' en routes/web.php");
            }
        }
    }

    /**
     * Ensure the 2FA columns exist in the users table.
     */
    protected function ensureTwoFactorMigration(Filesystem $filesystem): void
    {
        $hasColumns = false;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $hasColumns = \Illuminate\Support\Facades\Schema::hasColumn('users', 'two_factor_secret');
            }
        } catch (\Throwable $e) {
            $hasColumns = false;
        }

        if (! $hasColumns) {
            $existing = glob(database_path('migrations/*add_two_factor_columns_to_users_table.php'));
            if (empty($existing)) {
                $timestamp = date('Y_m_d_His');
                $migrationTarget = database_path("migrations/{$timestamp}_add_two_factor_columns_to_users_table.php");
                $migrationStub = __DIR__ . '/../../stubs/auth/add_two_factor_columns_to_users_table.stub';

                if ($filesystem->exists($migrationStub)) {
                    $filesystem->copy($migrationStub, $migrationTarget);
                    $this->line("  - <fg=green>Migración 2FA:</fg=green> database/migrations/{$timestamp}_add_two_factor_columns_to_users_table.php");

                    try {
                        $this->callSilently('migrate');
                        $this->line("  - <fg=cyan>Base de datos:</fg=cyan> Columnas de autenticación 2FA creadas con éxito.");
                    } catch (\Throwable $e) {
                        $this->warn("  - Por favor ejecute: php artisan migrate");
                    }
                }
            }
        } else {
            $this->line("  - <fg=green>Base de datos:</fg=green> Columnas 2FA ya configuradas en tabla 'users'.");
        }
    }

    /**
     * Ensure the User model has 2FA support method.
     */
    protected function ensureUserModelHasTwoFactor(Filesystem $filesystem): void
    {
        $userModelPath = app_path('Models/User.php');
        if (! $filesystem->exists($userModelPath)) {
            return;
        }

        $content = $filesystem->get($userModelPath);

        if (! str_contains($content, 'hasEnabledTwoFactorAuthentication')) {
            $method = <<<'PHP'

    /**
     * Determine if two-factor authentication is enabled and confirmed.
     */
    public function hasEnabledTwoFactorAuthentication(): bool
    {
        return ! empty($this->two_factor_secret) && ! empty($this->two_factor_confirmed_at);
    }
}
PHP;
            $content = preg_replace('/\}\s*$/', $method, $content);
            $filesystem->put($userModelPath, $content);
            $this->line("  - <fg=green>Modelo:</fg=green> app/Models/User.php actualizado con hasEnabledTwoFactorAuthentication().");
        }
    }
}
