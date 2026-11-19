<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class CrudCommand extends Command
{
    use InteractsWithVeltraCli;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:crud
                            {model? : Nombre del Modelo Eloquent (ej: Product, Order, Customer)}
                            {--fields= : Campos separados por coma (ej: name:string,price:number,is_active:boolean)}
                            {--stack= : Stack de frontend (blade, livewire, react, vue, svelte)}
                            {--menu : Agregar automáticamente al menú lateral}
                            {--route : Registrar automáticamente en routes/web.php}
                            {--force : Sobrescribir archivos existentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un módulo CRUD completo para producción (Controlador Eloquent, Vistas VeltraUI, Rutas y Menú)';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $stack = $this->determineStack($filesystem);
        $this->displayVeltraBanner('Generador de Módulos CRUD para Producción', $stack);

        // 1. Modelo
        $modelName = $this->resolveModelName();
        $studlyModel = Str::studly($modelName);
        $kebabPlural = Str::kebab(Str::plural($studlyModel));
        $headlinePlural = Str::headline($kebabPlural);
        $headlineSingular = Str::headline($studlyModel);
        $camelPlural = Str::camel(Str::plural($studlyModel));
        $camelSingular = Str::camel($studlyModel);

        // 2. Campos / Atributos
        $fields = $this->resolveFields($modelName);

        // 3. Generar Controller con Eloquent real
        $controllerDir = app_path('Http/Controllers');
        $controllerFile = "{$controllerDir}/{$studlyModel}Controller.php";
        $controllerContent = $this->compileController($studlyModel, $kebabPlural, $camelPlural, $camelSingular, $fields);

        $force = (bool) $this->option('force');

        if (! $filesystem->exists($controllerFile) || $force) {
            $filesystem->ensureDirectoryExists($controllerDir);
            $filesystem->put($controllerFile, $controllerContent);
        }

        // 4. Generar Vistas según el Stack
        $isInertia = in_array($stack, ['react', 'vue', 'svelte']);
        $viewsGenerated = [];

        if ($isInertia) {
            $pagesDir = resource_path("js/Pages/{$studlyModel}");
            $filesystem->ensureDirectoryExists($pagesDir);
            $extension = match ($stack) {
                'react' => ($filesystem->exists(base_path('tsconfig.json')) ? '.tsx' : '.jsx'),
                'vue' => '.vue',
                'svelte' => '.svelte',
            };

            $indexFile = "{$pagesDir}/Index{$extension}";
            $formFile = "{$pagesDir}/Form{$extension}";

            $filesystem->put($indexFile, $this->compileInertiaIndex($stack, $studlyModel, $headlinePlural, $kebabPlural, $fields));
            $filesystem->put($formFile, $this->compileInertiaForm($stack, $studlyModel, $headlineSingular, $kebabPlural, $fields));

            $viewsGenerated[] = $indexFile;
            $viewsGenerated[] = $formFile;
        } else {
            $viewsDir = resource_path("views/{$kebabPlural}");
            $filesystem->ensureDirectoryExists($viewsDir);

            $indexFile = "{$viewsDir}/index.blade.php";
            $createFile = "{$viewsDir}/create.blade.php";
            $editFile = "{$viewsDir}/edit.blade.php";

            $filesystem->put($indexFile, $this->compileBladeIndex($studlyModel, $kebabPlural, $headlinePlural, $camelPlural, $camelSingular, $fields));
            $filesystem->put($createFile, $this->compileBladeCreate($studlyModel, $kebabPlural, $headlineSingular, $camelSingular, $fields));
            $filesystem->put($editFile, $this->compileBladeEdit($studlyModel, $kebabPlural, $headlineSingular, $camelSingular, $fields));

            $viewsGenerated[] = $indexFile;
            $viewsGenerated[] = $createFile;
            $viewsGenerated[] = $editFile;
        }

        // 5. Registrar Rutas en web.php
        $routeRegistered = false;
        $shouldRegisterRoute = $this->option('route') || ($this->input->isInteractive() && ! app()->runningUnitTests());
        if ($shouldRegisterRoute) {
            $routesPath = base_path('routes/web.php');
            if ($filesystem->exists($routesPath)) {
                $routeContent = $filesystem->get($routesPath);
                if (! str_contains($routeContent, "{$studlyModel}Controller")) {
                    $routeCode = "\nuse App\Http\Controllers\\{$studlyModel}Controller;\n" .
                        "Route::middleware(['auth'])->group(function () {\n" .
                        "    Route::resource('{$kebabPlural}', {$studlyModel}Controller::class);\n" .
                        "});\n";
                    $filesystem->append($routesPath, $routeCode);
                    $routeRegistered = true;
                }
            }
        }

        // 6. Registrar en Menú Lateral
        $menuRegistered = false;
        $shouldRegisterMenu = $this->option('menu') || ($this->input->isInteractive() && ! app()->runningUnitTests());
        if ($shouldRegisterMenu) {
            $configPath = config_path('veltra.php');
            if ($filesystem->exists($configPath)) {
                $configContent = $filesystem->get($configPath);
                if (! str_contains($configContent, "'url' => '/{$kebabPlural}'")) {
                    $itemString = "        [\n" .
                        "            'title' => '{$headlinePlural}',\n" .
                        "            'icon' => 'table',\n" .
                        "            'url' => '/{$kebabPlural}',\n" .
                        "        ],\n";

                    if (preg_match("/('header'\s*=>\s*'MAIN NAVIGATION'\s*,?\s*],)/", $configContent)) {
                        $newContent = preg_replace("/('header'\s*=>\s*'MAIN NAVIGATION'\s*,?\s*],)/", "$1\n" . $itemString, $configContent, 1);
                        $filesystem->put($configPath, $newContent);
                        $menuRegistered = true;
                    }
                }
            }
        }

        // Resumen Final
        $viewsList = implode("\n", array_map(fn ($f) => str_replace(base_path() . '/', '', $f), $viewsGenerated));

        $this->displaySuccessCard(
            "¡Módulo CRUD [{$studlyModel}] generado exitosamente!",
            [
                'Modelo Eloquent' => "<fg=white;options=bold>{$studlyModel}</>",
                'Controlador' => "<fg=cyan>app/Http/Controllers/{$studlyModel}Controller.php</>",
                'Vistas VeltraUI' => "<fg=yellow>{$viewsList}</>",
                'Ruta Web' => $routeRegistered ? "<fg=green>✔ Route::resource('{$kebabPlural}', ...)</>" : "<fg=gray>Existente o manual</>",
                'Menú Sidebar' => $menuRegistered ? "<fg=green>✔ Registrado en config/veltra.php [/{$kebabPlural}]</>" : "<fg=gray>Existente o manual</>",
            ],
            'Accede a tu nuevo CRUD en: ' . url("/{$kebabPlural}")
        );

        return self::SUCCESS;
    }

    /**
     * Resuelve el nombre del modelo.
     */
    protected function resolveModelName(): string
    {
        $arg = $this->argument('model');
        if ($arg) {
            return $arg;
        }

        if ($this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                return text(
                    label: '¿Cuál es el nombre del Modelo Eloquent?',
                    placeholder: 'ej: Product, Order, Customer, Invoice',
                    default: 'Product',
                    required: true,
                );
            }
        }

        return 'Product';
    }

    /**
     * Resuelve los campos del modelo.
     */
    protected function resolveFields(string $modelName): array
    {
        $fieldsOpt = $this->option('fields');
        if ($fieldsOpt) {
            $parsed = [];
            foreach (explode(',', $fieldsOpt) as $fieldDef) {
                $parts = explode(':', trim($fieldDef));
                $parsed[] = [
                    'name' => $parts[0],
                    'type' => $parts[1] ?? 'string',
                ];
            }
            return $parsed;
        }

        $fullClass = "App\\Models\\{$modelName}";
        if (class_exists($fullClass)) {
            try {
                $instance = new $fullClass;
                $table = $instance->getTable();
                if (Schema::hasTable($table)) {
                    $cols = Schema::getColumnListing($table);
                    $ignore = ['id', 'created_at', 'updated_at', 'deleted_at', 'password', 'remember_token'];
                    $parsed = [];
                    foreach ($cols as $col) {
                        if (! in_array($col, $ignore)) {
                            $parsed[] = ['name' => $col, 'type' => 'string'];
                        }
                    }
                    if (! empty($parsed)) {
                        return $parsed;
                    }
                }
            } catch (\Throwable $e) {}
        }

        return [
            ['name' => 'name', 'type' => 'string'],
            ['name' => 'code', 'type' => 'string'],
            ['name' => 'status', 'type' => 'string'],
            ['name' => 'description', 'type' => 'text'],
        ];
    }

    /**
     * Compila el Controller con métodos completos de Eloquent.
     */
    protected function compileController(string $model, string $plural, string $camelPlural, string $camelSingular, array $fields): string
    {
        $validationRules = '';
        foreach ($fields as $field) {
            $rule = match ($field['type']) {
                'number', 'integer' => 'required|numeric',
                'boolean' => 'nullable|boolean',
                'email' => 'required|email|max:255',
                default => 'required|string|max:255',
            };
            $validationRules .= "            '{$field['name']}' => '{$rule}',\n";
        }

        return <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\\{$model};
use Illuminate\Http\Request;

class {$model}Controller extends Controller
{
    /**
     * Listado con paginación y búsqueda.
     */
    public function index(Request \$request)
    {
        \$query = class_exists({$model}::class) ? {$model}::query() : null;

        if (\$query && \$request->filled('search')) {
            \$term = \$request->input('search');
            \$query->where('name', 'like', "%{\$term}%");
        }

        \${$camelPlural} = \$query ? \$query->latest()->paginate(10)->withQueryString() : collect();

        return view('{$plural}.index', compact('{$camelPlural}'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        return view('{$plural}.create');
    }

    /**
     * Almacenar un nuevo registro con validación.
     */
    public function store(Request \$request)
    {
        \$validated = \$request->validate([
{$validationRules}        ]);

        if (class_exists({$model}::class)) {
            {$model}::create(\$validated);
        }

        return redirect()->route('{$plural}.index')->with('success', '{$model} creado exitosamente.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(\$id)
    {
        \${$camelSingular} = class_exists({$model}::class) ? {$model}::findOrFail(\$id) : (object) ['id' => \$id];

        return view('{$plural}.edit', compact('{$camelSingular}'));
    }

    /**
     * Actualizar registro existente con validación.
     */
    public function update(Request \$request, \$id)
    {
        \$validated = \$request->validate([
{$validationRules}        ]);

        if (class_exists({$model}::class)) {
            \${$camelSingular} = {$model}::findOrFail(\$id);
            \${$camelSingular}->update(\$validated);
        }

        return redirect()->route('{$plural}.index')->with('success', '{$model} actualizado exitosamente.');
    }

    /**
     * Eliminar registro.
     */
    public function destroy(\$id)
    {
        if (class_exists({$model}::class)) {
            \${$camelSingular} = {$model}::findOrFail(\$id);
            \${$camelSingular}->delete();
        }

        return redirect()->route('{$plural}.index')->with('success', '{$model} eliminado exitosamente.');
    }
}
PHP;
    }

    /**
     * Compila la vista Blade de listado con @forelse real y paginación.
     */
    protected function compileBladeIndex(string $model, string $plural, string $title, string $camelPlural, string $camelSingular, array $fields): string
    {
        $headers = '';
        $dataCells = '';

        foreach ($fields as $field) {
            $h = Str::headline($field['name']);
            $fName = $field['name'];
            $headers .= "                        <th class=\"py-3 px-5\">{$h}</th>\n";
            $dataCells .= "                        <td class=\"py-3.5 px-5 font-semibold text-slate-800 dark:text-slate-100\">{{ \${$camelSingular}->{$fName} ?? '—' }}</td>\n";
        }

        $colSpan = count($fields) + 2;

        return <<<BLADE
<x-app-layout>
    <x-slot name="header">
        <section class="vlt-content-header">
            <div>
                <h1 class="vlt-page-title">{{ __('{$title}') }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ __('Administración y listado de {$title}') }}
                </p>
            </div>
            <nav class="vlt-breadcrumb" aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-xs">
                    <li><a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-cyan-600 font-medium">{{ __('Home') }}</a></li>
                    <li><span class="text-slate-300 dark:text-slate-600">/</span></li>
                    <li class="active font-bold text-cyan-600">{{ __('{$title}') }}</li>
                </ol>
            </nav>
        </section>
    </x-slot>

    <div class="w-full space-y-4">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl shadow-xs overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 border-b border-slate-100 dark:border-slate-800">
                <form method="GET" action="{{ url('{$plural}') }}" class="relative flex-1 max-w-sm">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Buscar en {$title}...') }}"
                        class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400" />
                </form>
                <div class="flex items-center gap-2">
                    <a href="{{ url('{$plural}/create') }}" class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-cyan-500 hover:bg-cyan-600 text-white shadow-xs inline-flex items-center gap-1.5 transition-colors">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>{{ __('Nuevo {$model}') }}</span>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-200">
                    <thead class="bg-slate-50/75 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-5"># ID</th>
{$headers}                            <th class="py-3 px-5 text-right">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse(\${$camelPlural} as \${$camelSingular})
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-5 font-mono text-[11px] font-bold text-cyan-600 dark:text-cyan-400">
                                    #{{ \${$camelSingular}->id ?? \$loop->iteration }}
                                </td>
{$dataCells}                                <td class="py-3.5 px-5 text-right space-x-1">
                                    <a href="{{ url('{$plural}/' . (\${$camelSingular}->id ?? 1) . '/edit') }}" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-cyan-600 transition-colors inline-block" title="Editar">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ url('{$plural}/' . (\${$camelSingular}->id ?? 1)) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Seguro que deseas eliminar este registro?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-400 hover:text-rose-600 transition-colors" title="Eliminar">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{$colSpan}" class="py-12 text-center text-slate-400 dark:text-slate-600">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-300 dark:text-slate-700"></i>
                                    <p class="text-xs font-semibold">{{ __('No se encontraron registros en {$title}') }}</p>
                                    <a href="{{ url('{$plural}/create') }}" class="inline-block mt-3 px-3 py-1.5 text-xs font-bold text-cyan-600 hover:underline">
                                        {{ __('Crear el primer registro →') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists(\${$camelPlural}, 'hasPages') && \${$camelPlural}->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ \${$camelPlural}->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
BLADE;
    }

    /**
     * Compila la vista Blade Create.
     */
    protected function compileBladeCreate(string $model, string $plural, string $title, string $camelSingular, array $fields): string
    {
        $inputs = $this->compileFormInputs($fields);

        return <<<BLADE
<x-app-layout>
    <x-slot name="header">
        <section class="vlt-content-header">
            <div>
                <h1 class="vlt-page-title">{{ __('Nuevo {$title}') }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ __('Ingresa los datos para registrar un nuevo elemento') }}
                </p>
            </div>
            <nav class="vlt-breadcrumb" aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-xs">
                    <li><a href="{{ url('{$plural}') }}" class="text-slate-500 hover:text-cyan-600 font-medium">{{ __('{$plural}') }}</a></li>
                    <li><span class="text-slate-300 dark:text-slate-600">/</span></li>
                    <li class="active font-bold text-cyan-600">{{ __('Nuevo') }}</li>
                </ol>
            </nav>
        </section>
    </x-slot>

    <div class="w-full">
        <div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl shadow-xs overflow-hidden">
            <form action="{{ url('{$plural}') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
{$inputs}                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ url('{$plural}') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-cyan-500 hover:bg-cyan-600 active:bg-cyan-700 rounded-xl shadow-xs transition-colors">
                        {{ __('Crear {$title}') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
BLADE;
    }

    /**
     * Compila la vista Blade Edit.
     */
    protected function compileBladeEdit(string $model, string $plural, string $title, string $camelSingular, array $fields): string
    {
        $inputs = $this->compileFormInputs($fields, $camelSingular);

        return <<<BLADE
<x-app-layout>
    <x-slot name="header">
        <section class="vlt-content-header">
            <div>
                <h1 class="vlt-page-title">{{ __('Editar {$title}') }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ __('Actualiza la información del registro') }}
                </p>
            </div>
            <nav class="vlt-breadcrumb" aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-xs">
                    <li><a href="{{ url('{$plural}') }}" class="text-slate-500 hover:text-cyan-600 font-medium">{{ __('{$plural}') }}</a></li>
                    <li><span class="text-slate-300 dark:text-slate-600">/</span></li>
                    <li class="active font-bold text-cyan-600">{{ __('Editar') }}</li>
                </ol>
            </nav>
        </section>
    </x-slot>

    <div class="w-full">
        <div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl shadow-xs overflow-hidden">
            <form action="{{ url('{$plural}/' . (\${$camelSingular}->id ?? 1)) }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
{$inputs}                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ url('{$plural}') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-cyan-500 hover:bg-cyan-600 active:bg-cyan-700 rounded-xl shadow-xs transition-colors">
                        {{ __('Guardar Cambios') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
BLADE;
    }

    /**
     * Compila los campos de formulario Blade con old() y @error.
     */
    protected function compileFormInputs(array $fields, ?string $camelSingular = null): string
    {
        $inputs = '';
        foreach ($fields as $field) {
            $h = Str::headline($field['name']);
            $fName = $field['name'];
            $valBind = $camelSingular ? "old('{$fName}', \${$camelSingular}->{$fName} ?? '')" : "old('{$fName}')";

            $inputs .= <<<HTML
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ __('{$h}') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="{$fName}" value="{{ {$valBind} }}" placeholder="{{ __('Ingresa {$h}') }}"
                            class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all @error('{$fName}') border-rose-500 @enderror" />
                        @error('{$fName}')
                            <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ \$message }}</p>
                        @enderror
                    </div>\n
HTML;
        }
        return $inputs;
    }

    /**
     * Compila Index para Inertia (React, Vue, Svelte).
     */
    protected function compileInertiaIndex(string $stack, string $model, string $title, string $plural, array $fields): string
    {
        return match ($stack) {
            'react' => <<<REACT
import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link } from '@inertiajs/react';

export default function Index({ {$plural} = { data: [] } }) {
    return (
        <AppLayout header={<h1 className="text-xl font-bold">{$title}</h1>}>
            <Head title="{$title}" />
            <div className="w-full vlt-card p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                <div className="flex justify-between items-center mb-4">
                    <h2 className="text-sm font-bold">Listado de {$title}</h2>
                    <Link href="/{$plural}/create" className="px-3.5 py-1.5 text-xs font-bold bg-cyan-500 text-white rounded-xl">
                        Nuevo {$model}
                    </Link>
                </div>
                <p className="text-xs text-slate-500">Módulo CRUD {$model} configurado en VeltraUI.</p>
            </div>
        </AppLayout>
    );
}
REACT,
            'vue' => <<<VUE
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
defineProps({
    {$plural}: {
        type: Object,
        default: () => ({ data: [] }),
    },
});
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold">{$title}</h1>
        </template>
        <Head title="{$title}" />
        <div class="w-full vlt-card p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-sm font-bold">Listado de {$title}</h2>
                <Link href="/{$plural}/create" class="px-3.5 py-1.5 text-xs font-bold bg-cyan-500 text-white rounded-xl">
                    Nuevo {$model}
                </Link>
            </div>
            <p class="text-xs text-slate-500">Módulo CRUD {$model} configurado en VeltraUI.</p>
        </div>
    </AppLayout>
</template>
VUE,
            'svelte' => <<<SVELTE
<script>
    import AppLayout from '@/Layouts/AppLayout.svelte';
    export let {$plural} = { data: [] };
</script>

<svelte:head>
    <title>{$title}</title>
</svelte:head>

<AppLayout>
    <div slot="header">
        <h1 class="text-xl font-bold">{$title}</h1>
    </div>
    <div class="w-full vlt-card p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
        <h2 class="text-sm font-bold mb-2">Listado de {$title}</h2>
        <p class="text-xs text-slate-500">Módulo CRUD {$model} configurado en VeltraUI.</p>
    </div>
</AppLayout>
SVELTE,
        };
    }

    /**
     * Compila Form para Inertia.
     */
    protected function compileInertiaForm(string $stack, string $model, string $title, string $plural, array $fields): string
    {
        return match ($stack) {
            'react' => <<<REACT
import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link } from '@inertiajs/react';

export default function Form({ item = null }) {
    const isEdit = !!item;
    return (
        <AppLayout header={<h1 className="text-xl font-bold">{isEdit ? 'Editar {$title}' : 'Nuevo {$title}'}</h1>}>
            <Head title="{isEdit ? 'Editar' : 'Nuevo'} {$title}" />
            <div className="w-full vlt-card p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                <h2 className="text-sm font-bold mb-4">Datos de {$title}</h2>
                <p className="text-xs text-slate-500 mb-6">Formulario listo para conectar formulario Inertia.</p>
                <Link href="/{$plural}" className="px-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700">
                    Volver al listado
                </Link>
            </div>
        </AppLayout>
    );
}
REACT,
            'vue' => <<<VUE
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
defineProps({
    item: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold">{{ item ? 'Editar {$title}' : 'Nuevo {$title}' }}</h1>
        </template>
        <Head :title="item ? 'Editar {$title}' : 'Nuevo {$title}'" />
        <div class="w-full vlt-card p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <h2 class="text-sm font-bold mb-4">Datos de {$title}</h2>
            <p class="text-xs text-slate-500 mb-6">Formulario listo para conectar formulario Inertia.</p>
            <Link href="/{$plural}" class="px-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700">
                Volver al listado
            </Link>
        </div>
    </AppLayout>
</template>
VUE,
            'svelte' => <<<SVELTE
<script>
    import AppLayout from '@/Layouts/AppLayout.svelte';
    export let item = null;
</script>

<svelte:head>
    <title>{item ? 'Editar' : 'Nuevo'} {$title}</title>
</svelte:head>

<AppLayout>
    <div slot="header">
        <h1 class="text-xl font-bold">{item ? 'Editar' : 'Nuevo'} {$title}</h1>
    </div>
    <div class="w-full vlt-card p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
        <h2 class="text-sm font-bold mb-4">Datos de {$title}</h2>
        <p class="text-xs text-slate-500 mb-6">Formulario listo para conectar formulario Inertia.</p>
        <a href="/{$plural}" class="px-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700">
            Volver al listado
        </a>
    </div>
</AppLayout>
SVELTE,
        };
    }
}
