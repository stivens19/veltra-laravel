<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:install
                            {--stack= : El tipo de stack del proyecto (none, livewire, blade, react, vue, svelte)}
                            {--views : Publicar todas las vistas y componentes Blade en resources/views/vendor/veltra}
                            {--no-build : Omitir compilación automática con npm run build}
                            {--force : Sobrescribir archivos existentes sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instala y configura la suite VeltraUI adaptándose a proyectos limpios o con Starter Kits (Blade, Livewire, React, Vue, Svelte)';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $this->displayBanner();

        $force = (bool) $this->option('force');

        // 1. Detectar o preguntar qué Starter Kit tiene el proyecto
        $stack = $this->determineStack($filesystem);

        $this->components->info("Configurando VeltraUI para stack: [{$stack}]");

        // 2. Publicar Configuración, Assets y Skill AI
        $this->info('  [1/5] Publicando configuración, assets compilados y Skill AI...');
        $this->call('vendor:publish', [
            '--tag' => 'veltra-config',
            '--force' => $force,
        ]);
        $this->call('vendor:publish', [
            '--tag' => 'veltra-assets',
            '--force' => $force,
        ]);
        $this->call('vendor:publish', [
            '--tag' => 'veltra-skill',
            '--force' => $force,
        ]);
        $this->call('vendor:publish', [
            '--tag' => 'veltra-errors',
            '--force' => $force,
        ]);

        // 3. Configurar Layouts y Dashboard según el Stack
        $this->info('  [2/5] Configurando layouts y dashboard limpio...');
        $replaced = $this->setupLayoutsAndDashboard($filesystem, $stack, $force);

        // 4. Si es stack 'none', configurar autenticación completa
        if ($stack === 'none') {
            $this->info('  [3/5] Implementando autenticación completa en /login y /register...');
            $this->call('veltra:auth', ['--force' => $force || $replaced]);
            $this->ensureDashboardRoute($filesystem);
        } elseif (in_array($stack, ['react', 'vue', 'svelte'])) {
            $this->info('  [3/5] Adaptando layouts de Inertia (' . ucfirst($stack) . ')...');
        } else {
            $this->info('  [3/5] Adaptando autenticación existente del Starter Kit...');
            $this->adaptStarterKitAuth($filesystem, $force || $replaced);
        }

        // 5. Configurar Tailwind CSS y Vite
        $this->info('  [4/5] Configurando Tailwind CSS y Vite...');
        $this->setupTailwindAndVite($filesystem);

        // 6. Opcional: Publicar vistas Blade
        $publishViews = (bool) $this->option('views');
        if (! $force && ! $publishViews && $this->input->isInteractive() && function_exists('Laravel\Prompts\confirm')) {
            $publishViews = confirm(
                label: '¿Deseas publicar las vistas y componentes Blade para editarlos directamente?',
                default: false,
                yes: 'Sí, publicar vistas',
                no: 'No, usar las del paquete (Recomendado)'
            );
        }

        if ($publishViews) {
            $this->info('  [5/5] Publicando vistas Blade...');
            $this->call('vendor:publish', [
                '--tag' => 'veltra-views',
                '--force' => $force,
            ]);
        } else {
            $this->line('  [5/5] Vistas Blade encapsuladas.');
        }

        // 7. Compilar Vite si no se especificó --no-build
        if (! $this->option('no-build')) {
            $this->buildAssets();
        }

        $this->displaySuccess($stack);

        return self::SUCCESS;
    }

    /**
     * Determine project stack (none, livewire, blade, react, vue, svelte).
     */
    protected function determineStack(Filesystem $filesystem): string
    {
        $stack = $this->option('stack');
        if (in_array($stack, ['none', 'livewire', 'blade', 'react', 'vue', 'svelte'])) {
            return $stack;
        }

        // Detección automática inteligente
        $detectedHasStarterKit = false;
        $detectedStack = 'livewire';

        // Check package.json for Inertia
        $packageJsonPath = base_path('package.json');
        if ($filesystem->exists($packageJsonPath)) {
            $packageJson = $filesystem->get($packageJsonPath);
            if (str_contains($packageJson, '@inertiajs/react')) {
                $detectedHasStarterKit = true;
                $detectedStack = 'react';
            } elseif (str_contains($packageJson, '@inertiajs/vue3')) {
                $detectedHasStarterKit = true;
                $detectedStack = 'vue';
            } elseif (str_contains($packageJson, '@inertiajs/svelte')) {
                $detectedHasStarterKit = true;
                $detectedStack = 'svelte';
            }
        }

        if (! $detectedHasStarterKit) {
            if ($filesystem->exists(resource_path('views/livewire')) || $filesystem->exists(base_path('vendor/livewire/livewire'))) {
                $detectedHasStarterKit = true;
                $detectedStack = 'livewire';
            } elseif ($filesystem->exists(resource_path('views/dashboard.blade.php')) || $filesystem->exists(resource_path('views/auth'))) {
                $detectedHasStarterKit = true;
                $detectedStack = 'blade';
            } elseif ($filesystem->exists(resource_path('js/Pages')) || $filesystem->exists(resource_path('js/pages'))) {
                $detectedHasStarterKit = true;
                $detectedStack = 'react';
            }
        }

        if ($this->input->isInteractive() && function_exists('Laravel\Prompts\confirm') && function_exists('Laravel\Prompts\select')) {
            // Pregunta 1: ¿Usó un Starter Kit? (tal como 'laravel new')
            $usedStarterKit = confirm(
                label: '¿Tu proyecto fue creado usando un Starter Kit (Breeze / Jetstream)?',
                default: $detectedHasStarterKit,
                yes: 'Sí, tiene un Starter Kit instalado',
                no: 'No, es una instalación limpia de Laravel (Sin Starter Kit)'
            );

            if (! $usedStarterKit) {
                return 'none';
            }

            // Pregunta 2: ¿Cuál stack de Starter Kit seleccionó?
            return select(
                label: '¿Cuál stack de Starter Kit tiene tu proyecto?',
                options: [
                    'react' => 'React + Inertia (Breeze — Adapta Layouts y Dashboard limpio VeltraUI)',
                    'vue' => 'Vue 3 + Inertia (Breeze — Adapta Layouts y Dashboard limpio VeltraUI)',
                    'svelte' => 'Svelte + Inertia (Breeze — Adapta Layouts y Dashboard limpio VeltraUI)',
                    'livewire' => 'Livewire (Breeze / Volt — Adapta Layouts y Dashboard limpio VeltraUI)',
                    'blade' => 'Blade tradicional (Breeze — Adapta Layouts y Dashboard limpio VeltraUI)',
                ],
                default: $detectedStack
            );
        }

        return $detectedHasStarterKit ? $detectedStack : 'none';
    }

    /**
     * Setup layouts and clean dashboard according to stack.
     */
    protected function setupLayoutsAndDashboard(Filesystem $filesystem, string $stack, bool $force): bool
    {
        if (in_array($stack, ['react', 'vue', 'svelte'])) {
            return $this->setupInertiaLayoutsAndDashboard($filesystem, $stack, $force);
        }

        $viewsDir = resource_path('views');
        $layoutsDir = $viewsDir . '/layouts';
        $filesystem->ensureDirectoryExists($layoutsDir);

        $dashboardTarget = $viewsDir . '/dashboard.blade.php';
        $appTarget = $layoutsDir . '/app.blade.php';
        $shouldReplace = $force;

        // Si ya existen dashboard o layouts de un Starter Kit y no se pasó --force
        if (! $shouldReplace && ($filesystem->exists($dashboardTarget) || $filesystem->exists($appTarget))) {
            if ($this->input->isInteractive() && function_exists('Laravel\Prompts\confirm')) {
                $shouldReplace = confirm(
                    label: 'Se detectó un Dashboard o Layouts previos del Starter Kit. ¿Deseas reemplazarlos con el diseño limpio de VeltraUI?',
                    default: true
                );
            } else {
                // En modo no interactivo con stack definido, se asume el reemplazo
                $shouldReplace = true;
            }
        }

        // 1. Dashboard Limpio
        $dashboardStub = __DIR__ . '/../../stubs/dashboard/dashboard-clean.stub';
        if (! $filesystem->exists($dashboardTarget) || $shouldReplace) {
            if ($filesystem->exists($dashboardStub)) {
                $filesystem->copy($dashboardStub, $dashboardTarget);
                $this->line('    - <fg=green>Dashboard limpio de VeltraUI creado:</fg=green> resources/views/dashboard.blade.php');
            }
        }

        // 2. Layouts app.blade.php y guest.blade.php
        $guestTarget = $layoutsDir . '/guest.blade.php';
        $appStub = __DIR__ . '/../../stubs/breeze/app.stub';
        $guestStub = __DIR__ . '/../../stubs/breeze/guest.stub';

        if (! $filesystem->exists($appTarget) || $shouldReplace) {
            if ($filesystem->exists($appStub)) {
                $filesystem->copy($appStub, $appTarget);
                $this->line('    - <fg=green>Layout App VeltraUI:</fg=green> resources/views/layouts/app.blade.php');
            }
        }

        if (! $filesystem->exists($guestTarget) || $shouldReplace) {
            if ($filesystem->exists($guestStub)) {
                $filesystem->copy($guestStub, $guestTarget);
                $this->line('    - <fg=green>Layout Guest VeltraUI:</fg=green> resources/views/layouts/guest.blade.php');
            }
        }

        // 3. Clases de componentes AppLayout y GuestLayout para soporte total de <x-app-layout> y <x-guest-layout>
        $componentsDir = app_path('View/Components');
        $filesystem->ensureDirectoryExists($componentsDir);

        $appLayoutClass = $componentsDir . '/AppLayout.php';
        if (! $filesystem->exists($appLayoutClass) || $shouldReplace) {
            $filesystem->put($appLayoutClass, <<<'PHP'
<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function render(): View
    {
        return view('layouts.app');
    }
}
PHP
            );
        }

        $guestLayoutClass = $componentsDir . '/GuestLayout.php';
        if (! $filesystem->exists($guestLayoutClass) || $shouldReplace) {
            $filesystem->put($guestLayoutClass, <<<'PHP'
<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public function render(): View
    {
        return view('layouts.guest');
    }
}
PHP
            );
        }

        // 4. Componentes Atomic Design para Blade (atoms, molecules, organisms)
        $bladeComponentsDir = resource_path('views/components');
        foreach (['atoms', 'molecules', 'organisms'] as $level) {
            $sourceLevelDir = __DIR__ . '/../../resources/views/components/' . $level;
            $targetLevelDir = $bladeComponentsDir . '/' . $level;
            $filesystem->ensureDirectoryExists($targetLevelDir);

            if ($filesystem->isDirectory($sourceLevelDir)) {
                $files = $filesystem->files($sourceLevelDir);
                foreach ($files as $file) {
                    $targetFile = $targetLevelDir . '/' . $file->getFilename();
                    if (! $filesystem->exists($targetFile) || $shouldReplace) {
                        $filesystem->copy($file->getPathname(), $targetFile);
                    }
                }
            }
        }
        $this->line('    - <fg=green>Componentes Atomic Design Blade:</fg=green> resources/views/components/ (atoms, molecules, organisms)');

        return $shouldReplace;
    }

    /**
     * Setup Inertia layouts and clean dashboard for React, Vue, or Svelte.
     */
    protected function setupInertiaLayoutsAndDashboard(Filesystem $filesystem, string $stack, bool $force): bool
    {
        $jsDir = resource_path('js');
        $filesystem->ensureDirectoryExists($jsDir);

        // Detect directory casing (Pages vs pages, Layouts vs layouts, Components vs components)
        $pagesDir = $filesystem->isDirectory($jsDir . '/pages') ? $jsDir . '/pages' : $jsDir . '/Pages';
        $filesystem->ensureDirectoryExists($pagesDir);

        $layoutsDir = $filesystem->isDirectory($jsDir . '/layouts') ? $jsDir . '/layouts' : $jsDir . '/Layouts';
        $filesystem->ensureDirectoryExists($layoutsDir);

        $componentsDir = $filesystem->isDirectory($jsDir . '/components') ? $jsDir . '/components' : $jsDir . '/Components';
        $filesystem->ensureDirectoryExists($componentsDir);

        $isTs = $filesystem->exists(base_path('tsconfig.json'))
            || $filesystem->exists($jsDir . '/app.tsx')
            || $filesystem->exists($jsDir . '/app.ts');

        $ext = match ($stack) {
            'react' => $isTs ? 'tsx' : 'jsx',
            'vue' => 'vue',
            'svelte' => 'svelte',
            default => 'jsx',
        };

        $dashboardTarget = $filesystem->exists($pagesDir . "/dashboard.{$ext}")
            ? $pagesDir . "/dashboard.{$ext}"
            : $pagesDir . "/Dashboard.{$ext}";

        $appLayoutTarget = $layoutsDir . "/AppLayout.{$ext}";
        $kebabAppLayoutTarget = $layoutsDir . "/app-layout.{$ext}";
        $authLayoutTarget = $layoutsDir . "/AuthenticatedLayout.{$ext}";
        $guestLayoutTarget = $layoutsDir . "/GuestLayout.{$ext}";
        $kebabAuthLayoutTarget = $layoutsDir . "/auth-layout.{$ext}";
        $pascalAuthLayoutTarget = $layoutsDir . "/AuthLayout.{$ext}";

        $shouldReplace = $force;
        if (! $shouldReplace && ($filesystem->exists($dashboardTarget) || $filesystem->exists($appLayoutTarget) || $filesystem->exists($kebabAppLayoutTarget) || $filesystem->exists($authLayoutTarget))) {
            if ($this->input->isInteractive() && function_exists('Laravel\Prompts\confirm')) {
                $shouldReplace = confirm(
                    label: "Se detectó un Dashboard o Layouts previos de Inertia ({$stack}). ¿Deseas reemplazarlos con el diseño limpio de VeltraUI?",
                    default: true
                );
            } else {
                $shouldReplace = true;
            }
        }

        $stubDir = __DIR__ . "/../../stubs/inertia/{$stack}";

        $componentsBase = basename($componentsDir);
        $layoutsBase = basename($layoutsDir);

        // 0. Ensure Lucide icon package exists in package.json
        $this->ensureNpmDependencies($filesystem, $stack);

        // 1. AppLayout, AuthenticatedLayout & app-layout (Starter Kits)
        $appLayoutStub = $stubDir . '/AppLayout.stub';
        if ($filesystem->exists($appLayoutStub)) {
            $layoutContent = $filesystem->get($appLayoutStub);
            $layoutContent = str_replace(['@/Components/', '@/components/'], "@/{$componentsBase}/", $layoutContent);
            if (! $filesystem->exists($appLayoutTarget) || $shouldReplace) {
                $filesystem->put($appLayoutTarget, $layoutContent);
                $this->line("    - <fg=green>Layout App VeltraUI ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $appLayoutTarget));
            }
            if (! $filesystem->exists($authLayoutTarget) || $shouldReplace) {
                $filesystem->put($authLayoutTarget, $layoutContent);
                $this->line("    - <fg=green>Layout Authenticated VeltraUI ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $authLayoutTarget));
            }
            if ($filesystem->exists($kebabAppLayoutTarget) && $shouldReplace) {
                $filesystem->put($kebabAppLayoutTarget, $layoutContent);
                $this->line("    - <fg=green>Layout Starter Kit app-layout adaptado ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $kebabAppLayoutTarget));
            }
        }

        // 2. GuestLayout & auth-layout
        $guestLayoutStub = $stubDir . '/GuestLayout.stub';
        if ($filesystem->exists($guestLayoutStub)) {
            $guestLayoutContent = $filesystem->get($guestLayoutStub);
            $guestLayoutContent = str_replace(
                ['./Components/Atoms/ThemeToggle', '@/Components/Atoms/ThemeToggle', '@/components/Atoms/ThemeToggle'],
                "@/{$componentsBase}/Atoms/ThemeToggle",
                $guestLayoutContent
            );
            if (! $filesystem->exists($guestLayoutTarget) || $shouldReplace) {
                $filesystem->put($guestLayoutTarget, $guestLayoutContent);
                $this->line("    - <fg=green>Layout Guest VeltraUI ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $guestLayoutTarget));
            }
            $authLayoutContent = match ($stack) {
                'react' => <<<TSX
import GuestLayout from '@/{$layoutsBase}/GuestLayout';

export default function AuthLayout({ title = '', description = '', children }: any) {
    return (
        <GuestLayout title={title} description={description}>
            {children}
        </GuestLayout>
    );
}
TSX,
                'vue' => <<<VUE
<script setup>
import GuestLayout from '@/{$layoutsBase}/GuestLayout.vue';

defineProps({
    title: String,
    description: String,
});
</script>

<template>
    <GuestLayout :title="title" :description="description">
        <slot />
    </GuestLayout>
</template>
VUE,
                default => <<<SVELTE
<script>
    import GuestLayout from '@/{$layoutsBase}/GuestLayout.svelte';

    export let title = '';
    export let description = '';
</script>

<GuestLayout {title} {description}>
    <slot />
</GuestLayout>
SVELTE,
            };

            if ($filesystem->exists($kebabAuthLayoutTarget) && $shouldReplace) {
                $filesystem->put($kebabAuthLayoutTarget, $authLayoutContent);
                $this->line("    - <fg=green>Layout Starter Kit auth-layout adaptado ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $kebabAuthLayoutTarget));
            }
            if ($filesystem->exists($pascalAuthLayoutTarget) && $shouldReplace) {
                $filesystem->put($pascalAuthLayoutTarget, $authLayoutContent);
                $this->line("    - <fg=green>Layout Starter Kit AuthLayout adaptado ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $pascalAuthLayoutTarget));
            }
        }

        // 2b. Starter Kit auth variants (auth-simple-layout, auth-card-layout, auth-split-layout, etc.)
        $authSubDir = $layoutsDir . '/auth';
        if ($filesystem->isDirectory($authSubDir) && $shouldReplace) {
            $authVariants = [
                'auth-simple-layout', 'auth-card-layout', 'auth-split-layout',
                'AuthSimpleLayout', 'AuthCardLayout', 'AuthSplitLayout',
            ];
            foreach ($authVariants as $variant) {
                $variantFile = $authSubDir . '/' . $variant . '.' . $ext;
                if ($filesystem->exists($variantFile)) {
                    if ($stack === 'react') {
                        $variantContent = <<<TSX
import GuestLayout from '@/{$layoutsBase}/GuestLayout';
import type { AuthLayoutProps } from '@/types';

export default function AuthLayoutVariant({
    children,
    title = '',
    description = '',
}: AuthLayoutProps) {
    return (
        <GuestLayout title={title} description={description}>
            {children}
        </GuestLayout>
    );
}
TSX;
                    } elseif ($stack === 'vue') {
                        $variantContent = <<<VUE
<script setup>
import GuestLayout from '@/{$layoutsBase}/GuestLayout.vue';

defineProps({
    title: String,
    description: String,
});
</script>

<template>
    <GuestLayout :title="title" :description="description">
        <slot />
    </GuestLayout>
</template>
VUE;
                    } else {
                        $variantContent = <<<SVELTE
<script>
    import GuestLayout from '@/{$layoutsBase}/GuestLayout.svelte';

    export let title = '';
    export let description = '';
</script>

<GuestLayout {title} {description}>
    <slot />
</GuestLayout>
SVELTE;
                    }
                    $filesystem->put($variantFile, $variantContent);
                    $this->line("    - <fg=green>Layout Starter Kit {$variant} adaptado ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $variantFile));
                }
            }
        }

        // 3. Dashboard
        $dashboardStub = $stubDir . '/Dashboard.stub';
        if ($filesystem->exists($dashboardStub)) {
            if (! $filesystem->exists($dashboardTarget) || $shouldReplace) {
                $filesystem->copy($dashboardStub, $dashboardTarget);
                $this->line("    - <fg=green>Dashboard limpio VeltraUI ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $dashboardTarget));
            }
        }

        // 4. Componentes Atomic Design (Atoms, Molecules, Organisms)
        foreach (['Atoms', 'Molecules', 'Organisms'] as $level) {
            $sourceLevelDir = $stubDir . '/Components/' . $level;
            $targetLevelDir = $componentsDir . '/' . $level;
            $filesystem->ensureDirectoryExists($targetLevelDir);

            if ($filesystem->isDirectory($sourceLevelDir)) {
                $files = $filesystem->files($sourceLevelDir);
                foreach ($files as $file) {
                    $baseName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    $targetFile = $targetLevelDir . '/' . $baseName . '.' . $ext;
                    if (! $filesystem->exists($targetFile) || $shouldReplace) {
                        $filesystem->copy($file->getPathname(), $targetFile);
                    }
                }
            }
        }
        $this->line("    - <fg=green>Componentes Atomic Design ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $componentsDir) . "/ (Atoms, Molecules, Organisms)");

        // 5. Adapt Starter Kit app-logo-icon / AppLogoIcon if present
        $logoCandidates = [
            $componentsDir . '/app-logo-icon.' . $ext,
            $componentsDir . '/AppLogoIcon.' . $ext,
        ];

        $logoStub = match ($stack) {
            'vue' => <<<'VUE'
<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

defineProps<{
    className?: HTMLAttributes['class'];
}>();
</script>

<template>
    <img
        src="/vendor/veltra/img/logo.png"
        alt="VeltraUI"
        class="w-8 h-8 rounded-xl object-contain"
        :class="className"
        v-bind="$attrs"
    />
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script>
    let { class: className = '', ...props } = $props();
</script>

<img
    src="/vendor/veltra/img/logo.png"
    alt="VeltraUI"
    class="w-8 h-8 rounded-xl object-contain {className}"
    {...props}
/>
SVELTE,
            default => <<<'TSX'
import React from 'react';

export default function AppLogoIcon({ className = '', ...props }: any) {
    return (
        <img
            src="/vendor/veltra/img/logo.png"
            alt="VeltraUI"
            className={`w-8 h-8 rounded-xl object-contain ${className}`}
            {...props}
        />
    );
}
TSX,
        };

        foreach ($logoCandidates as $logoIconTarget) {
            if ($filesystem->exists($logoIconTarget) && $shouldReplace) {
                $filesystem->put($logoIconTarget, $logoStub);
                $this->line("    - <fg=green>Logo de Starter Kit adaptado con VeltraUI:</fg=green> " . str_replace(base_path() . '/', '', $logoIconTarget));
            }
        }

        // 6. Error Page (Inertia)
        $errorStub = $stubDir . '/Pages/Error.stub';
        if ($filesystem->exists($errorStub)) {
            $pagesDir = resource_path('js/pages');
            if (! $filesystem->isDirectory($pagesDir)) {
                $pagesDir = resource_path('js/Pages');
            }
            $filesystem->ensureDirectoryExists($pagesDir);
            $errorFileName = ($stack === 'react') ? 'error.tsx' : ('Error.' . $ext);
            $errorTarget = $pagesDir . '/' . $errorFileName;
            if (! $filesystem->exists($errorTarget) || $shouldReplace) {
                $filesystem->copy($errorStub, $errorTarget);
                $this->line("    - <fg=green>Página de Error VeltraUI ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $errorTarget));
            }
        }

        // 7. Login Page (Inertia)
        $loginStub = $stubDir . '/Pages/Auth/Login.stub';
        if ($filesystem->exists($loginStub)) {
            $authPagesDir = resource_path('js/pages/auth');
            if (! $filesystem->isDirectory($authPagesDir)) {
                $authPagesDir = resource_path('js/Pages/Auth');
            }
            if (! $filesystem->isDirectory($authPagesDir)) {
                $authPagesDir = resource_path('js/pages/auth');
            }
            $filesystem->ensureDirectoryExists($authPagesDir);

            $loginFileName = ($stack === 'react') ? 'login.tsx' : ('Login.' . $ext);
            $loginTarget = $authPagesDir . '/' . $loginFileName;

            if (! $filesystem->exists($loginTarget) || $shouldReplace) {
                $filesystem->copy($loginStub, $loginTarget);
                $this->line("    - <fg=green>Página de Login VeltraUI ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $loginTarget));
            }

            // Asegurar que Fortify comparta canUsePasskey si tiene passkeys configurado
            $fortifyProvider = app_path('Providers/FortifyServiceProvider.php');
            if ($filesystem->exists($fortifyProvider)) {
                $fContent = $filesystem->get($fortifyProvider);
                if (str_contains($fContent, "Inertia::render('auth/login'") && ! str_contains($fContent, "'canUsePasskey'")) {
                    $fContent = str_replace(
                        "'canResetPassword' => Features::enabled(Features::resetPasswords()),",
                        "'canResetPassword' => Features::enabled(Features::resetPasswords()),\n            'canUsePasskey' => Features::enabled(Features::passkeys()),",
                        $fContent
                    );
                    $filesystem->put($fortifyProvider, $fContent);
                }
            }
        }

        // 8. Settings Layout & Profile Page (Inertia)
        $settingsLayoutStub = $stubDir . '/Layouts/SettingsLayout.stub';
        if ($filesystem->exists($settingsLayoutStub)) {
            $settingsLayoutDir = resource_path('js/layouts/settings');
            if (! $filesystem->isDirectory($settingsLayoutDir)) {
                $settingsLayoutDir = resource_path('js/Layouts/Settings');
            }
            if ($filesystem->isDirectory(dirname($settingsLayoutDir))) {
                $filesystem->ensureDirectoryExists($settingsLayoutDir);
                $settingsLayoutFile = ($stack === 'react') ? 'layout.tsx' : ('Layout.' . $ext);
                $settingsLayoutTarget = $settingsLayoutDir . '/' . $settingsLayoutFile;
                if (! $filesystem->exists($settingsLayoutTarget) || $shouldReplace) {
                    $filesystem->copy($settingsLayoutStub, $settingsLayoutTarget);
                    $this->line("    - <fg=green>Layout Settings VeltraUI ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $settingsLayoutTarget));
                }
            }
        }

        // 8.1 Settings Subpages (Profile, Security, Appearance)
        $settingsPages = [
            'Profile' => [
                'react' => 'profile.tsx',
                'other' => 'Profile.' . $ext,
                'desc' => 'Página de Perfil',
            ],
            'Security' => [
                'react' => 'security.tsx',
                'other' => 'Security.' . $ext,
                'desc' => 'Página de Seguridad & 2FA',
            ],
            'Appearance' => [
                'react' => 'appearance.tsx',
                'other' => 'Appearance.' . $ext,
                'desc' => 'Página de Apariencia',
            ],
        ];

        foreach ($settingsPages as $pageKey => $pageMeta) {
            $pageStub = $stubDir . "/Pages/Settings/{$pageKey}.stub";
            if ($filesystem->exists($pageStub)) {
                $settingsPagesDir = resource_path('js/pages/settings');
                if (! $filesystem->isDirectory($settingsPagesDir)) {
                    $settingsPagesDir = resource_path('js/Pages/Settings');
                }
                if ($filesystem->isDirectory(dirname($settingsPagesDir))) {
                    $filesystem->ensureDirectoryExists($settingsPagesDir);
                    $pageFile = ($stack === 'react') ? $pageMeta['react'] : $pageMeta['other'];
                    $pageTarget = $settingsPagesDir . '/' . $pageFile;
                    if (! $filesystem->exists($pageTarget) || $shouldReplace) {
                        $filesystem->copy($pageStub, $pageTarget);
                        $this->line("    - <fg=green>{$pageMeta['desc']} VeltraUI ({$stack}):</fg=green> " . str_replace(base_path() . '/', '', $pageTarget));
                    }
                }
            }
        }

        // 8.2 Adapt ProfileController for Unified Settings (2FA & Passkeys)
        $this->setupInertiaProfileController($filesystem);

        // 8.3 Adapt use-appearance hook for React if present
        $this->setupInertiaHooks($filesystem);

        // 9. Root app.blade.php
        $this->setupInertiaRootView($filesystem, $stack, $ext);

        // 8. Inject Veltra navigation to HandleInertiaRequests middleware
        $this->setupInertiaMiddleware($filesystem);

        return $shouldReplace;
    }

    /**
     * Adapt ProfileController for Unified Settings (2FA & Passkeys).
     */
    protected function setupInertiaProfileController(Filesystem $filesystem): void
    {
        $controllerCandidates = [
            app_path('Http/Controllers/Settings/ProfileController.php'),
            app_path('Http/Controllers/ProfileController.php'),
        ];

        foreach ($controllerCandidates as $controllerPath) {
            if (! $filesystem->exists($controllerPath)) {
                continue;
            }

            $content = $filesystem->get($controllerPath);

            // If already handling canManageTwoFactor, skip
            if (str_contains($content, 'canManageTwoFactor')) {
                continue;
            }

            // Ensure Features import
            if (! str_contains($content, 'use Laravel\Fortify\Features;')) {
                $content = preg_replace(
                    '/(namespace\s+[^\n;]+;[\r\n]+)/',
                    "$1\nuse Laravel\\Fortify\\Features;\n",
                    $content,
                    1
                );
            }

            // Replace standard edit() implementation with Fortify 2FA + Passkeys enriched version
            $targetEditPattern = '/public\s+function\s+edit\s*\([^\)]*\)\s*:\s*Response\s*\{[\s\S]*?return\s+Inertia::render\([\'"]settings\/[Pp]rofile[\'"],\s*\[[\s\S]*?\]\s*\);\s*\}/';

            $componentName = str_contains($content, "settings/Profile") ? "settings/Profile" : "settings/profile";

            $replacementEdit = <<<PHP
public function edit(Request \$request): Response
    {
        \$user = \$request->user();

        \$canManageTwoFactor = class_exists(Features::class)
            && Features::canManageTwoFactorAuthentication();
        \$canManagePasskeys = class_exists(Features::class)
            && Features::canManagePasskeys();

        \$passkeys = [];
        if (\$canManagePasskeys && \$user && method_exists(\$user, 'passkeys')) {
            \$passkeys = \$user
                ->passkeys()
                ->select(['id', 'name', 'credential', 'created_at', 'last_used_at'])
                ->latest()
                ->get()
                ->map(fn (\$passkey) => [
                    'id' => \$passkey->id,
                    'name' => \$passkey->name,
                    'authenticator' => \$passkey->authenticator,
                    'created_at_diff' => \$passkey->created_at?->diffForHumans(),
                    'last_used_at_diff' => \$passkey->last_used_at?->diffForHumans(),
                ])
                ->values()
                ->all();
        }

        \$twoFactorEnabled = false;
        \$requiresConfirmation = false;
        if (\$canManageTwoFactor && \$user) {
            \$twoFactorEnabled = method_exists(\$user, 'hasEnabledTwoFactorAuthentication')
                ? (bool) \$user->hasEnabledTwoFactorAuthentication()
                : (! is_null(\$user->two_factor_secret) && ! is_null(\$user->two_factor_confirmed_at));
            \$requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
        }

        return Inertia::render('{$componentName}', [
            'mustVerifyEmail' => \$user instanceof MustVerifyEmail,
            'status' => \$request->session()->get('status'),
            'canManageTwoFactor' => \$canManageTwoFactor,
            'twoFactorEnabled' => \$twoFactorEnabled,
            'requiresConfirmation' => \$requiresConfirmation,
            'canManagePasskeys' => \$canManagePasskeys,
            'passkeys' => \$passkeys,
        ]);
    }
PHP;

            if (preg_match($targetEditPattern, $content)) {
                $content = preg_replace($targetEditPattern, $replacementEdit, $content, 1);
                $filesystem->put($controllerPath, $content);
                $this->line('    - <fg=green>Adaptado ProfileController para 2FA y Passkeys en VeltraUI:</fg=green> ' . str_replace(base_path() . '/', '', $controllerPath));
            }
        }
    }

    /**
     * Setup Inertia Middleware to share Veltra navigation and brand.
     */
    protected function setupInertiaMiddleware(Filesystem $filesystem): void
    {
        $middlewarePath = app_path('Http/Middleware/HandleInertiaRequests.php');
        if (! $filesystem->exists($middlewarePath)) {
            return;
        }

        $content = $filesystem->get($middlewarePath);
        if (str_contains($content, "'veltra'")) {
            return;
        }

        $search = "...parent::share(\$request),";
        $injection = <<<'PHP'
...parent::share($request),
            'veltra' => [
                'brand' => config('veltra.brand'),
                'navigation' => class_exists(\Veltra\VeltraLaravel\Support\Navigation::class)
                    ? \Veltra\VeltraLaravel\Support\Navigation::getTree()
                    : [],
            ],
PHP;
        if (str_contains($content, $search)) {
            $content = str_replace($search, $injection, $content);
            $filesystem->put($middlewarePath, $content);
            $this->line('    - <fg=green>HandleInertiaRequests adaptado con Veltra navigation:</fg=green> app/Http/Middleware/HandleInertiaRequests.php');
        }
    }

    /**
     * Adapt use-appearance hook if present for seamless VeltraUI theme integration.
     */
    protected function setupInertiaHooks(Filesystem $filesystem): void
    {
        $hookCandidates = [
            resource_path('js/hooks/use-appearance.tsx'),
            resource_path('js/hooks/useAppearance.tsx'),
            resource_path('js/hooks/use-appearance.ts'),
        ];

        foreach ($hookCandidates as $hookPath) {
            if ($filesystem->exists($hookPath)) {
                $content = $filesystem->get($hookPath);
                if (! str_contains($content, 'data-theme')) {
                    $content = str_replace(
                        "document.documentElement.classList.toggle('dark', isDark);",
                        "const effectiveTheme = isDark ? 'dark' : 'light';\n    document.documentElement.setAttribute('data-theme', effectiveTheme);\n    document.documentElement.classList.toggle('dark', isDark);\n    try { localStorage.setItem('veltra-theme', appearance); } catch (e) {}",
                        $content
                    );
                    $filesystem->put($hookPath, $content);
                    $this->line('    - <fg=green>Hook de apariencia adaptado con VeltraUI:</fg=green> ' . str_replace(base_path() . '/', '', $hookPath));
                }
            }
        }
    }

    /**
     * Setup or adapt resources/views/app.blade.php for Inertia.
     */
    protected function setupInertiaRootView(Filesystem $filesystem, string $stack, string $ext): void
    {
        $appBladePath = resource_path('views/app.blade.php');

        if (! $filesystem->exists($appBladePath)) {
            $stub = $filesystem->get(__DIR__ . '/../../stubs/inertia/common/app.blade.stub');
            if ($stack === 'react') {
                $viteEntry = "@viteReactRefresh\n        @vite(['resources/js/app.{$ext}', \"resources/js/Pages/{\$page['component']}.{$ext}\"])";
            } elseif ($stack === 'vue') {
                $viteEntry = "@vite(['resources/js/app.js', \"resources/js/Pages/{\$page['component']}.vue\"])";
            } else {
                $viteEntry = "@vite(['resources/js/app.js', \"resources/js/Pages/{\$page['component']}.svelte\"])";
            }
            $stub = preg_replace('/@viteReactRefresh\s+@vite\([^)]+\)/s', $viteEntry, $stub);
            $filesystem->put($appBladePath, $stub);
            $this->line('    - <fg=green>Vista raíz Inertia creada:</fg=green> resources/views/app.blade.php');
            return;
        }

        $content = $filesystem->get($appBladePath);

        // Ensure data-theme attribute in <html> tag safely without breaking -> in app()->getLocale()
        if (! str_contains($content, 'data-theme')) {
            $content = preg_replace('/<html\b/i', '<html data-theme="dark"', $content, 1);
        }

        // Remove legacy/conflicting starter kit inline scripts if present
        $content = preg_replace('/\{\{-- Inline script to detect system dark mode preference.*?<\/script>/s', '', $content);
        $content = preg_replace('/<script>\s*\(function\(\)\s*\{\s*const appearance = \'\s*\{\{\s*\$appearance.*?\);?\s*<\/script>/s', '', $content);

        // Ensure Zero-FOUC theme script
        if (! str_contains($content, 'Zero-FOUC Unified Veltra & Laravel Theme Script')) {
            $antiFoucScript = <<<'HTML'

        <!-- Zero-FOUC Unified Veltra & Laravel Theme Script -->
        <script>
            (function() {
                try {
                    const savedTheme = localStorage.getItem('veltra-theme') || localStorage.getItem('appearance') || 'dark';
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    const isDark = savedTheme === 'dark' || (savedTheme === 'system' && prefersDark);
                    const effective = isDark ? 'dark' : 'light';

                    document.documentElement.setAttribute('data-theme', effective);
                    if (isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                    document.documentElement.style.colorScheme = effective;
                } catch (e) {}
            })();
        </script>
HTML;
            if (str_contains($content, '<!-- Zero-FOUC Theme Script -->')) {
                $content = preg_replace('/<!-- Zero-FOUC Theme Script -->\s*<script>.*?<\/script>/s', trim($antiFoucScript), $content);
            } else {
                $content = str_replace('<head>', "<head>{$antiFoucScript}", $content);
            }
        }

        // Ensure VeltraUI CSS
        if (! str_contains($content, 'vendor/veltra/css/veltra.css')) {
            $cssLink = "\n        <link rel=\"stylesheet\" href=\"{{ asset('vendor/veltra/css/veltra.css') }}\">\n";
            if (str_contains($content, '@vite')) {
                $content = preg_replace('/(@viteReactRefresh|@vite)/', "{$cssLink}        $1", $content, 1);
            } else {
                $content = str_replace('</head>', "{$cssLink}    </head>", $content);
            }
        }

        // Ensure Lucide icons script
        if (! str_contains($content, 'lucide')) {
            $lucideScript = "\n        <script src=\"https://unpkg.com/lucide@latest\"></script>\n";
            $content = str_replace('</head>', "{$lucideScript}    </head>", $content);
        }

        // Strip starter kit hardcoded white background styles if present
        $content = preg_replace('/<style>\s*html\s*\{[^}]*\}\s*html\.dark\s*\{[^}]*\}\s*<\/style>/s', '', $content);

        // Ensure body classes
        if (str_contains($content, '<body class="')) {
            $content = preg_replace('/<body class="[^"]*"/', '<body class="min-h-screen font-sans antialiased bg-slate-50 dark:bg-[#060913] text-slate-800 dark:text-slate-100"', $content, 1);
        }

        $filesystem->put($appBladePath, $content);
        $this->line('    - <fg=green>Vista raíz Inertia adaptada con VeltraUI:</fg=green> resources/views/app.blade.php');
    }

    /**
     * Ensure /dashboard route is defined in routes/web.php.
     */
    protected function ensureDashboardRoute(Filesystem $filesystem): void
    {
        $webRoutesPath = base_path('routes/web.php');
        if ($filesystem->exists($webRoutesPath)) {
            $content = $filesystem->get($webRoutesPath);
            if (! str_contains($content, "'dashboard'")) {
                $snippet = "\nRoute::view('dashboard', 'dashboard')\n    ->middleware(['auth'])\n    ->name('dashboard');\n";
                $filesystem->append($webRoutesPath, $snippet);
                $this->line('    - <fg=green>Ruta /dashboard registrada en:</fg=green> routes/web.php');
            }
        }
    }

    /**
     * Adapt starter kit auth views.
     */
    protected function adaptStarterKitAuth(Filesystem $filesystem, bool $force): void
    {
        // 1. Si tiene auth de Blade tradicional (Breeze), publicar vistas de Veltra
        $authDir = resource_path('views/auth');
        if ($filesystem->isDirectory($authDir)) {
            $sourceDir = __DIR__ . '/../../resources/views/auth';
            $files = $filesystem->files($sourceDir);
            foreach ($files as $file) {
                $target = $authDir . '/' . $file->getFilename();
                if ($force || ! $filesystem->exists($target)) {
                    $filesystem->copy($file->getPathname(), $target);
                }
            }
            $this->line('    - <fg=green>Vistas de autenticación adaptadas en:</fg=green> resources/views/auth/');
        }

        // 2. Si tiene Laravel 12+ Livewire Starter Kit (Flux / Fortify layouts)
        $layoutsAuthDir = resource_path('views/layouts/auth');
        $layoutAuthFile = resource_path('views/layouts/auth.blade.php');

        if ($filesystem->exists($layoutAuthFile) || $filesystem->isDirectory($layoutsAuthDir)) {
            $veltraAuthBridge = <<<'BLADE'
<x-guest-layout :title="$title ?? null">
    <div class="vlt-auth-card">
        {{ $slot }}
    </div>
</x-guest-layout>
BLADE;
            if ($filesystem->exists($layoutAuthFile)) {
                $filesystem->put($layoutAuthFile, $veltraAuthBridge);
            }
            if ($filesystem->isDirectory($layoutsAuthDir)) {
                foreach (['simple.blade.php', 'card.blade.php', 'split.blade.php'] as $variant) {
                    $target = $layoutsAuthDir . '/' . $variant;
                    if ($filesystem->exists($target) || $force) {
                        $filesystem->put($target, $veltraAuthBridge);
                    }
                }
            }
            $this->line('    - <fg=green>Layouts de autenticación de Livewire (Flux) adaptados en:</fg=green> resources/views/layouts/auth/');
        }

        // 3. Si tiene vistas de autenticación en resources/views/livewire/auth (Laravel 12 Livewire)
        $livewireAuthDir = resource_path('views/livewire/auth');
        if ($filesystem->isDirectory($livewireAuthDir)) {
            $lwSourceDir = __DIR__ . '/../../stubs/livewire/auth';
            if ($filesystem->isDirectory($lwSourceDir)) {
                $files = $filesystem->files($lwSourceDir);
                foreach ($files as $file) {
                    $baseName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    $target = $livewireAuthDir . '/' . $baseName . '.blade.php';
                    if ($force || $filesystem->exists($target)) {
                        $filesystem->copy($file->getPathname(), $target);
                    }
                }
                $this->line('    - <fg=green>Formularios de autenticación Livewire adaptados con VeltraUI en:</fg=green> resources/views/livewire/auth/');
            }
        }

        // 4. Si tiene vistas de ajustes / configuración (Laravel 12 Livewire Settings)
        $livewireSettingsDir = resource_path('views/livewire/settings');
        if ($filesystem->isDirectory($livewireSettingsDir)) {
            $settingsSourceDir = __DIR__ . '/../../stubs/livewire/settings';
            if ($filesystem->isDirectory($settingsSourceDir)) {
                $files = $filesystem->files($settingsSourceDir);
                foreach ($files as $file) {
                    $baseName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    $target = $livewireSettingsDir . '/' . $baseName . '.blade.php';
                    if ($force || $filesystem->exists($target)) {
                        $filesystem->copy($file->getPathname(), $target);
                    }
                }
            }

            // Layout de Settings
            $settingsLayoutStub = __DIR__ . '/../../stubs/livewire/components/settings/layout.stub';
            $settingsLayoutTarget = resource_path('views/components/settings/layout.blade.php');
            if ($filesystem->exists($settingsLayoutStub) && ($force || $filesystem->exists($settingsLayoutTarget))) {
                $filesystem->ensureDirectoryExists(dirname($settingsLayoutTarget));
                $filesystem->copy($settingsLayoutStub, $settingsLayoutTarget);
            }

            // Heading de Settings
            $settingsHeadingStub = __DIR__ . '/../../stubs/livewire/partials/settings-heading.stub';
            $settingsHeadingTarget = resource_path('views/partials/settings-heading.blade.php');
            if ($filesystem->exists($settingsHeadingStub) && ($force || $filesystem->exists($settingsHeadingTarget))) {
                $filesystem->ensureDirectoryExists(dirname($settingsHeadingTarget));
                $filesystem->copy($settingsHeadingStub, $settingsHeadingTarget);
            }

            // Componente action-message
            $actionMessageStub = __DIR__ . '/../../stubs/livewire/components/action-message.stub';
            $actionMessageTarget = resource_path('views/components/action-message.blade.php');
            if ($filesystem->exists($actionMessageStub) && (! $filesystem->exists($actionMessageTarget) || $force)) {
                $filesystem->ensureDirectoryExists(dirname($actionMessageTarget));
                $filesystem->copy($actionMessageStub, $actionMessageTarget);
            }

            // Compatibilidad 2FA: asegurar soporte para showVerification y showVerificationIfNecessary
            $securityComponent = app_path('Livewire/Settings/Security.php');
            if ($filesystem->exists($securityComponent)) {
                $secContent = $filesystem->get($securityComponent);
                if (str_contains($secContent, 'showVerificationIfNecessary') && ! str_contains($secContent, 'function showVerification(')) {
                    $secContent = str_replace(
                        'public function showVerificationIfNecessary(): void',
                        "public function showVerification(): void\n    {\n        \$this->showVerificationIfNecessary();\n    }\n\n    public function showVerificationIfNecessary(): void",
                        $secContent
                    );
                    $filesystem->put($securityComponent, $secContent);
                }
            }

            $this->line('    - <fg=green>Vistas y paneles de Ajustes/Perfil Livewire adaptados con VeltraUI en:</fg=green> resources/views/livewire/settings/');
        }
    }

    /**
     * Setup Tailwind CSS and Vite integration.
     */
    protected function setupTailwindAndVite(Filesystem $filesystem): void
    {
        // 1. Configurar resources/css/app.css para Tailwind CSS v4 y VeltraUI
        $cssPath = resource_path('css/app.css');
        if ($filesystem->exists($cssPath)) {
            $content = $filesystem->get($cssPath);

            // Asegurar @custom-variant dark para sincronización de Tailwind v4 con data-theme="dark" y .dark
            $vltVariant = '@custom-variant dark (&:where([data-theme="dark"], [data-theme="dark"] *, .dark, .dark *));';
            if (str_contains($content, '@custom-variant dark')) {
                $content = preg_replace('/@custom-variant\s+dark\s+[^;]+;/', $vltVariant, $content);
            } else {
                if (str_contains($content, "@import 'tailwindcss';")) {
                    $content = str_replace("@import 'tailwindcss';", "@import 'tailwindcss';\n\n" . $vltVariant, $content);
                } elseif (str_contains($content, '@import "tailwindcss";')) {
                    $content = str_replace('@import "tailwindcss";', "@import \"tailwindcss\";\n\n" . $vltVariant, $content);
                } else {
                    $content .= "\n" . $vltVariant . "\n";
                }
            }

            // Asegurar fuentes de escaneo para Tailwind v4
            if (! str_contains($content, 'veltra-laravel')) {
                $sources = "\n@source '../../resources/views/**/*.blade.php';\n@source '../../resources/js/**/*.{js,ts,jsx,tsx,vue,svelte}';\n@source '../../vendor/veltra/veltra-laravel/resources/views/**/*.blade.php';\n";
                $content .= $sources;
            } elseif (! str_contains($content, 'resources/js')) {
                $content .= "\n@source '../../resources/js/**/*.{js,ts,jsx,tsx,vue,svelte}';\n";
            }

            $filesystem->put($cssPath, $content);
            $this->line('    - <fg=green>Configurado Tailwind CSS v4 (@custom-variant dark) en:</fg=green> resources/css/app.css');
        }

        // 2. Configurar tailwind.config.js si existe (Tailwind v3 fallback)
        $tailwindConfig = base_path('tailwind.config.js');
        if ($filesystem->exists($tailwindConfig)) {
            $twContent = $filesystem->get($tailwindConfig);
            if (! str_contains($twContent, 'veltra-laravel')) {
                $twContent = str_replace(
                    "'./resources/views/**/*.blade.php',",
                    "'./resources/views/**/*.blade.php',\n        './resources/js/**/*.{js,ts,jsx,tsx,vue,svelte}',\n        './vendor/veltra/veltra-laravel/resources/views/**/*.blade.php',",
                    $twContent
                );
                $filesystem->put($tailwindConfig, $twContent);
                $this->line('    - <fg=green>Configurado escaneo de vistas en:</fg=green> tailwind.config.js');
            }
        }
    }

    /**
     * Ensure required Lucide icon package is present in package.json.
     */
    protected function ensureNpmDependencies(Filesystem $filesystem, string $stack): void
    {
        $packageJsonPath = base_path('package.json');
        if (! $filesystem->exists($packageJsonPath)) {
            return;
        }

        $packageJson = json_decode($filesystem->get($packageJsonPath), true);
        if (! is_array($packageJson)) {
            return;
        }

        $deps = $packageJson['dependencies'] ?? [];
        $devDeps = $packageJson['devDependencies'] ?? [];
        $modified = false;

        if ($stack === 'vue') {
            if (! isset($deps['@lucide/vue']) && ! isset($devDeps['@lucide/vue']) && ! isset($deps['lucide-vue-next']) && ! isset($devDeps['lucide-vue-next'])) {
                $packageJson['dependencies']['@lucide/vue'] = '^1.17.0';
                $modified = true;
            }
        } elseif ($stack === 'react') {
            if (! isset($deps['lucide-react']) && ! isset($devDeps['lucide-react'])) {
                $packageJson['dependencies']['lucide-react'] = '^1.16.0';
                $modified = true;
            }
        } elseif ($stack === 'svelte') {
            if (! isset($deps['@lucide/svelte']) && ! isset($devDeps['@lucide/svelte']) && ! isset($deps['lucide-svelte']) && ! isset($devDeps['lucide-svelte'])) {
                $packageJson['dependencies']['@lucide/svelte'] = '^1.35.0';
                $modified = true;
            }
        }

        if ($modified) {
            $filesystem->put(
                $packageJsonPath,
                json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
            );
            $this->line('    - <fg=green>Dependencias de iconos Lucide añadidas a:</fg=green> package.json');
        }
    }

    /**
     * Build Vite assets.
     */
    protected function buildAssets(): void
    {
        if (file_exists(base_path('package.json'))) {
            $this->components->info('Compilando assets de frontend con Vite (npm run build)...');
            $process = new Process(['npm', 'run', 'build'], base_path());
            $process->setTimeout(120);
            $process->run();

            if ($process->isSuccessful()) {
                $this->line('    - <fg=green>Compilación exitosa:</fg=green> Vite generó los bundles de Tailwind y VeltraUI.');
            } else {
                $this->components->warn('No se pudo ejecutar npm run build automáticamente. Ejecuta manualmente: npm run build');
            }
        }
    }

    /**
     * Display CLI banner.
     */
    protected function displayBanner(): void
    {
        $this->newLine();
        $this->line('  <fg=black;bg=cyan;options=bold>  VELTRA  </><fg=black;bg=white;options=bold> UI </><fg=cyan;options=bold> · Enterprise Suite</>');
        $this->line('  <fg=gray>Instalador Inteligente de Suite Administrativa para Laravel</>');
        $this->newLine();
    }

    /**
     * Display success messages.
     */
    protected function displaySuccess(string $stack): void
    {
        $this->newLine();
        $this->line('  <fg=green;options=bold>✔ Instalación y Configuración Completada</>');
        $this->newLine();

        $rows = [
            ['<fg=gray>Stack Activo</>', "<fg=yellow;options=bold>[" . strtoupper($stack) . "]</>"],
            ['<fg=gray>Dashboard</>', '<fg=cyan;options=bold>' . url('/dashboard') . '</>'],
            ['<fg=gray>Inicio de Sesión</>', '<fg=cyan>' . url('/login') . '</>'],
            ['<fg=gray>Registro</>', '<fg=cyan>' . url('/register') . '</>'],
            ['<fg=gray>Vitrina Demo</>', '<fg=cyan>' . url('/veltra/demo') . '</>'],
        ];

        $this->table(['Parámetro', 'Detalle'], $rows);

        $this->newLine();
        $this->line('  <fg=cyan;options=bold>Siguientes Pasos:</>');
        $this->line('  1. En una terminal ejecuta: <fg=yellow>npm run dev</>');
        $this->line('  2. En otra terminal ejecuta: <fg=yellow>php artisan serve</>');
        $this->line('  3. Crea tu primera vista con: <fg=cyan>php artisan veltra:page</>');
        $this->newLine();
    }
}
