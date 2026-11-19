<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class ReferenceCommand extends Command
{
    use InteractsWithVeltraCli;
    use Concerns\HasReferenceComponentTemplates;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:reference
                            {--stack= : Stack frontend (livewire, blade, react, vue, svelte, all)}
                            {--dir= : Directorio de destino para las referencias (default: resources/veltra)}
                            {--cursor : Generar o sincronizar también con .cursorrules en la raíz del proyecto}
                            {--force : Sobrescribir archivos existentes sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera el catálogo maestro y cheatsheet anti-alucinación de VeltraUI para IAs y desarrolladores (Livewire, React, Vue, Svelte)';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $detectedStack = $this->determineStack($filesystem);
        $this->displayVeltraBanner('Catálogo Maestro & Guía Anti-Alucinación para IAs', $detectedStack);

        $stackOption = $this->option('stack');
        $stack = $stackOption ? strtolower($stackOption) : null;

        if (! $stack && $this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                $stackChoices = [
                    'Stack Activo del Proyecto (' . strtoupper($detectedStack) . ')' => $detectedStack,
                    'Todos los Stacks (Livewire, React, Vue, Svelte)' => 'all',
                    'Blade & Livewire (Flux & Volt)' => 'livewire',
                    'React + Inertia (TypeScript)' => 'react',
                    'Vue 3 + Inertia (Composition API)' => 'vue',
                    'Svelte + Inertia' => 'svelte',
                ];

                $choice = select(
                    label: '¿Para qué stack deseas generar el catálogo de referencia?',
                    options: array_keys($stackChoices),
                    default: array_keys($stackChoices)[0],
                );

                $stack = $stackChoices[$choice] ?? $detectedStack;
            }
        }

        $stack = $stack ?: $detectedStack;
        $targetDir = base_path($this->option('dir') ?: 'resources/veltra');
        $force = (bool) $this->option('force');

        $this->displayStep('1/3', 'Estructura de Directorio', "Generando catálogo en [{$targetDir}]");
        $filesystem->ensureDirectoryExists($targetDir);
        $filesystem->ensureDirectoryExists("{$targetDir}/components");

        // 1. Generar AI_REFERENCE.md (La guía principal anti-alucinación)
        $this->displayStep('2/3', 'Guía Determinista para IAs', 'Creando AI_REFERENCE.md con especificaciones estrictas');
        $aiReferencePath = "{$targetDir}/AI_REFERENCE.md";
        $filesystem->put($aiReferencePath, $this->compileAiReference($stack, $detectedStack));

        // 2. Generar catalog.json (Machine-readable)
        $catalogJsonPath = "{$targetDir}/catalog.json";
        $filesystem->put($catalogJsonPath, json_encode($this->getCatalogData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        // 3. Generar plantillas de componentes según el stack
        $this->displayStep('3/3', 'Plantillas de Componentes', 'Generando componentes de referencia en código real');
        $generatedFilesCount = $this->generateReferenceComponents($filesystem, $targetDir, $stack, $force);

        // 4. Cursor / Copilot integration
        $cursorCreated = false;
        $shouldCreateCursor = (bool) $this->option('cursor');
        if (! $shouldCreateCursor && $this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                $shouldCreateCursor = confirm(
                    label: '¿Deseas vincular esta referencia a [.cursorrules] en la raíz del proyecto para Cursor / Copilot?',
                    default: true,
                );
            }
        }

        if ($shouldCreateCursor) {
            $cursorPath = base_path('.cursorrules');
            $cursorContent = $this->compileCursorRules($stack);
            $filesystem->put($cursorPath, $cursorContent);
            $cursorCreated = true;
        }

        // Resumen
        $this->displaySuccessCard(
            '¡Catálogo Maestro de Referencia VeltraUI Generado!',
            [
                'Directorio' => "<fg=cyan>{$targetDir}</>",
                'Guía Anti-Alucinación' => "<fg=green>{$aiReferencePath}</>",
                'Catálogo JSON' => "<fg=yellow>{$catalogJsonPath}</>",
                'Stack(s) Soportado(s)' => "<fg=white;options=bold>[" . strtoupper($stack) . "]</>",
                'Componentes Base' => "<fg=cyan>{$generatedFilesCount} archivos de referencia generados</>",
                'Integración .cursorrules' => $cursorCreated ? '<fg=green>✔ Creado en la raíz del proyecto</>' : '<fg=gray>Omitido</>',
            ],
            'Las IAs y desarrolladores ahora pueden consultar resources/veltra/AI_REFERENCE.md para usar los componentes exactos sin inventar estilos.'
        );

        return self::SUCCESS;
    }

    /**
     * Genera los archivos de componentes de referencia en código real.
     */
    protected function generateReferenceComponents(Filesystem $filesystem, string $baseDir, string $stack, bool $force): int
    {
        $count = 0;
        $isAll = ($stack === 'all');
        $stacksToGenerate = $isAll ? ['blade', 'react', 'vue', 'svelte'] : [($stack === 'livewire' ? 'blade' : $stack)];

        // Si es stack específico, limpiar directorios no correspondientes para mantener el proyecto limpio
        if (! $isAll && $filesystem->exists("{$baseDir}/components")) {
            foreach (['blade', 'react', 'vue', 'svelte'] as $otherStack) {
                if ($filesystem->isDirectory("{$baseDir}/components/{$otherStack}")) {
                    $filesystem->deleteDirectory("{$baseDir}/components/{$otherStack}");
                }
            }
        }

        foreach ($stacksToGenerate as $stk) {
            $stackDir = $isAll ? "{$baseDir}/components/{$stk}" : "{$baseDir}/components";
            $filesystem->ensureDirectoryExists("{$stackDir}/atoms");
            $filesystem->ensureDirectoryExists("{$stackDir}/molecules");
            $filesystem->ensureDirectoryExists("{$stackDir}/organisms");

            // Atoms (10)
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/atoms/Button", $stk, $this->getButtonCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/atoms/Badge", $stk, $this->getBadgeCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/atoms/Input", $stk, $this->getInputCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/atoms/Textarea", $stk, $this->getTextareaCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/atoms/Select", $stk, $this->getSelectCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/atoms/Checkbox", $stk, $this->getCheckboxCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/atoms/Toggle", $stk, $this->getToggleCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/atoms/Avatar", $stk, $this->getAvatarCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/atoms/ThemeToggle", $stk, $this->getThemeToggleCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/atoms/Spinner", $stk, $this->getSpinnerCode($stk), $force);

            // Molecules (9)
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/molecules/FormField", $stk, $this->getFormFieldCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/molecules/StatCard", $stk, $this->getStatCardCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/molecules/Alert", $stk, $this->getAlertCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/molecules/SearchInput", $stk, $this->getSearchInputCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/molecules/UserPanel", $stk, $this->getUserPanelCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/molecules/Tabs", $stk, $this->getTabsCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/molecules/Breadcrumb", $stk, $this->getBreadcrumbCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/molecules/Pagination", $stk, $this->getPaginationCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/molecules/Dropdown", $stk, $this->getDropdownCode($stk), $force);

            // Organisms (4)
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/organisms/Card", $stk, $this->getCardCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/organisms/Table", $stk, $this->getTableCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/organisms/Modal", $stk, $this->getModalCode($stk), $force);
            $count += $this->writeComponentFile($filesystem, "{$stackDir}/organisms/Accordion", $stk, $this->getAccordionCode($stk), $force);
        }

        return $count;
    }

    /**
     * Escribe un archivo de componente si no existe o si force está activo.
     */
    protected function writeComponentFile(Filesystem $filesystem, string $baseName, string $stack, string $code, bool $force): int
    {
        $extension = match ($stack) {
            'react' => '.tsx',
            'vue' => '.vue',
            'svelte' => '.svelte',
            default => '.blade.php',
        };

        $filePath = "{$baseName}{$extension}";
        if (! $filesystem->exists($filePath) || $force) {
            $filesystem->put($filePath, $code);
            return 1;
        }

        return 0;
    }

    /**
     * Compila la guía maestra para IAs (AI_REFERENCE.md).
     */
    protected function compileAiReference(string $stack, string $detectedStack): string
    {
        return <<<'MARKDOWN'
# 🛡️ VELTRA UI · Enterprise AI Engineering Reference
> **Guía Determinista Anti-Alucinación para Modelos de Lenguaje (IAs) y Desarrolladores.**
> Este documento define las reglas estrictas de diseño, arquitectura de componentes, tokens CSS y sintaxis para **Laravel con VeltraUI**.

---

## ⛔ Reglas Críticas Anti-Alucinación (Stricts Prompts)

1. **PROHIBIDO inventar nombres de componentes:** Solo existen los componentes documentados en esta guía o creados bajo la convención de VeltraUI (`atoms`, `molecules`, `organisms`).
2. **COLOR PRIMARIO ESTRICTO:** El acento primario de VeltraUI es **CYAN (`#06b6d4`, Tailwind: `cyan-500` / `cyan-600`)**. NUNCA uses morado (purple/indigo), azul estándar o verde como acento de marca principal.
3. **SOPORTE TOTAL DARK MODE:** Todo componente DEBE incluir variantes oscuras:
   - Fondos: `bg-white dark:bg-slate-900` o `bg-slate-50/50 dark:bg-slate-800/40`.
   - Bordes: `border-slate-200/80 dark:border-slate-800`.
   - Textos: `text-slate-900 dark:text-white` y secundarios `text-slate-500 dark:text-slate-400`.
4. **BORDES REDONDEADOS:**
   - Tarjetas / Contenedores: `rounded-2xl`
   - Botones / Inputs / Badges: `rounded-xl`
   - Pills de estado: `rounded-full`
5. **TIPOGRAFÍA:**
   - Textos generales: `font-sans` (Plus Jakarta Sans).
   - Números, códigos, IDs y métricas: `font-mono` (JetBrains Mono).
6. **ICONOS:** Utiliza exclusivamente **Lucide Icons** (`data-lucide="nombre"` en Blade, `import { Icon } from 'lucide-react'` en React, `@lucide/vue` en Vue, `@lucide/svelte` en Svelte).

---

## 🎨 Tokens de Diseño VeltraUI

| Token | Propósito | Clase Tailwind (Modo Claro / Modo Oscuro) |
| :--- | :--- | :--- |
| **Primary Accent** | Botones principales, enlaces activos, badges | `bg-cyan-500 hover:bg-cyan-600 text-white`, `text-cyan-600 dark:text-cyan-400` |
| **Card Surface** | Superficie de paneles y widgets | `bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs` |
| **Input Surface** | Inputs y controles de formulario | `bg-white dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl focus:border-cyan-500` |
| **Table Header** | Encabezados de tablas de datos | `bg-slate-50/75 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-[11px] font-bold uppercase` |
| **Row Hover** | Estado hover en filas | `hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors` |

---

## 📦 Especificaciones Multi-Stack por Componente

### 1. Button (Átomo)
* **Props:**
  - `variant`: `'cyan'` (default), `'secondary'`, `'outline'`, `'danger'`, `'ghost'`
  - `size`: `'sm'`, `'md'` (default), `'lg'`
  - `icon`: string (Nombre de icono Lucide opcional)

```blade
<!-- BLADE / LIVEWIRE -->
<x-veltra::button variant="cyan" size="md">
    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
    <span>{{ __('Crear Registro') }}</span>
</x-veltra::button>
```

```tsx
// REACT (Inertia + TSX)
import { Button } from '@/Components/Veltra/Atoms/Button';
import { Plus } from 'lucide-react';

<Button variant="cyan" size="md" icon={Plus}>
    Crear Registro
</Button>
```

```vue
<!-- VUE 3 (Inertia + Composition API) -->
<script setup>
import Button from '@/Components/Veltra/Atoms/Button.vue';
import { Plus } from 'lucide-vue-next';
</script>

<template>
  <Button variant="cyan" size="md" :icon="Plus">
    Crear Registro
  </Button>
</template>
```

```svelte
<!-- SVELTE (Inertia) -->
<script>
  import Button from '$lib/Components/Veltra/Atoms/Button.svelte';
  import { Plus } from '@lucide/svelte';
</script>

<Button variant="cyan" size="md" icon={Plus}>
  Crear Registro
</Button>
```

---

### 2. Badge & Status Pill (Átomo)
* **Props:** `variant`: `'cyan'`, `'emerald'`, `'amber'`, `'rose'`, `'slate'`

```blade
<!-- BLADE -->
<span class="vlt-pill vlt-pill-cyan text-xs font-semibold">Activo</span>
<span class="vlt-pill vlt-pill-emerald text-xs font-semibold">Completado</span>
<span class="vlt-pill vlt-pill-amber text-xs font-semibold">Pendiente</span>
<span class="vlt-pill vlt-pill-rose text-xs font-semibold">Cancelado</span>
```

---

### 3. StatCard / KPI Widget (Molécula)
* **Props:** `title`, `value`, `icon`, `trend`, `trendUp` (boolean)

```blade
<!-- BLADE / LIVEWIRE -->
<div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-xs">
    <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Ingresos</span>
        <div class="w-9 h-9 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
            <i data-lucide="dollar-sign" class="w-5 h-5"></i>
        </div>
    </div>
    <div class="mt-3">
        <h3 class="text-2xl font-black text-slate-900 dark:text-white font-mono">$45,280.00</h3>
        <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1 font-semibold">
            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
            <span>+14.2% vs mes anterior</span>
        </p>
    </div>
</div>
```

---

### 4. Card Contenedor & Header (Organismo)
Toda vista VeltraUI sigue esta estructura:

```blade
<x-app-layout>
    <x-slot name="header">
        <section class="vlt-content-header">
            <div>
                <h1 class="vlt-page-title">{{ __('Título del Módulo') }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('Descripción breve') }}</p>
            </div>
            <nav class="vlt-breadcrumb">
                <ol class="flex items-center gap-2 text-xs">
                    <li><a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-cyan-600 font-medium">Home</a></li>
                    <li><span class="text-slate-300 dark:text-slate-600">/</span></li>
                    <li class="active font-bold text-cyan-600">Módulo</li>
                </ol>
            </nav>
        </section>
    </x-slot>

    <div class="w-full">
        <div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl shadow-xs overflow-hidden w-full">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-4">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Sección Principal</h2>
            </div>
            <div class="p-6">
                <!-- Contenido aquí -->
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## 🛠️ Comandos Artisan del Desarrollador

* `php artisan veltra:page <Nombre> --template=<blank|table|analytics|kanban|pos|form>`
* `php artisan veltra:crud <Model> --fields="campo:tipo"`
* `php artisan veltra:component <Nombre> --type=<atom|molecule|organism>`
* `php artisan veltra:menu --list`
* `php artisan veltra:theme --theme=light`
* `php artisan veltra:icons <search>`
* `php artisan veltra:build --prod` (Para pase a producción limpio)
MARKDOWN;
    }

    /**
     * Compila reglas para .cursorrules.
     */
    protected function compileCursorRules(string $stack): string
    {
        return <<<RULES
# VeltraUI Cursor & AI Rules
- Primary accent color: CYAN (#06b6d4, cyan-500/cyan-600). Do not use indigo/purple.
- Surfaces: bg-white dark:bg-slate-900 with border-slate-200/80 dark:border-slate-800.
- Cards: rounded-2xl with shadow-xs.
- Buttons and inputs: rounded-xl with cyan focus rings.
- Font: font-sans (Plus Jakarta Sans) for body, font-mono (JetBrains Mono) for numbers/IDs/metrics.
- Active Stack: {$stack}. Follow component standards in resources/veltra/AI_REFERENCE.md.
- Icons: Use Lucide icons only.
RULES;
    }
}
