<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MenuCommand extends Command
{
    use InteractsWithVeltraCli;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:menu
                            {--list : Listar todos los elementos y secciones del menú}
                            {--add : Agregar un nuevo elemento al menú}
                            {--title= : Título del elemento}
                            {--url= : URL o ruta}
                            {--icon= : Icono de Lucide}
                            {--section= : Sección del menú}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Administra los elementos y secciones de navegación del Sidebar en config/veltra.php';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $this->displayVeltraBanner('Gestor de Navegación & Sidebar');

        $configPath = config_path('veltra.php');
        if (! $filesystem->exists($configPath)) {
            $defaultConfig = __DIR__ . '/../../config/veltra.php';
            $filesystem->ensureDirectoryExists(dirname($configPath));
            $filesystem->copy($defaultConfig, $configPath);
        }

        $items = config('veltra.navigation', []);

        if ($this->option('list')) {
            $this->listMenu($items);
            return self::SUCCESS;
        }

        if ($this->option('add')) {
            return $this->addItem($filesystem, $configPath);
        }

        if (! $this->input->isInteractive() || app()->runningUnitTests()) {
            $this->listMenu($items);
            return self::SUCCESS;
        }

        if (class_exists(\Laravel\Prompts\Prompt::class)) {
            $actionsMap = [
                '📋 Listar todos los elementos actuales del sidebar' => 'list',
                '➕ Agregar un nuevo elemento al menú' => 'add',
            ];

            $choice = select(
                label: '¿Qué acción deseas realizar en el menú de navegación?',
                options: array_keys($actionsMap),
                default: array_key_first($actionsMap),
            );

            if (($actionsMap[$choice] ?? 'list') === 'list') {
                $this->listMenu($items);
                return self::SUCCESS;
            }

            return $this->addItem($filesystem, $configPath);
        }

        $this->listMenu($items);
        return self::SUCCESS;
    }

    /**
     * Lista los elementos del menú en una tabla estilizada.
     */
    protected function listMenu(array $items): void
    {
        $this->line('  <fg=cyan;options=bold>Estructura Actual del Sidebar (config/veltra.php):</>');
        $this->newLine();

        $rows = [];
        $currentHeader = 'GENERAL';

        foreach ($items as $item) {
            if (isset($item['header'])) {
                $currentHeader = $item['header'];
                $rows[] = [
                    "<fg=yellow;options=bold>── [SECCIÓN] ──</>",
                    "<fg=yellow;options=bold>{$item['header']}</>",
                    '—',
                    '—',
                ];
                continue;
            }

            $title = $item['title'] ?? 'Sin título';
            $icon = $item['icon'] ?? 'circle';
            $url = $item['url'] ?? ($item['route'] ?? '#');

            $rows[] = [
                "<fg=gray>{$currentHeader}</>",
                "<fg=white;options=bold>{$title}</>",
                "<fg=cyan>{$icon}</>",
                "<fg=gray>{$url}</>",
            ];

            if (isset($item['children']) && is_array($item['children'])) {
                foreach ($item['children'] as $child) {
                    $rows[] = [
                        "<fg=gray>{$currentHeader}</>",
                        "  <fg=gray>└─</> " . ($child['title'] ?? 'Subitem'),
                        "<fg=cyan>" . ($child['icon'] ?? 'minus') . "</>",
                        "<fg=gray>" . ($child['url'] ?? '#') . "</>",
                    ];
                }
            }
        }

        $this->table(['Sección', 'Título', 'Icono Lucide', 'URL / Ruta'], $rows);
        $this->newLine();
    }

    /**
     * Agrega un nuevo elemento al menú.
     */
    protected function addItem(Filesystem $filesystem, string $configPath): int
    {
        $title = $this->option('title');
        $url = $this->option('url');
        $icon = $this->option('icon');
        $section = $this->option('section') ?: 'MAIN NAVIGATION';

        if (! $title && $this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                $title = text(label: 'Título del elemento en el menú:', placeholder: 'ej: Inventario', required: true);
                $url = text(label: 'URL o ruta web:', placeholder: 'ej: /inventory', default: '/' . strtolower(str_replace(' ', '-', $title)), required: true);
                $icon = text(label: 'Icono de Lucide:', placeholder: 'ej: package, table, shopping-bag', default: 'circle', required: true);
            }
        }

        $title = $title ?: 'Nuevo Módulo';
        $url = $url ?: '/modulo';
        $icon = $icon ?: 'circle';

        $content = $filesystem->get($configPath);

        $itemString = "        [\n" .
            "            'title' => '{$title}',\n" .
            "            'icon' => '{$icon}',\n" .
            "            'url' => '{$url}',\n" .
            "        ],\n";

        if (str_contains($content, "'header' => '{$section}'")) {
            $pattern = "/('header'\s*=>\s*'" . preg_quote($section, '/') . "'\s*,?\s*],)/";
            if (preg_match($pattern, $content)) {
                $newContent = preg_replace($pattern, "$1\n" . $itemString, $content, 1);
                $filesystem->put($configPath, $newContent);
            }
        } elseif (preg_match("/('navigation'\s*=>\s*\[)(.*?)(\n\s*],)/s", $content)) {
            $newContent = preg_replace("/('navigation'\s*=>\s*\[)(.*?)(\n\s*],)/s", "$1$2" . $itemString . "$3", $content, 1);
            $filesystem->put($configPath, $newContent);
        }

        $this->displaySuccessCard(
            "¡Elemento [{$title}] agregado al menú con éxito!",
            [
                'Título' => "<fg=white;options=bold>{$title}</>",
                'Icono Lucide' => "<fg=cyan>{$icon}</>",
                'URL' => "<fg=yellow>{$url}</>",
                'Sección' => "<fg=white>{$section}</>",
            ]
        );

        return self::SUCCESS;
    }
}
