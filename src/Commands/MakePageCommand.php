<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakePageCommand extends Command
{
    use InteractsWithVeltraCli;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:make-page
                            {name? : Nombre de la página en notación de vistas o componentes (ej: admin.orders, Admin/Orders)}
                            {--template= : Plantilla base (blank, table, analytics, kanban, pos, form)}
                            {--stack= : Stack frontend (livewire, blade, react, vue, svelte; se detecta automáticamente)}
                            {--dir= : Directorio base dentro de resources (views o js/Pages)}
                            {--layout= : Layout a usar (app o veltra; se autodetecta si se omite)}
                            {--menu : Agregar automáticamente al menú lateral de navegación}
                            {--menu-title= : Título personalizado para el menú}
                            {--menu-icon= : Icono de Lucide para el menú}
                            {--menu-section= : Encabezado de sección en el menú (ej: MAIN NAVIGATION)}
                            {--route : Registrar automáticamente la ruta en routes/web.php}
                            {--route-uri= : URI para la ruta web (ej: /admin/orders)}
                            {--force : Sobrescribir si el archivo ya existe}';

    /**
     * Command aliases.
     *
     * @var array<int, string>
     */
    protected $aliases = ['veltra:page'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generador inteligente de páginas adaptado al stack activo (Livewire, Blade, React, Vue, Svelte)';

    /**
     * Plantillas disponibles con metadatos.
     *
     * @var array<string, array{name: string, desc: string, icon: string}>
     */
    protected array $templates = [
        'blank' => [
            'name' => 'Lienzo en Blanco (Workspace)',
            'desc' => 'Canvas limpio w-full listo para construir componentes personalizados',
            'icon' => 'layers',
        ],
        'table' => [
            'name' => 'Tabla de Datos (CRUD List)',
            'desc' => 'Búsqueda, badges de estado, acciones rápidas y paginación',
            'icon' => 'table',
        ],
        'analytics' => [
            'name' => 'Dashboard Analítico (KPIs)',
            'desc' => 'Cuadrícula de estadísticas, tendencias y contenedor de gráficos',
            'icon' => 'activity',
        ],
        'form' => [
            'name' => 'Formulario de Registro / Edición',
            'desc' => 'Campos estilizados, selectores, switches y botones de acción',
            'icon' => 'file-text',
        ],
        'kanban' => [
            'name' => 'Tablero Kanban (Workflow)',
            'desc' => 'Columnas de tareas (Por Hacer, En Progreso, Finalizado)',
            'icon' => 'kanban',
        ],
        'pos' => [
            'name' => 'Punto de Venta / Órdenes (POS)',
            'desc' => 'Catálogo de productos y ticket resumen de orden',
            'icon' => 'shopping-cart',
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $stack = $this->determineStack($filesystem);
        $this->displayVeltraBanner('Asistente de Creación de Módulos & Páginas', $stack);

        // 1. Selección de Plantilla
        $this->displayStep('1/4', 'Selección de Plantilla Base', 'Elige la estructura inicial para tu nueva vista');
        $template = $this->resolveTemplate();

        // 2. Nombre y Ubicación en resources/
        $this->displayStep('2/4', 'Nombre del Módulo y Ubicación en resources/');
        $isInertia = in_array($stack, ['react', 'vue', 'svelte']);
        $name = $this->resolveName($stack);

        // Normalizar según el stack
        if ($isInertia) {
            $extension = match ($stack) {
                'react' => ($filesystem->exists(base_path('tsconfig.json')) ? '.tsx' : '.jsx'),
                'vue' => '.vue',
                'svelte' => '.svelte',
            };
            $subDir = $this->option('dir') ?: 'js/Pages';
            $normalizedPath = str_replace(['.', '\\'], '/', $name);
            if (! Str::endsWith($normalizedPath, ['.tsx', '.jsx', '.vue', '.svelte'])) {
                $normalizedPath .= $extension;
            }
            $componentName = basename($normalizedPath, $extension);
            $baseName = $componentName;
        } else {
            $subDir = $this->option('dir') ?: 'views';
            $normalizedPath = str_replace(['.', '\\'], '/', $name);
            if (! Str::endsWith($normalizedPath, '.blade.php')) {
                $normalizedPath .= '.blade.php';
            }
            $baseName = basename($normalizedPath, '.blade.php');
            $componentName = $baseName;
        }

        $targetFile = resource_path($subDir . '/' . $normalizedPath);
        $targetDirectory = dirname($targetFile);
        $force = (bool) $this->option('force');

        if ($filesystem->exists($targetFile) && ! $force) {
            $this->components->error("El archivo [{$targetFile}] ya existe. Usa --force para sobrescribir.");
            return self::FAILURE;
        }

        $defaultTitle = Str::headline($baseName);

        // 3. Opciones de Menú Lateral
        $this->displayStep('3/4', 'Integración en Menú Lateral (Sidebar Navigation)', 'Agrega un acceso directo para el usuario');
        $addToMenu = $this->resolveMenuOption();
        $menuTitle = null;
        $menuIcon = null;
        $menuSection = null;

        if ($addToMenu) {
            $menuTitle = $this->resolveMenuTitle($defaultTitle);
            $menuIcon = $this->resolveMenuIcon($template);
            $menuSection = $this->resolveMenuSection();
        }

        // 4. Opción de Registrar Ruta Web
        $this->displayStep('4/4', 'Registro de Ruta Web (routes/web.php)', 'Define la URL de acceso directo');
        $registerRoute = $this->resolveRouteOption();
        $routeUri = null;
        if ($registerRoute) {
            $cleanSlug = Str::slug(str_replace(['/', '\\'], '-', str_replace(['.blade.php', '.tsx', '.jsx', '.vue', '.svelte'], '', $normalizedPath)));
            $defaultUri = '/' . $cleanSlug;
            $routeUri = $this->resolveRouteUri($defaultUri);
        }

        // 5. Cargar y compilar stub según el Stack
        if ($isInertia) {
            $stubFile = __DIR__ . "/../../stubs/inertia/{$stack}/Page.stub";
            if (! $filesystem->exists($stubFile)) {
                $stubFile = __DIR__ . "/../../stubs/inertia/{$stack}/Dashboard.stub";
            }
            $stubContent = $filesystem->get($stubFile);
            $inertiaComponent = str_replace(['.tsx', '.jsx', '.vue', '.svelte'], '', $normalizedPath);

            $renderedContent = str_replace(
                ['{{ componentName }}', '{{ pageTitle }}', '{{ pageSubtitle }}', '{{ filePath }}'],
                [$componentName, $defaultTitle, "Módulo {$defaultTitle} configurado en VeltraUI", "resources/{$subDir}/{$normalizedPath}"],
                $stubContent
            );
        } else {
            $stubFile = __DIR__ . "/../../stubs/page-{$template}.stub";
            if (! $filesystem->exists($stubFile)) {
                $stubFile = __DIR__ . '/../../stubs/page-blank.stub';
            }
            $stubContent = $filesystem->get($stubFile);

            $viewName = str_replace(['/', '\\'], '.', str_replace('.blade.php', '', $normalizedPath));
            if ($subDir !== 'views') {
                $viewName = str_replace(['/', '\\'], '.', $subDir) . '.' . $viewName;
            }

            $layoutOption = $this->option('layout');
            if ($layoutOption) {
                $layoutTag = $layoutOption === 'veltra' ? 'x-veltra::layout.app' : 'x-app-layout';
            } else {
                $hasAppLayout = $filesystem->exists(resource_path('views/layouts/app.blade.php'));
                $layoutTag = $hasAppLayout ? 'x-app-layout' : 'x-veltra::layout.app';
            }

            $renderedContent = str_replace(
                ['{{ pageTitle }}', '{{ pageSubtitle }}', '{{ viewName }}', '{{ layoutTag }}'],
                [$defaultTitle, "Módulo {$defaultTitle} configurado en VeltraUI", "resources/{$subDir}/{$normalizedPath}", $layoutTag],
                $stubContent
            );
        }

        $filesystem->ensureDirectoryExists($targetDirectory);
        $filesystem->put($targetFile, $renderedContent);

        // 6. Registrar en Menú si fue seleccionado
        $menuRegistered = false;
        if ($addToMenu && $menuTitle && $menuIcon) {
            $cleanUri = ltrim(str_replace(['/', '\\'], '/', str_replace(['.blade.php', '.tsx', '.jsx', '.vue', '.svelte'], '', $normalizedPath)), '/');
            $navUrl = $routeUri ?: '/' . $cleanUri;
            $menuRegistered = $this->registerInNavigation($filesystem, $menuTitle, $menuIcon, $navUrl, $menuSection);
        }

        // 7. Registrar Ruta en web.php si fue seleccionado
        $routeRegistered = false;
        if ($registerRoute && $routeUri) {
            $routeName = str_replace('/', '.', trim($routeUri, '/')) ?: 'home';
            if ($isInertia) {
                $routeRegistered = $this->registerInertiaRoute($filesystem, $routeUri, $inertiaComponent, $routeName);
            } else {
                $routeRegistered = $this->registerRoute($filesystem, $routeUri, $viewName, $routeName);
            }
        }

        // Resumen Visual Final
        $renderCode = $isInertia
            ? "Inertia::render('{$inertiaComponent}')"
            : "return view('{$viewName}');";

        $this->displaySuccessCard(
            "¡Módulo [{$defaultTitle}] generado exitosamente!",
            [
                'Nombre / Título' => "<fg=white;options=bold>{$defaultTitle}</>",
                'Stack Activo' => "<fg=cyan;options=bold>" . strtoupper($stack) . "</>",
                'Plantilla' => "<fg=cyan>{$this->templates[$template]['name']}</>",
                'Archivo Generado' => "<fg=yellow>resources/{$subDir}/{$normalizedPath}</>",
                'Renderizado' => "<fg=white>{$renderCode}</>",
                'Menú Lateral' => $menuRegistered ? "<fg=green>✔ Registrado en config/veltra.php [{$menuTitle}] ({$menuIcon})</>" : "<fg=gray>Omitido</>",
                'Ruta Web' => $routeRegistered ? "<fg=green>✔ [{$routeUri}] con middleware auth</>" : "<fg=gray>Omitida</>",
            ],
            'Accede a tu nuevo módulo en: ' . url($routeUri ?: '/dashboard')
        );

        return self::SUCCESS;
    }

    /**
     * Resuelve la plantilla a usar con opciones limpias (sin .. key).
     */
    protected function resolveTemplate(): string
    {
        $opt = $this->option('template');
        if ($opt && array_key_exists(strtolower($opt), $this->templates)) {
            return strtolower($opt);
        }

        if (! $this->input->isInteractive() || app()->runningUnitTests()) {
            return 'blank';
        }

        if (class_exists(\Laravel\Prompts\Prompt::class)) {
            $optionsMap = [
                '📄 Lienzo en Blanco (blank) — Workspace limpio w-full listo para construir' => 'blank',
                '📋 Tabla de Datos (table) — Búsqueda, badges, filtros y acciones rápidas' => 'table',
                '📊 Dashboard Analítico (analytics) — Tarjetas de métricas, KPIs y gráficos' => 'analytics',
                '📝 Formulario (form) — Inputs estilizados, selects, switches y botones' => 'form',
                '📌 Tablero Kanban (kanban) — Columnas de tareas (To Do, In Progress, Done)' => 'kanban',
                '🛒 Punto de Venta (pos) — Catálogo de productos y ticket de orden' => 'pos',
            ];

            $choice = select(
                label: 'Selecciona la plantilla base a generar:',
                options: array_keys($optionsMap),
                default: array_key_first($optionsMap),
                hint: 'Usa las flechas del teclado ↑/↓ y pulsa Enter.',
            );

            return $optionsMap[$choice] ?? 'blank';
        }

        return 'blank';
    }

    /**
     * Resuelve el nombre o ruta del archivo adaptado al stack.
     */
    protected function resolveName(string $stack = 'blade'): string
    {
        $arg = $this->argument('name');
        if ($arg) {
            return $arg;
        }

        $isInertia = in_array($stack, ['react', 'vue', 'svelte']);

        if (! $this->input->isInteractive() || app()->runningUnitTests()) {
            return $isInertia ? 'Admin/Orders' : 'admin.orders';
        }

        if (class_exists(\Laravel\Prompts\Prompt::class)) {
            if ($isInertia) {
                return text(
                    label: 'Nombre del componente en resources/js/Pages/:',
                    placeholder: 'Admin/Orders  o  Reports/Sales',
                    default: 'Admin/Orders',
                    required: true,
                    hint: 'Se creará en la carpeta Pages del stack ' . ucfirst($stack) . '.',
                    validate: fn (string $val) => strlen(trim($val)) > 0 ? null : 'El nombre es obligatorio.',
                );
            }

            return text(
                label: 'Nombre o ruta de la vista (en resources/views/):',
                placeholder: 'admin.orders  o  reports.sales',
                default: 'admin.orders',
                required: true,
                hint: 'Puedes usar notación de puntos (admin.orders) o barras (admin/orders).',
                validate: fn (string $val) => strlen(trim($val)) > 0 ? null : 'El nombre de la vista es obligatorio.',
            );
        }

        return $isInertia ? 'Admin/Orders' : 'admin.orders';
    }

    /**
     * Resuelve si se añade al menú.
     */
    protected function resolveMenuOption(): bool
    {
        if ($this->option('menu')) {
            return true;
        }

        if (! $this->input->isInteractive() || app()->runningUnitTests()) {
            return false;
        }

        if (class_exists(\Laravel\Prompts\Prompt::class)) {
            return confirm(
                label: '¿Deseas agregar esta página al menú lateral (Sidebar Navigation)?',
                default: true,
                hint: 'Se insertará automáticamente en config/veltra.php',
            );
        }

        return false;
    }

    /**
     * Resuelve el título del menú.
     */
    protected function resolveMenuTitle(string $defaultTitle): string
    {
        $opt = $this->option('menu-title');
        if ($opt) {
            return $opt;
        }

        if (! $this->input->isInteractive() || app()->runningUnitTests()) {
            return $defaultTitle;
        }

        if (class_exists(\Laravel\Prompts\Prompt::class)) {
            return text(
                label: 'Título a mostrar en el menú:',
                default: $defaultTitle,
                required: true,
                hint: 'Texto que leerán los usuarios en el sidebar lateral.',
            );
        }

        return $defaultTitle;
    }

    /**
     * Resuelve el icono del menú.
     */
    protected function resolveMenuIcon(string $template): string
    {
        $opt = $this->option('menu-icon');
        if ($opt) {
            return $opt;
        }

        $defaultIcon = $this->templates[$template]['icon'] ?? 'circle';

        if ($this->option('menu-title') || ! $this->input->isInteractive() || app()->runningUnitTests()) {
            return $defaultIcon;
        }

        if (class_exists(\Laravel\Prompts\Prompt::class)) {
            return text(
                label: 'Icono Lucide para el menú:',
                default: $defaultIcon,
                required: true,
                hint: 'Sugerencias: table, shopping-bag, activity, users, file-text, layers, settings',
            );
        }

        return $defaultIcon;
    }

    /**
     * Resuelve la sección del menú sin mostrar redundancias.
     */
    protected function resolveMenuSection(): string
    {
        $opt = $this->option('menu-section');
        if ($opt) {
            return $opt;
        }

        if ($this->option('menu-title') || ! $this->input->isInteractive() || app()->runningUnitTests()) {
            return 'MAIN NAVIGATION';
        }

        if (class_exists(\Laravel\Prompts\Prompt::class)) {
            $sectionsMap = [
                'MAIN NAVIGATION (Sección Principal)' => 'MAIN NAVIGATION',
                'SISTEMA & CUENTA (Sección de Sistema)' => 'SISTEMA & CUENTA',
                'Sin sección (Al final del menú)' => 'NONE',
            ];

            $choice = select(
                label: '¿Bajo qué sección del menú deseas ubicarlo?',
                options: array_keys($sectionsMap),
                default: array_key_first($sectionsMap),
                hint: 'Define el grupo visual en el sidebar.',
            );

            return $sectionsMap[$choice] ?? 'MAIN NAVIGATION';
        }

        return 'MAIN NAVIGATION';
    }

    /**
     * Resuelve si se añade a routes/web.php.
     */
    protected function resolveRouteOption(): bool
    {
        if ($this->option('route')) {
            return true;
        }

        if (! $this->input->isInteractive() || app()->runningUnitTests()) {
            return false;
        }

        if (class_exists(\Laravel\Prompts\Prompt::class)) {
            return confirm(
                label: '¿Deseas registrar automáticamente la ruta en routes/web.php?',
                default: true,
                hint: 'Creará una ruta protegida con auth::middleware.',
            );
        }

        return false;
    }

    /**
     * Resuelve la URI de la ruta.
     */
    protected function resolveRouteUri(string $defaultUri): string
    {
        $opt = $this->option('route-uri');
        if ($opt) {
            return '/' . ltrim($opt, '/');
        }

        if ($this->option('route') || ! $this->input->isInteractive() || app()->runningUnitTests()) {
            return $defaultUri;
        }

        if (class_exists(\Laravel\Prompts\Prompt::class)) {
            $uri = text(
                label: 'URI pública para acceder a la página:',
                default: $defaultUri,
                required: true,
                hint: 'Ejemplo: /admin/orders o /reports/sales',
            );
            return '/' . ltrim($uri, '/');
        }

        return $defaultUri;
    }

    /**
     * Inserta el elemento en config/veltra.php.
     */
    protected function registerInNavigation(Filesystem $filesystem, string $title, string $icon, string $url, ?string $section = null): bool
    {
        $configPath = config_path('veltra.php');
        if (! $filesystem->exists($configPath)) {
            $defaultConfig = __DIR__ . '/../../config/veltra.php';
            if ($filesystem->exists($defaultConfig)) {
                $filesystem->copy($defaultConfig, $configPath);
            } else {
                return false;
            }
        }

        $content = $filesystem->get($configPath);

        if (str_contains($content, "'title' => '{$title}'") || str_contains($content, "'url' => '{$url}'")) {
            return true;
        }

        $itemString = "        [\n" .
            "            'title' => '{$title}',\n" .
            "            'icon' => '{$icon}',\n" .
            "            'url' => '{$url}',\n" .
            "        ],\n";

        if ($section && $section !== 'NONE' && str_contains($content, "'header' => '{$section}'")) {
            $pattern = "/('header'\s*=>\s*'" . preg_quote($section, '/') . "'\s*,?\s*],)/";
            if (preg_match($pattern, $content)) {
                $newContent = preg_replace($pattern, "$1\n" . $itemString, $content, 1);
                $filesystem->put($configPath, $newContent);
                return true;
            }
        }

        if (preg_match("/('navigation'\s*=>\s*\[)(.*?)(\n\s*],)/s", $content)) {
            $newContent = preg_replace("/('navigation'\s*=>\s*\[)(.*?)(\n\s*],)/s", "$1$2" . $itemString . "$3", $content, 1);
            $filesystem->put($configPath, $newContent);
            return true;
        }

        return false;
    }

    /**
     * Registra la ruta Blade en routes/web.php.
     */
    protected function registerRoute(Filesystem $filesystem, string $uri, string $viewName, string $routeName): bool
    {
        $routesPath = base_path('routes/web.php');
        if (! $filesystem->exists($routesPath)) {
            return false;
        }

        $content = $filesystem->get($routesPath);

        if (str_contains($content, "'{$uri}'") || str_contains($content, "\"{$uri}\"")) {
            return true;
        }

        $routeDefinition = "\nRoute::get('{$uri}', function () {\n    return view('{$viewName}');\n})->middleware(['auth'])->name('{$routeName}');\n";

        $filesystem->append($routesPath, $routeDefinition);
        return true;
    }

    /**
     * Registra la ruta Inertia en routes/web.php.
     */
    protected function registerInertiaRoute(Filesystem $filesystem, string $uri, string $component, string $routeName): bool
    {
        $routesPath = base_path('routes/web.php');
        if (! $filesystem->exists($routesPath)) {
            return false;
        }

        $content = $filesystem->get($routesPath);

        if (str_contains($content, "'{$uri}'") || str_contains($content, "\"{$uri}\"")) {
            return true;
        }

        $routeDefinition = "\nRoute::inertia('{$uri}', '{$component}')->middleware(['auth'])->name('{$routeName}');\n";

        $filesystem->append($routesPath, $routeDefinition);
        return true;
    }
}
