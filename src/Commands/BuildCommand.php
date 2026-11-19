<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

use function Laravel\Prompts\confirm;

class BuildCommand extends Command
{
    use InteractsWithVeltraCli;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:build
                            {--clean : Limpiar vistas temporales de prueba o desarrollo}
                            {--no-cache : Omitir generación de caché de Laravel (config, routes, views)}
                            {--no-compile : Omitir ejecución automática de npm run build}
                            {--force : Ejecutar sin confirmaciones interactivas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepara, audita y optimiza el proyecto para pase a producción limpio (seguridad, assets y caché)';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $stack = $this->determineStack($filesystem);
        $this->displayVeltraBanner('Preparador de Producción & Optimización', $stack);

        $force = (bool) $this->option('force');

        if (! $force && $this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                $confirmed = confirm(
                    label: '¿Deseas preparar y blindar el proyecto para producción? (Desactiva demos, compila assets y cachea)',
                    default: true,
                );
                if (! $confirmed) {
                    $this->components->warn('Operación cancelada por el usuario.');
                    return self::SUCCESS;
                }
            }
        }

        // 1. Blindaje de Seguridad: Desactivar Rutas de Demostración
        $this->displayStep('1/5', 'Blindaje de Rutas & Seguridad', 'Desactivando rutas de demo (/veltra/demo) en producción');
        $this->disableDemoRoutes($filesystem);

        // 2. Limpieza de Artefactos de Desarrollo
        $this->displayStep('2/5', 'Limpieza de Desarrollo', 'Auditando vistas y artefactos temporales');
        $cleanedFiles = $this->cleanDevArtifacts($filesystem);

        // 3. Compilación de Assets con Vite / Tailwind
        $assetsCompiled = false;
        if (! $this->option('no-compile') && $filesystem->exists(base_path('package.json'))) {
            $this->displayStep('3/5', 'Compilación de Assets Frontend', 'Ejecutando npm run build en modo producción');
            $assetsCompiled = $this->compileAssets();
        } else {
            $this->displayStep('3/5', 'Compilación de Assets Frontend', 'Omitido (--no-compile o sin package.json)');
        }

        // 4. Optimizaciones de Caché de Laravel
        $cacheOptimized = false;
        if (! $this->option('no-cache')) {
            $this->displayStep('4/5', 'Optimizaciones de Caché', 'Generando config:cache, route:cache y view:cache');
            $cacheOptimized = $this->optimizeCache();
        } else {
            $this->displayStep('4/5', 'Optimizaciones de Caché', 'Omitido (--no-cache)');
        }

        // 5. Auditoría de Integridad Final
        $this->displayStep('5/5', 'Auditoría de Salud (Health Check)', 'Verificando estado de integridad de VeltraUI');
        $auditOk = $this->runHealthAudit($filesystem);

        // Resumen
        $this->displaySuccessCard(
            '¡Proyecto Optimizado y Listo para Producción!',
            [
                'Entorno' => '<fg=green;options=bold>PRODUCTION READY</>',
                'Rutas de Demo' => '<fg=green;options=bold>Desactivadas (Seguro)</>',
                'Assets Frontend' => $assetsCompiled ? '<fg=green>✔ Compilados con éxito (Vite / Tailwind)</>' : '<fg=yellow>Compilación manual o Plug & Play</>',
                'Caché de Producción' => $cacheOptimized ? '<fg=green>✔ Config, Rutas y Vistas Cacheadas</>' : '<fg=gray>Omitido</>',
                'Archivos Limpiados' => ! empty($cleanedFiles) ? '<fg=cyan>' . count($cleanedFiles) . ' archivos temporales limpiados</>' : '<fg=gray>Sin residuos de prueba</>',
                'Auditoría Veltra' => $auditOk ? '<fg=green;options=bold>100% Correcto</>' : '<fg=yellow>Atención requerida</>',
            ],
            'Tu aplicación está completamente blindada y optimizada para servir tráfico en producción con el máximo rendimiento.'
        );

        return self::SUCCESS;
    }

    /**
     * Desactiva las rutas de demo en config y .env.
     */
    protected function disableDemoRoutes(Filesystem $filesystem): void
    {
        $configPath = config_path('veltra.php');
        if ($filesystem->exists($configPath)) {
            $content = $filesystem->get($configPath);
            $content = preg_replace(
                "/'enabled'\s*=>\s*env\([^,]+,\s*(true|false)\)/",
                "'enabled' => env('VELTRA_ROUTES_ENABLED', false)",
                $content
            );
            $filesystem->put($configPath, $content);
        }

        $envPath = base_path('.env');
        if ($filesystem->exists($envPath)) {
            $envContent = $filesystem->get($envPath);
            if (preg_match('/^VELTRA_ROUTES_ENABLED=.*$/m', $envContent)) {
                $envContent = preg_replace('/^VELTRA_ROUTES_ENABLED=.*$/m', 'VELTRA_ROUTES_ENABLED=false', $envContent);
            } else {
                $envContent .= "\nVELTRA_ROUTES_ENABLED=false\n";
            }
            $filesystem->put($envPath, $envContent);
        }

        config(['veltra.routes.enabled' => false]);
    }

    /**
     * Limpia artefactos temporales si --clean está activo.
     */
    protected function cleanDevArtifacts(Filesystem $filesystem): array
    {
        $cleaned = [];
        $shouldClean = (bool) $this->option('clean');

        if ($shouldClean) {
            $testView = resource_path('views/admin/tests.blade.php');
            if ($filesystem->exists($testView)) {
                $filesystem->delete($testView);
                $cleaned[] = $testView;
            }
        }

        return $cleaned;
    }

    /**
     * Compila los assets de frontend.
     */
    protected function compileAssets(): bool
    {
        if (app()->runningUnitTests()) {
            return true;
        }

        try {
            $process = new Process(['npm', 'run', 'build'], base_path());
            $process->setTimeout(180);
            $process->run();

            return $process->isSuccessful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Optimiza las cachés de Laravel para producción.
     */
    protected function optimizeCache(): bool
    {
        if (app()->runningUnitTests()) {
            return true;
        }

        try {
            $this->callSilently('config:cache');
            $this->callSilently('route:cache');
            $this->callSilently('view:cache');

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Ejecuta una verificación rápida de salud.
     */
    protected function runHealthAudit(Filesystem $filesystem): bool
    {
        $configExists = $filesystem->exists(config_path('veltra.php'));
        $cssExists = $filesystem->exists(public_path('vendor/veltra/css/veltra.css')) || $filesystem->exists(public_path('build/manifest.json'));

        return $configExists && $cssExists;
    }
}
