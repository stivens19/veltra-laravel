<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

class DoctorCommand extends Command
{
    use InteractsWithVeltraCli;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:doctor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica la integridad de la instalación y configuración de VeltraUI';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $this->displayVeltraBanner('Diagnóstico de Entorno & Verificación');
        $this->line('  <fg=cyan;options=bold>Iniciando diagnóstico del entorno VeltraUI...</>');
        $this->newLine();

        $rows = [];
        $hasIssues = false;

        // 1. Configuración
        $configExists = $filesystem->exists(config_path('veltra.php'));
        $rows[] = [
            'Configuración',
            'config/veltra.php',
            $configExists ? '<fg=green>CORRECTO</fg=green>' : '<fg=yellow>FALTA PUBLICAR</fg=yellow>',
            $configExists ? 'Archivo cargado en el sistema' : 'Ejecuta: php artisan veltra:publish --config',
        ];
        if (! $configExists) {
            $hasIssues = true;
        }

        // 2. CSS Compilado
        $cssPath = public_path('vendor/veltra/css/veltra.css');
        $cssExists = $filesystem->exists($cssPath);
        $rows[] = [
            'Estilos CSS',
            'public/vendor/veltra/css/veltra.css',
            $cssExists ? '<fg=green>CORRECTO</fg=green>' : '<fg=red>NO ENCONTRADO</fg=red>',
            $cssExists ? 'Tamaño: ' . round($filesystem->size($cssPath) / 1024, 1) . ' KB' : 'Ejecuta: php artisan veltra:publish --assets',
        ];
        if (! $cssExists) {
            $hasIssues = true;
        }

        // 3. JavaScript Compilado
        $jsPath = public_path('vendor/veltra/js/veltra.umd.js');
        $jsExists = $filesystem->exists($jsPath);
        $rows[] = [
            'Scripts JS',
            'public/vendor/veltra/js/veltra.umd.js',
            $jsExists ? '<fg=green>CORRECTO</fg=green>' : '<fg=red>NO ENCONTRADO</fg=red>',
            $jsExists ? 'Runtime VeltraUI disponible' : 'Ejecuta: php artisan veltra:publish --assets',
        ];
        if (! $jsExists) {
            $hasIssues = true;
        }

        // 4. Logo e Imágenes
        $logoPath = public_path('vendor/veltra/img/logo.png');
        $logoExists = $filesystem->exists($logoPath);
        $rows[] = [
            'Imágenes de Marca',
            'public/vendor/veltra/img/logo.png',
            $logoExists ? '<fg=green>CORRECTO</fg=green>' : '<fg=yellow>OPCIONAL</fg=yellow>',
            $logoExists ? 'Logo por defecto presente' : 'Ejecuta: php artisan veltra:publish --assets',
        ];

        // 5. Modo de Assets
        $assetMode = config('veltra.assets.mode', 'compiled');
        $rows[] = [
            'Modo de Assets',
            $assetMode === 'compiled' ? 'Plug & Play (Sin Node)' : 'Vite Pipeline',
            '<fg=green>ACTIVO</fg=green>',
            $assetMode === 'compiled' ? 'Carga desde public/vendor/veltra' : 'Compilación nativa con @vite',
        ];

        // 6. Skills de IA para Agentes
        $skillsCheck = [
            'Skill AI (VeltraUI)' => ['.agents/skills/veltra-ui/SKILL.md', 'Reglas de diseño UI & Multi-Stack'],
            'Skill AI (Seguridad)' => ['.agents/skills/veltra-security/SKILL.md', 'Reglas OWASP Top 10 y hardening'],
            'Skill AI (Rendimiento)' => ['.agents/skills/veltra-performance/SKILL.md', 'Optimización N+1, caché y queries'],
            'Skill AI (Testing)' => ['.agents/skills/veltra-testing/SKILL.md', 'Estándares Pest & PHPUnit'],
        ];

        foreach ($skillsCheck as $label => [$path, $desc]) {
            $exists = $filesystem->exists(base_path($path));
            $rows[] = [
                $label,
                $path,
                $exists ? '<fg=green>INSTALADA</fg=green>' : '<fg=yellow>OPCIONAL</fg=yellow>',
                $exists ? $desc : 'Ejecuta: php artisan veltra:skill',
            ];
        }

        // 7. Servidor MCP (Model Context Protocol)
        $mcpConfigExists = $filesystem->exists(base_path('.agents/mcp_config.json'));
        $rows[] = [
            'Configuración MCP',
            '.agents/mcp_config.json',
            $mcpConfigExists ? '<fg=green>DISPONIBLE</fg=green>' : '<fg=yellow>OPCIONAL</fg=yellow>',
            $mcpConfigExists ? 'Listo para Cursor / Claude Desktop' : 'Ejecuta: php artisan veltra:skill',
        ];

        // 8. Blindaje de Seguridad en Rutas
        $demoEnabled = config('veltra.routes.enabled', false);
        $isProd = app()->environment('production');
        $rows[] = [
            'Rutas de Demostración',
            '/veltra/demo',
            $demoEnabled ? ($isProd ? '<fg=red>VULNERABLE (PROD)</fg=red>' : '<fg=yellow>HABILITADAS</fg=yellow>') : '<fg=green>BLINDADAS</fg=green>',
            $demoEnabled ? ($isProd ? '¡Peligro! Desactiva en prod con: php artisan veltra:build' : 'Rutas activas para desarrollo') : 'Rutas deshabilitadas para producción',
        ];
        if ($isProd && $demoEnabled) {
            $hasIssues = true;
        }

        $this->table(['Componente', 'Ubicación / Clave', 'Estado', 'Detalles'], $rows);
        $this->newLine();

        if ($hasIssues) {
            $this->displaySuccessCard(
                'Diagnóstico Finalizado con Advertencias',
                [
                    'Resultado' => '<fg=yellow;options=bold>Atención Requerida</>',
                    'Solución' => '<fg=cyan;options=bold>php artisan veltra:publish --all</>',
                ],
                'Ejecuta el publicador para sincronizar los recursos faltantes.'
            );
        } else {
            $this->displaySuccessCard(
                'Entorno Verificado Exitosamente',
                [
                    'Estado General' => '<fg=green;options=bold>100% Operativo</>',
                    'Compatibilidad' => 'VeltraUI Enterprise Ready',
                    'Dashboard' => url('/dashboard'),
                ],
                'Tu instalación está lista para renderizar componentes y dashboards de alta fidelidad.'
            );
        }

        return self::SUCCESS;
    }
}

