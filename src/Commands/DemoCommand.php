<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

use function Laravel\Prompts\confirm;

class DemoCommand extends Command
{
    use InteractsWithVeltraCli;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:demo
                            {--enable : Habilitar rutas de demostración}
                            {--disable : Deshabilitar rutas de demostración}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Habilita o deshabilita la vitrina de demostración de componentes interactivos de VeltraUI';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $this->displayVeltraBanner('Vitrina Interactiva de Demostración');

        $configPath = config_path('veltra.php');
        if (! $filesystem->exists($configPath)) {
            $defaultConfig = __DIR__ . '/../../config/veltra.php';
            $filesystem->ensureDirectoryExists(dirname($configPath));
            $filesystem->copy($defaultConfig, $configPath);
        }

        $content = $filesystem->get($configPath);
        $currentlyEnabled = config('veltra.routes.enabled', false);

        if ($this->option('enable')) {
            $enable = true;
        } elseif ($this->option('disable')) {
            $enable = false;
        } elseif ($this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                $enable = confirm(
                    label: $currentlyEnabled
                        ? 'Las rutas de demo están activas. ¿Deseas mantenerlas habilitadas?'
                        : '¿Deseas habilitar la vitrina interactiva de demostración (/veltra/demo)?',
                    default: ! $currentlyEnabled,
                );
            } else {
                $enable = ! $currentlyEnabled;
            }
        } else {
            $enable = true;
        }

        $enableStr = $enable ? 'true' : 'false';
        $content = preg_replace(
            "/'enabled'\s*=>\s*env\([^,]+,\s*(true|false)\)/",
            "'enabled' => env('VELTRA_ROUTES_ENABLED', {$enableStr})",
            $content
        );

        $filesystem->put($configPath, $content);

        $demoUrl = url(config('veltra.routes.prefix', 'veltra') . '/demo');

        if ($enable) {
            $this->displaySuccessCard(
                'Vitrina de Demostración Habilitada con Éxito',
                [
                    'Estado' => '<fg=green;options=bold>Activo</>',
                    'URL de la Vitrina' => "<fg=cyan;options=bold>{$demoUrl}</>",
                    'Componentes' => 'Botones, Cards, Tablas, Estadísticas, Modales, Dark Mode',
                    'Configuración' => 'config/veltra.php (\'routes.enabled\')',
                ],
                'Puedes desactivar estas rutas en producción ejecutando php artisan veltra:demo --disable'
            );
        } else {
            $this->displaySuccessCard(
                'Vitrina de Demostración Deshabilitada',
                [
                    'Estado' => '<fg=yellow;options=bold>Desactivado (Producción Limpio)</>',
                    'Configuración' => 'config/veltra.php (\'routes.enabled\' => false)',
                ],
                'Para volver a habilitarla, ejecuta php artisan veltra:demo --enable'
            );
        }

        return self::SUCCESS;
    }
}
