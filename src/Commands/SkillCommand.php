<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

class SkillCommand extends Command
{
    use InteractsWithVeltraCli;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:skill
                            {--force : Sobrescribir archivo de skill existente sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instala la Skill oficial de VeltraUI (.agents/skills/veltra-ui/SKILL.md) para agentes de IA';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $stack = $this->determineStack($filesystem);
        $this->displayVeltraBanner('Instalador de Skill AI para Agentes (Antigravity/Cursor/Claude)', $stack);

        // Lista de Skills oficiales del ecosistema Veltra
        $skills = [
            'veltra-ui' => 'Diseño UI & Multi-Stack',
            'veltra-security' => 'Ciberseguridad & OWASP Top 10',
            'veltra-performance' => 'Optimización Eloquent & Caché',
            'veltra-testing' => 'Pruebas Pest & PHPUnit',
        ];

        $installedFiles = [];

        foreach ($skills as $skillSlug => $skillTitle) {
            $skillDir = base_path(".agents/skills/{$skillSlug}");
            $filesystem->ensureDirectoryExists($skillDir);
            $targetSkillFile = "{$skillDir}/SKILL.md";
            $sourceSkillFile = __DIR__ . "/../../resources/skills/{$skillSlug}/SKILL.md";

            if (! $filesystem->exists($sourceSkillFile)) {
                $sourceSkillFile = __DIR__ . "/../../../.agents/skills/{$skillSlug}/SKILL.md";
            }

            if ($filesystem->exists($sourceSkillFile)) {
                $filesystem->put($targetSkillFile, $filesystem->get($sourceSkillFile));
                $installedFiles[$skillTitle] = "<fg=green>{$targetSkillFile}</>";
            } elseif ($skillSlug === 'veltra-ui') {
                $filesystem->put($targetSkillFile, $this->getFallbackSkillContent());
                $installedFiles[$skillTitle] = "<fg=green>{$targetSkillFile}</>";
            }
        }

        // Publicar plantilla de configuración MCP en .agents/mcp_config.json
        $mcpSource = __DIR__ . '/../../resources/mcp/mcp_config.json';
        $mcpTarget = base_path('.agents/mcp_config.json');
        if ($filesystem->exists($mcpSource) && (! $filesystem->exists($mcpTarget) || $this->option('force'))) {
            $filesystem->ensureDirectoryExists(dirname($mcpTarget));
            $filesystem->copy($mcpSource, $mcpTarget);
        }

        $summaryData = array_merge($installedFiles, [
            'Compatibilidad' => 'Antigravity, Cursor, Claude Code, Windsurf, Copilot',
            'Stack Detectado' => '<fg=yellow;options=bold>[' . strtoupper($stack) . ']</>',
            'Protocolo de Stacks' => 'Livewire, React (TSX), Vue 3 (SFC), Svelte',
        ]);

        $this->displaySuccessCard(
            '¡Suite de 4 Skills de Veltra Instalada con Éxito!',
            $summaryData,
            'Los agentes de IA que abran este proyecto aplicarán automáticamente diseño atómico, blindaje OWASP, optimización de base de datos y tests Pest/PHPUnit.'
        );

        return self::SUCCESS;
    }

    /**
     * Contenido de respaldo de la skill si no se encuentra el archivo fuente.
     */
    protected function getFallbackSkillContent(): string
    {
        return <<<'MARKDOWN'
---
name: veltra-ui
description: >-
  Guía de ingeniería, arquitectura de diseño y estándares de desarrollo para VeltraUI en Laravel.
  Enseña a las IAs a generar vistas, CRUDs, componentes y páginas respetando los tokens de diseño
  corporativo (Cyan #06b6d4, Slate, Dark Mode, rounded-2xl) con distinción ESTRICTA entre los 4
  stacks soportados: Livewire (Blade/Volt/Flux), React + Inertia (TSX), Vue 3 + Inertia (SFC)
  y Svelte + Inertia.
---

# 🛡️ VeltraUI: Manual de Ingeniería & Arquitectura AI-Native
MARKDOWN;
    }
}
