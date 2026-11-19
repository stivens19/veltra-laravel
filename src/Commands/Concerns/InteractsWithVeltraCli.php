<?php

namespace Veltra\VeltraLaravel\Commands\Concerns;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

trait InteractsWithVeltraCli
{
    /**
     * Muestra el encabezado visual moderno y compacto de VeltraUI.
     */
    protected function displayVeltraBanner(string $subtitle, ?string $stack = null): void
    {
        $this->newLine();
        $this->line('  <fg=black;bg=cyan;options=bold>  VELTRA  </><fg=black;bg=white;options=bold> UI </><fg=cyan;options=bold> · Enterprise Suite</>');
        
        $infoLine = "  <fg=gray>{$subtitle}</>";
        if ($stack) {
            $stackLabel = match ($stack) {
                'react' => 'React + Inertia',
                'vue' => 'Vue 3 + Inertia',
                'svelte' => 'Svelte + Inertia',
                'livewire' => 'Livewire (Flux & Volt)',
                default => 'Blade Tradicional',
            };
            $infoLine .= " <fg=gray>│</> <fg=gray>Stack:</> <fg=yellow;options=bold>[{$stackLabel}]</>";
        }

        $this->line($infoLine);
        $this->newLine();
    }

    /**
     * Muestra un indicador de paso estilizado.
     */
    protected function displayStep(string $step, string $title, ?string $hint = null): void
    {
        if ($this->input->isInteractive() && ! app()->runningUnitTests()) {
            $this->line("  <fg=cyan;options=bold>◆</> <fg=black;bg=cyan;options=bold> PASO {$step} </> <fg=white;options=bold>{$title}</>");
            if ($hint) {
                $this->line("    <fg=gray>{$hint}</>");
            }
            $this->newLine();
        }
    }

    /**
     * Muestra una tarjeta de éxito final con bordes elegantes.
     */
    protected function displaySuccessCard(string $title, array $details, ?string $tip = null): void
    {
        $this->newLine();
        $this->line("  <fg=green;options=bold>✔ {$title}</>");
        $this->newLine();

        $rows = [];
        foreach ($details as $label => $value) {
            $rows[] = ["<fg=gray>{$label}</>", $value];
        }

        $this->table(['Parámetro', 'Detalle'], $rows);

        if ($tip) {
            $this->newLine();
            $this->line("  <fg=cyan;options=bold>Tip:</> <fg=gray>{$tip}</>");
        }

        $this->newLine();
    }

    /**
     * Determina el stack del proyecto (react, vue, svelte, livewire, blade).
     */
    protected function determineStack(Filesystem $filesystem): string
    {
        if ($this->hasOption('stack')) {
            $opt = $this->option('stack');
            if ($opt && in_array(strtolower($opt), ['react', 'vue', 'svelte', 'livewire', 'blade'])) {
                return strtolower($opt);
            }
        }

        // 1. Detectar Inertia desde package.json
        $packageJsonPath = base_path('package.json');
        if ($filesystem->exists($packageJsonPath)) {
            $content = $filesystem->get($packageJsonPath);
            if (str_contains($content, '@inertiajs/react')) {
                return 'react';
            }
            if (str_contains($content, '@inertiajs/vue3')) {
                return 'vue';
            }
            if (str_contains($content, '@inertiajs/svelte')) {
                return 'svelte';
            }
        }

        // 2. Detectar Livewire
        if ($filesystem->exists(base_path('vendor/livewire/livewire')) || $filesystem->exists(resource_path('views/livewire'))) {
            return 'livewire';
        }

        // 3. Detectar React/Vue por Pages
        if ($filesystem->exists(resource_path('js/Pages'))) {
            $files = $filesystem->glob(resource_path('js/Pages/*'));
            foreach ($files as $file) {
                if (Str::endsWith($file, ['.tsx', '.jsx'])) return 'react';
                if (Str::endsWith($file, '.vue')) return 'vue';
                if (Str::endsWith($file, '.svelte')) return 'svelte';
            }
        }

        return 'blade';
    }
}
