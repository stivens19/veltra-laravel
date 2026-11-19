<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class ThemeCommand extends Command
{
    use InteractsWithVeltraCli;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:theme
                            {--name= : Nombre de la marca o proyecto}
                            {--theme= : Tema por defecto (light, dark, system)}
                            {--boxed : Activar diseño encajonado (Boxed layout)}
                            {--collapsed : Iniciar con sidebar colapsado}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura interactivamente la identidad de marca, modo oscuro y preferencias de layout de VeltraUI';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $this->displayVeltraBanner('Personalización de Marca, Tema & Layout');

        $configPath = config_path('veltra.php');
        if (! $filesystem->exists($configPath)) {
            $defaultConfig = __DIR__ . '/../../config/veltra.php';
            $filesystem->ensureDirectoryExists(dirname($configPath));
            $filesystem->copy($defaultConfig, $configPath);
            $this->components->info('Archivo [config/veltra.php] publicado para configuración.');
        }

        $content = $filesystem->get($configPath);

        // 1. Nombre de la Marca
        $brandName = $this->option('name');
        if (! $brandName && $this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                $brandName = text(
                    label: 'Nombre principal de tu aplicación / marca:',
                    default: config('veltra.brand.name', 'VeltraUI'),
                    required: true,
                );
            }
        }
        $brandName = $brandName ?: config('veltra.brand.name', 'VeltraUI');

        // 2. Tema por defecto
        $defaultTheme = $this->option('theme');
        if (! $defaultTheme && $this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                $themeMap = [
                    '☀️ Modo Claro (Light)' => 'light',
                    '🌙 Modo Oscuro (Dark)' => 'dark',
                    '💻 Automático según el Sistema Operativo (System)' => 'system',
                ];

                $choice = select(
                    label: 'Tema visual por defecto al abrir el sistema:',
                    options: array_keys($themeMap),
                    default: array_keys($themeMap)[0],
                );
                $defaultTheme = $themeMap[$choice] ?? 'light';
            }
        }
        $defaultTheme = in_array($defaultTheme, ['light', 'dark', 'system']) ? $defaultTheme : 'light';

        // 3. Estilo de Layout
        $boxed = $this->option('boxed');
        if (! $boxed && $this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                $layoutMap = [
                    'Pantalla Completa Fluida (Fluid — Recomendado)' => 'fluid',
                    'Diseño Encajonado / Caja Centrada (Boxed 1340px)' => 'boxed',
                ];

                $layoutChoice = select(
                    label: 'Estilo de contenedor general:',
                    options: array_keys($layoutMap),
                    default: array_keys($layoutMap)[0],
                );
                $boxed = (($layoutMap[$layoutChoice] ?? 'fluid') === 'boxed');
            }
        }

        // 4. Sidebar Colapsado
        $collapsed = $this->option('collapsed');
        if (! $collapsed && $this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                $collapsed = confirm(
                    label: '¿Deseas que el sidebar inicie colapsado (icon-only)?',
                    default: (bool) config('veltra.layout.sidebar_collapsed', false),
                );
            }
        }

        // Aplicar cambios en config/veltra.php
        $content = preg_replace(
            "/'name'\s*=>\s*env\([^,]+,\s*'[^']*'\)/",
            "'name' => env('VELTRA_BRAND_NAME', '{$brandName}')",
            $content
        );

        $content = preg_replace(
            "/'short_name'\s*=>\s*'[^']*'/",
            "'short_name' => '{$brandName}'",
            $content
        );

        $content = preg_replace(
            "/'suffix'\s*=>\s*'[^']*'/",
            "'suffix' => ''",
            $content
        );

        $content = preg_replace(
            "/'default'\s*=>\s*env\([^,]+,\s*'[^']*'\)/",
            "'default' => env('VELTRA_THEME_DEFAULT', '{$defaultTheme}')",
            $content
        );

        $collapsedStr = $collapsed ? 'true' : 'false';
        $content = preg_replace(
            "/'sidebar_collapsed'\s*=>\s*(true|false)/",
            "'sidebar_collapsed' => {$collapsedStr}",
            $content
        );

        $boxedStr = $boxed ? 'true' : 'false';
        if (preg_match("/'boxed'\s*=>\s*.*$/m", $content)) {
            $content = preg_replace("/'boxed'\s*=>\s*.*$/m", "'boxed' => env('VELTRA_LAYOUT_BOXED', {$boxedStr}),", $content);
        } else {
            $content = preg_replace("/('layout'\s*=>\s*\[)/", "$1\n        'boxed' => env('VELTRA_LAYOUT_BOXED', {$boxedStr}),", $content);
        }

        $filesystem->put($configPath, $content);

        // Actualizar .env si existe
        $envPath = base_path('.env');
        if ($filesystem->exists($envPath)) {
            $envContent = $filesystem->get($envPath);

            // APP_NAME
            if (preg_match('/^APP_NAME=.*$/m', $envContent)) {
                $envContent = preg_replace('/^APP_NAME=.*$/m', 'APP_NAME="' . $brandName . '"', $envContent);
            } else {
                $envContent .= "\nAPP_NAME=\"" . $brandName . "\"\n";
            }

            // VELTRA_BRAND_NAME
            if (preg_match('/^VELTRA_BRAND_NAME=.*$/m', $envContent)) {
                $envContent = preg_replace('/^VELTRA_BRAND_NAME=.*$/m', 'VELTRA_BRAND_NAME="' . $brandName . '"', $envContent);
            } else {
                $envContent .= "\nVELTRA_BRAND_NAME=\"" . $brandName . "\"\n";
            }

            // VELTRA_THEME_DEFAULT
            if (preg_match('/^VELTRA_THEME_DEFAULT=.*$/m', $envContent)) {
                $envContent = preg_replace('/^VELTRA_THEME_DEFAULT=.*$/m', 'VELTRA_THEME_DEFAULT=' . $defaultTheme, $envContent);
            } else {
                $envContent .= "\nVELTRA_THEME_DEFAULT=" . $defaultTheme . "\n";
            }

            // VELTRA_LAYOUT_BOXED
            if (preg_match('/^VELTRA_LAYOUT_BOXED=.*$/m', $envContent)) {
                $envContent = preg_replace('/^VELTRA_LAYOUT_BOXED=.*$/m', 'VELTRA_LAYOUT_BOXED=' . $boxedStr, $envContent);
            } else {
                $envContent .= "\nVELTRA_LAYOUT_BOXED=" . $boxedStr . "\n";
            }

            $filesystem->put($envPath, $envContent);
        }

        // Limpiar caché de configuración para aplicar de inmediato
        try {
            $this->callSilently('config:clear');
        } catch (\Throwable $e) {}

        // Resumen
        $this->displaySuccessCard(
            '¡Configuración de VeltraUI actualizada con éxito!',
            [
                'Nombre de Marca' => "<fg=cyan;options=bold>{$brandName}</>",
                'Tema por Defecto' => "<fg=yellow>{$defaultTheme}</>",
                'Estilo de Layout' => $boxed ? '<fg=white>Boxed (Encajonado)</>' : '<fg=green>Fluid (Pantalla Completa)</>',
                'Sidebar Inicial' => $collapsed ? '<fg=yellow>Colapsado (Iconos)</>' : '<fg=green>Expandido</>',
                'Archivo Modificado' => '<fg=gray>config/veltra.php</>',
            ]
        );

        return self::SUCCESS;
    }
}
