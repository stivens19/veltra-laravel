<?php

namespace Veltra\VeltraLaravel\Tests\Feature;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Veltra\VeltraLaravel\Tests\TestCase;

class CommandsTest extends TestCase
{
    #[Test]
    public function it_can_run_make_page_command(): void
    {
        $testViewPath = resource_path('views/admin/test-dashboard.blade.php');

        if (File::exists($testViewPath)) {
            File::delete($testViewPath);
        }

        $this->artisan('veltra:make-page', [
            'name' => 'admin.test-dashboard',
            '--template' => 'analytics',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertTrue(File::exists($testViewPath));
        $content = File::get($testViewPath);
        $this->assertStringContainsString('Test Dashboard', $content);
        $this->assertStringContainsString('x-veltra::layout.app', $content);

        // Limpieza
        File::delete($testViewPath);
    }

    #[Test]
    public function it_can_generate_page_for_react_inertia(): void
    {
        $testPagePath = resource_path('js/Pages/Admin/ReactTest.jsx');
        $routesPath = base_path('routes/web.php');

        File::ensureDirectoryExists(dirname($testPagePath));
        File::ensureDirectoryExists(dirname($routesPath));

        if (! File::exists($routesPath)) {
            File::put($routesPath, "<?php\n\nuse Illuminate\Support\Facades\Route;\n");
        }

        $this->artisan('veltra:make-page', [
            'name' => 'Admin/ReactTest',
            '--stack' => 'react',
            '--route' => true,
            '--route-uri' => '/admin/react-test',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertTrue(File::exists($testPagePath));
        $content = File::get($testPagePath);
        $this->assertStringContainsString('ReactTest', $content);
        $this->assertStringContainsString('@inertiajs/react', $content);

        $routeContent = File::get($routesPath);
        $this->assertStringContainsString("Route::inertia('/admin/react-test', 'Admin/ReactTest')", $routeContent);

        // Limpieza
        File::delete($testPagePath);
    }

    #[Test]
    public function it_can_generate_page_for_vue_inertia(): void
    {
        $testPagePath = resource_path('js/Pages/Admin/VueTest.vue');
        $routesPath = base_path('routes/web.php');

        File::ensureDirectoryExists(dirname($testPagePath));

        $this->artisan('veltra:make-page', [
            'name' => 'Admin/VueTest',
            '--stack' => 'vue',
            '--route' => true,
            '--route-uri' => '/admin/vue-test',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertTrue(File::exists($testPagePath));
        $content = File::get($testPagePath);
        $this->assertStringContainsString('VueTest', $content);
        $this->assertStringContainsString('@inertiajs/vue3', $content);

        $routeContent = File::get($routesPath);
        $this->assertStringContainsString("Route::inertia('/admin/vue-test', 'Admin/VueTest')", $routeContent);

        // Limpieza
        File::delete($testPagePath);
    }

    #[Test]
    public function it_can_generate_page_for_svelte_inertia(): void
    {
        $testPagePath = resource_path('js/Pages/Admin/SvelteTest.svelte');
        $routesPath = base_path('routes/web.php');

        File::ensureDirectoryExists(dirname($testPagePath));

        $this->artisan('veltra:make-page', [
            'name' => 'Admin/SvelteTest',
            '--stack' => 'svelte',
            '--route' => true,
            '--route-uri' => '/admin/svelte-test',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertTrue(File::exists($testPagePath));
        $content = File::get($testPagePath);
        $this->assertStringContainsString('SvelteTest', $content);
        $this->assertStringContainsString('@inertiajs/svelte', $content);

        $routeContent = File::get($routesPath);
        $this->assertStringContainsString("Route::inertia('/admin/svelte-test', 'Admin/SvelteTest')", $routeContent);

        // Limpieza
        File::delete($testPagePath);
    }

    #[Test]
    public function it_can_generate_page_with_menu_and_route(): void
    {
        $testViewPath = resource_path('views/admin/orders.blade.php');
        $routesPath = base_path('routes/web.php');
        $configPath = config_path('veltra.php');

        File::ensureDirectoryExists(dirname($routesPath));
        File::ensureDirectoryExists(dirname($configPath));

        if (! File::exists($routesPath)) {
            File::put($routesPath, "<?php\n\nuse Illuminate\Support\Facades\Route;\n");
        }

        $this->artisan('veltra:make-page', [
            'name' => 'admin.orders',
            '--template' => 'table',
            '--menu' => true,
            '--menu-title' => 'Gestión de Pedidos',
            '--menu-icon' => 'shopping-bag',
            '--menu-section' => 'MAIN NAVIGATION',
            '--route' => true,
            '--route-uri' => '/admin/orders',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertTrue(File::exists($testViewPath));
        $content = File::get($testViewPath);
        $this->assertStringContainsString('Orders', $content);

        // Verificar que se haya registrado en config/veltra.php
        $this->assertTrue(File::exists($configPath));
        $configContent = File::get($configPath);
        $this->assertStringContainsString('Gestión de Pedidos', $configContent);
        $this->assertStringContainsString('shopping-bag', $configContent);

        // Verificar que se haya registrado en routes/web.php
        $routeContent = File::get($routesPath);
        $this->assertStringContainsString('/admin/orders', $routeContent);
        $this->assertStringContainsString("view('admin.orders')", $routeContent);

        // Limpieza
        File::delete($testViewPath);
    }

    #[Test]
    public function it_can_run_doctor_command(): void
    {
        $this->artisan('veltra:doctor')
            ->assertSuccessful();
    }

    #[Test]
    public function it_can_install_veltra_for_blade_with_atomic_design(): void
    {
        $this->artisan('veltra:install', [
            '--stack' => 'none',
            '--force' => true,
            '--no-build' => true,
        ])->assertSuccessful();

        // Verificar componentes Atomic Design en Blade
        $this->assertTrue(File::exists(resource_path('views/components/atoms/brand-logo.blade.php')));
        $this->assertTrue(File::exists(resource_path('views/components/atoms/theme-toggle.blade.php')));
        $this->assertTrue(File::exists(resource_path('views/components/atoms/badge.blade.php')));
        $this->assertTrue(File::exists(resource_path('views/components/atoms/avatar.blade.php')));
        $this->assertTrue(File::exists(resource_path('views/components/atoms/button.blade.php')));
        $this->assertTrue(File::exists(resource_path('views/components/atoms/input.blade.php')));

        $this->assertTrue(File::exists(resource_path('views/components/molecules/user-panel.blade.php')));
        $this->assertTrue(File::exists(resource_path('views/components/molecules/search-input.blade.php')));
        $this->assertTrue(File::exists(resource_path('views/components/molecules/nav-item.blade.php')));
        $this->assertTrue(File::exists(resource_path('views/components/molecules/stat-card.blade.php')));
        $this->assertTrue(File::exists(resource_path('views/components/molecules/form-field.blade.php')));

        $this->assertTrue(File::exists(resource_path('views/components/organisms/sidebar.blade.php')));
        $this->assertTrue(File::exists(resource_path('views/components/organisms/header.blade.php')));

        $this->assertTrue(File::exists(resource_path('views/dashboard.blade.php')));
        $this->assertTrue(File::exists(resource_path('views/layouts/app.blade.php')));
        $this->assertTrue(File::exists(resource_path('views/layouts/guest.blade.php')));

        // Cleanup
        File::deleteDirectory(resource_path('views/components'));
        File::deleteDirectory(resource_path('views/layouts'));
        File::delete(resource_path('views/dashboard.blade.php'));
    }

    #[Test]
    public function it_can_install_veltra_for_react_inertia_with_atomic_design(): void
    {
        $this->artisan('veltra:install', [
            '--stack' => 'react',
            '--force' => true,
            '--no-build' => true,
        ])->assertSuccessful();

        // Verificar Layouts y Pages
        $this->assertTrue(File::exists(resource_path('js/Layouts/AppLayout.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Layouts/AuthenticatedLayout.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Layouts/GuestLayout.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Pages/Dashboard.jsx')));
        $this->assertTrue(File::exists(resource_path('views/app.blade.php')));

        // Verificar Atomic Design (Atoms, Molecules, Organisms)
        $this->assertTrue(File::exists(resource_path('js/Components/Atoms/BrandLogo.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Components/Atoms/ThemeToggle.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Components/Atoms/Badge.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Components/Atoms/Avatar.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Components/Atoms/Button.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Components/Atoms/Input.jsx')));

        $this->assertTrue(File::exists(resource_path('js/Components/Molecules/UserPanel.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Components/Molecules/SearchInput.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Components/Molecules/NavItem.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Components/Molecules/StatCard.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Components/Molecules/FormField.jsx')));

        $this->assertTrue(File::exists(resource_path('js/Components/Organisms/Sidebar.jsx')));
        $this->assertTrue(File::exists(resource_path('js/Components/Organisms/Header.jsx')));

        $rootContent = File::get(resource_path('views/app.blade.php'));
        $this->assertStringContainsString('veltra.css', $rootContent);
        $this->assertStringContainsString('veltra-theme', $rootContent);

        // Cleanup
        File::deleteDirectory(resource_path('js'));
        File::delete(resource_path('views/app.blade.php'));
    }

    #[Test]
    public function it_can_install_veltra_for_vue_inertia_with_atomic_design(): void
    {
        $this->artisan('veltra:install', [
            '--stack' => 'vue',
            '--force' => true,
            '--no-build' => true,
        ])->assertSuccessful();

        $this->assertTrue(File::exists(resource_path('js/Layouts/AppLayout.vue')));
        $this->assertTrue(File::exists(resource_path('js/Layouts/AuthenticatedLayout.vue')));
        $this->assertTrue(File::exists(resource_path('js/Layouts/GuestLayout.vue')));
        $this->assertTrue(File::exists(resource_path('js/Pages/Dashboard.vue')));

        // Verificar Atomic Design (Atoms, Molecules, Organisms)
        $this->assertTrue(File::exists(resource_path('js/Components/Atoms/BrandLogo.vue')));
        $this->assertTrue(File::exists(resource_path('js/Components/Atoms/ThemeToggle.vue')));
        $this->assertTrue(File::exists(resource_path('js/Components/Molecules/StatCard.vue')));
        $this->assertTrue(File::exists(resource_path('js/Components/Organisms/Sidebar.vue')));
        $this->assertTrue(File::exists(resource_path('js/Components/Organisms/Header.vue')));

        // Cleanup
        File::deleteDirectory(resource_path('js'));
        File::delete(resource_path('views/app.blade.php'));
    }

    #[Test]
    public function it_can_install_veltra_for_svelte_inertia_with_atomic_design(): void
    {
        $this->artisan('veltra:install', [
            '--stack' => 'svelte',
            '--force' => true,
            '--no-build' => true,
        ])->assertSuccessful();

        $this->assertTrue(File::exists(resource_path('js/Layouts/AppLayout.svelte')));
        $this->assertTrue(File::exists(resource_path('js/Layouts/AuthenticatedLayout.svelte')));
        $this->assertTrue(File::exists(resource_path('js/Layouts/GuestLayout.svelte')));
        $this->assertTrue(File::exists(resource_path('js/Pages/Dashboard.svelte')));

        // Verificar Atomic Design (Atoms, Molecules, Organisms)
        $this->assertTrue(File::exists(resource_path('js/Components/Atoms/BrandLogo.svelte')));
        $this->assertTrue(File::exists(resource_path('js/Components/Atoms/ThemeToggle.svelte')));
        $this->assertTrue(File::exists(resource_path('js/Components/Molecules/StatCard.svelte')));
        $this->assertTrue(File::exists(resource_path('js/Components/Organisms/Sidebar.svelte')));
        $this->assertTrue(File::exists(resource_path('js/Components/Organisms/Header.svelte')));

        // Cleanup
        File::deleteDirectory(resource_path('js'));
        File::delete(resource_path('views/app.blade.php'));
    }

    #[Test]
    public function it_can_run_icons_command(): void
    {
        $this->artisan('veltra:icons', ['search' => 'user'])
            ->assertSuccessful();

        $this->artisan('veltra:icons', ['--category' => 'nav'])
            ->assertSuccessful();
    }

    #[Test]
    public function it_can_run_theme_command(): void
    {
        $this->artisan('veltra:theme', [
            '--name' => 'Custom SaaS',
            '--theme' => 'dark',
            '--boxed' => true,
            '--collapsed' => true,
        ])->assertSuccessful();

        $configContent = File::get(config_path('veltra.php'));
        $this->assertStringContainsString('Custom SaaS', $configContent);
        $this->assertStringContainsString("'default' => env('VELTRA_THEME_DEFAULT', 'dark')", $configContent);
    }

    #[Test]
    public function it_can_run_menu_command(): void
    {
        $this->artisan('veltra:menu', ['--list' => true])
            ->assertSuccessful();

        $this->artisan('veltra:menu', [
            '--add' => true,
            '--title' => 'Inventario',
            '--url' => '/inventory',
            '--icon' => 'package',
            '--section' => 'MAIN NAVIGATION',
        ])->assertSuccessful();

        $configContent = File::get(config_path('veltra.php'));
        $this->assertStringContainsString('Inventario', $configContent);
        $this->assertStringContainsString('/inventory', $configContent);
    }

    #[Test]
    public function it_can_run_component_command(): void
    {
        $targetBlade = resource_path('views/components/molecules/test-card.blade.php');
        if (File::exists($targetBlade)) {
            File::delete($targetBlade);
        }

        $this->artisan('veltra:component', [
            'name' => 'TestCard',
            '--type' => 'molecule',
            '--stack' => 'blade',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertTrue(File::exists($targetBlade));
        $content = File::get($targetBlade);
        $this->assertStringContainsString('vlt-molecule', $content);

        // Limpieza
        File::delete($targetBlade);
    }

    #[Test]
    public function it_can_run_demo_command(): void
    {
        $this->artisan('veltra:demo', ['--enable' => true])
            ->assertSuccessful();

        $configContent = File::get(config_path('veltra.php'));
        $this->assertStringContainsString("'enabled' => env('VELTRA_ROUTES_ENABLED', true)", $configContent);

        $this->artisan('veltra:demo', ['--disable' => true])
            ->assertSuccessful();
    }

    #[Test]
    public function it_can_run_crud_command(): void
    {
        $controllerPath = app_path('Http/Controllers/ProductController.php');
        $indexPath = resource_path('views/products/index.blade.php');
        $createPath = resource_path('views/products/create.blade.php');
        $editPath = resource_path('views/products/edit.blade.php');

        $this->artisan('veltra:crud', [
            'model' => 'Product',
            '--fields' => 'name:string,price:number,status:string',
            '--stack' => 'blade',
            '--menu' => true,
            '--route' => true,
            '--force' => true,
        ])->assertSuccessful();

        $this->assertTrue(File::exists($controllerPath));
        $this->assertTrue(File::exists($indexPath));
        $this->assertTrue(File::exists($createPath));
        $this->assertTrue(File::exists($editPath));

        $indexContent = File::get($indexPath);
        $this->assertStringContainsString('Products', $indexContent);
        $this->assertStringContainsString('x-app-layout', $indexContent);

        // Limpieza
        File::delete($controllerPath);
        File::deleteDirectory(resource_path('views/products'));
    }

    #[Test]
    public function it_can_run_reference_command(): void
    {
        $refDir = base_path('resources/veltra');
        $aiRef = "{$refDir}/AI_REFERENCE.md";
        $catalog = "{$refDir}/catalog.json";

        $this->artisan('veltra:reference', [
            '--stack' => 'blade',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertTrue(File::exists($aiRef));
        $this->assertTrue(File::exists($catalog));
        $this->assertTrue(File::exists("{$refDir}/components/atoms/Button.blade.php"));

        $aiContent = File::get($aiRef);
        $this->assertStringContainsString('VELTRA UI', $aiContent);
        $this->assertStringContainsString('CYAN', $aiContent);

        // Limpieza
        File::deleteDirectory($refDir);
    }

    #[Test]
    public function it_can_run_reference_command_for_all_stacks(): void
    {
        $refDir = base_path('resources/veltra');

        $this->artisan('veltra:reference', [
            '--stack' => 'all',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertTrue(File::exists("{$refDir}/components/blade/atoms/Button.blade.php"));
        $this->assertTrue(File::exists("{$refDir}/components/react/atoms/Button.tsx"));
        $this->assertTrue(File::exists("{$refDir}/components/vue/atoms/Button.vue"));
        $this->assertTrue(File::exists("{$refDir}/components/svelte/atoms/Button.svelte"));

        // Limpieza
        File::deleteDirectory($refDir);
    }

    #[Test]
    public function it_can_run_build_command(): void
    {
        $this->artisan('veltra:build', [
            '--force' => true,
            '--no-compile' => true,
            '--no-cache' => true,
        ])->assertSuccessful();

        $this->assertFalse(config('veltra.routes.enabled', false));
    }

    #[Test]
    public function it_can_run_skill_command(): void
    {
        $this->artisan('veltra:skill', ['--force' => true])->assertSuccessful();

        $skillPath = base_path('.agents/skills/veltra-ui/SKILL.md');
        $this->assertTrue(File::exists($skillPath));

        $content = File::get($skillPath);
        $this->assertStringContainsString('name: veltra-ui', $content);
        $this->assertStringContainsString('Livewire', $content);
        $this->assertStringContainsString('React', $content);
        $this->assertStringContainsString('Vue', $content);
        $this->assertStringContainsString('Svelte', $content);
    }

    #[Test]
    public function it_can_run_mcp_command(): void
    {
        $this->artisan('veltra:mcp', ['--ping' => true])->assertSuccessful();
    }

    #[Test]
    public function it_can_handle_mcp_protocol_requests(): void
    {
        $mcp = new \Veltra\VeltraLaravel\Commands\McpCommand();
        $fs = app(\Illuminate\Filesystem\Filesystem::class);

        // 1. Initialize
        $initRes = $mcp->handleMcpRequest(['id' => 1, 'method' => 'initialize'], $fs, 'livewire');
        $this->assertEquals('veltra-mcp', $initRes['result']['serverInfo']['name']);

        // 2. Tools list
        $toolsRes = $mcp->handleMcpRequest(['id' => 2, 'method' => 'tools/list'], $fs, 'livewire');
        $this->assertNotEmpty($toolsRes['result']['tools']);

        // 3. Tool call detect stack
        $callRes = $mcp->handleMcpRequest([
            'id' => 3,
            'method' => 'tools/call',
            'params' => ['name' => 'veltra_detect_stack'],
        ], $fs, 'react');
        $this->assertStringContainsString('react', $callRes['result']['content'][0]['text']);

        // 4. Tool call security checklist
        $secRes = $mcp->handleMcpRequest([
            'id' => 4,
            'method' => 'tools/call',
            'params' => ['name' => 'veltra_get_security_checklist'],
        ], $fs, 'livewire');
        $this->assertStringContainsString('OWASP Top 10', $secRes['result']['content'][0]['text']);
        $this->assertStringContainsString('broken_access_control', $secRes['result']['content'][0]['text']);

        // 5. Tool call performance guidelines
        $perfRes = $mcp->handleMcpRequest([
            'id' => 5,
            'method' => 'tools/call',
            'params' => ['name' => 'veltra_get_performance_guidelines'],
        ], $fs, 'livewire');
        $this->assertStringContainsString('n_plus_one_elimination', $perfRes['result']['content'][0]['text']);

        // 6. Tool call testing guidelines
        $testRes = $mcp->handleMcpRequest([
            'id' => 6,
            'method' => 'tools/call',
            'params' => ['name' => 'veltra_get_testing_guidelines'],
        ], $fs, 'livewire');
        $this->assertStringContainsString('crud_coverage', $testRes['result']['content'][0]['text']);
    }
}
