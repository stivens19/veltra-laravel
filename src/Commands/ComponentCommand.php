<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class ComponentCommand extends Command
{
    use InteractsWithVeltraCli;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:component
                            {name? : Nombre del componente (ej: MetricBadge, OrderSummary, FilterDrawer)}
                            {--type= : Nivel de Atomic Design (atom, molecule, organism)}
                            {--stack= : Stack de destino (blade, react, vue, svelte; autodetectado si se omite)}
                            {--force : Sobrescribir si ya existe}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera componentes reutilizables con Atomic Design (Átomos, Moléculas, Organismos)';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $stack = $this->determineStack($filesystem);
        $this->displayVeltraBanner('Generador de Componentes (Atomic Design)', $stack);

        // 1. Tipo de Componente
        $type = $this->option('type');
        if (! $type && $this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                $typeMap = [
                    '⚛️ Átomo (Atom) — Elemento indivisible (Badge, Avatar, Button, Input)' => 'atom',
                    '🧪 Molécula (Molecule) — Unión de átomos (FormField, StatCard, NavItem)' => 'molecule',
                    '🧬 Organismo (Organism) — Sección compleja (Sidebar, Header, TableCard, Modal)' => 'organism',
                ];

                $choice = select(
                    label: '¿Qué nivel de Atomic Design tendrá el componente?',
                    options: array_keys($typeMap),
                    default: array_keys($typeMap)[1],
                );
                $type = $typeMap[$choice] ?? 'molecule';
            }
        }
        $type = in_array($type, ['atom', 'molecule', 'organism']) ? $type : 'molecule';

        // 2. Nombre del Componente
        $name = $this->argument('name');
        if (! $name && $this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                $name = text(
                    label: 'Nombre del componente (en PascalCase o kebab-case):',
                    placeholder: 'ej: StatCard  o  order-summary',
                    required: true,
                );
            }
        }
        $name = $name ?: 'CustomComponent';

        $pascalName = Str::studly($name);
        $kebabName = Str::kebab($name);
        $folderName = match ($type) {
            'atom' => 'atoms',
            'molecule' => 'molecules',
            'organism' => 'organisms',
        };

        $isInertia = in_array($stack, ['react', 'vue', 'svelte']);

        if ($isInertia) {
            $folderCap = ucfirst($folderName);
            $extension = match ($stack) {
                'react' => ($filesystem->exists(base_path('tsconfig.json')) ? '.tsx' : '.jsx'),
                'vue' => '.vue',
                'svelte' => '.svelte',
            };
            $targetDir = resource_path("js/Components/{$folderCap}");
            $targetFile = "{$targetDir}/{$pascalName}{$extension}";
            $content = $this->getInertiaTemplate($stack, $pascalName, $type);
        } else {
            $targetDir = resource_path("views/components/{$folderName}");
            $targetFile = "{$targetDir}/{$kebabName}.blade.php";
            $content = $this->getBladeTemplate($pascalName, $type);
        }

        if ($filesystem->exists($targetFile) && ! $this->option('force')) {
            $this->components->error("El componente [{$targetFile}] ya existe. Usa --force para sobrescribir.");
            return self::FAILURE;
        }

        $filesystem->ensureDirectoryExists($targetDir);
        $filesystem->put($targetFile, $content);

        // Resumen
        $usage = $isInertia
            ? "import {$pascalName} from '@/Components/{$folderCap}/{$pascalName}';"
            : "<x-{$folderName}.{$kebabName} />";

        $this->displaySuccessCard(
            "¡Componente [{$pascalName}] generado con éxito!",
            [
                'Nombre' => "<fg=white;options=bold>{$pascalName}</>",
                'Atomic Level' => "<fg=yellow>" . strtoupper($type) . "</>",
                'Stack' => "<fg=cyan>" . strtoupper($stack) . "</>",
                'Archivo' => "<fg=green>{$targetFile}</>",
                'Modo de Uso' => "<fg=gray>{$usage}</>",
            ]
        );

        return self::SUCCESS;
    }

    /**
     * Retorna plantilla para Blade.
     */
    protected function getBladeTemplate(string $name, string $type): string
    {
        return <<<BLADE
@props([
    'title' => null,
])

<div {{ \$attributes->merge(['class' => 'vlt-{$type} p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs']) }}>
    @if(\$title)
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-2">{{ \$title }}</h3>
    @endif
    {{ \$slot }}
</div>
BLADE;
    }

    /**
     * Retorna plantilla para Inertia (React, Vue, Svelte).
     */
    protected function getInertiaTemplate(string $stack, string $name, string $type): string
    {
        return match ($stack) {
            'react' => <<<REACT
import React from 'react';

interface Props {
    title?: string;
    children?: React.ReactNode;
    className?: string;
}

export default function {$name}({ title, children, className = '' }: Props) {
    return (
        <div className={`vlt-{$type} p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs \${className}`}>
            {title && <h3 className="text-sm font-bold text-slate-900 dark:text-white mb-2">{title}</h3>}
            {children}
        </div>
    );
}
REACT,
            'vue' => <<<VUE
<script setup>
defineProps({
    title: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <div class="vlt-{$type} p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
        <h3 v-if="title" class="text-sm font-bold text-slate-900 dark:text-white mb-2">{{ title }}</h3>
        <slot />
    </div>
</template>
VUE,
            'svelte' => <<<SVELTE
<script>
    export let title = null;
</script>

<div class="vlt-{$type} p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
    {#if title}
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-2">{title}</h3>
    {/if}
    <slot />
</div>
SVELTE,
        };
    }
}
