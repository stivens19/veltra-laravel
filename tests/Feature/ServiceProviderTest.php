<?php

namespace Veltra\VeltraLaravel\Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\Test;
use Veltra\VeltraLaravel\Facades\Veltra;
use Veltra\VeltraLaravel\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_merges_default_configuration(): void
    {
        $this->assertEquals('Veltra Test', config('veltra.brand.name'));
        $this->assertEquals('compiled', config('veltra.assets.mode'));
        $this->assertTrue(config('veltra.theme.allow_toggle'));
    }

    #[Test]
    public function it_registers_veltra_facade_and_singleton(): void
    {
        $this->assertEquals('Veltra Test', Veltra::brandName());
        $this->assertTrue(Veltra::isCompiledMode());
        $this->assertEquals('1.0.0', Veltra::version());
    }

    #[Test]
    public function it_registers_views_under_veltra_namespace(): void
    {
        $this->assertTrue(View::exists('veltra::layouts.app'));
        $this->assertTrue(View::exists('veltra::layouts.guest'));
        $this->assertTrue(View::exists('veltra::demo.dashboard'));
    }

    #[Test]
    public function it_renders_blade_card_component(): void
    {
        $rendered = Blade::render('<x-veltra::card title="Mi Tarjeta">Hola Mundo</x-veltra::card>');

        $this->assertStringContainsString('vlt-card', $rendered);
        $this->assertStringContainsString('Mi Tarjeta', $rendered);
        $this->assertStringContainsString('Hola Mundo', $rendered);
    }

    #[Test]
    public function it_renders_blade_stat_box_component(): void
    {
        $rendered = Blade::render('<x-veltra::stat-box color="cyan" title="Ventas" value="150" />');

        $this->assertStringContainsString('vlt-stat-box-cyan', $rendered);
        $this->assertStringContainsString('Ventas', $rendered);
        $this->assertStringContainsString('150', $rendered);
    }

    #[Test]
    public function it_renders_button_component_with_variants(): void
    {
        $rendered = Blade::render('<x-veltra::button variant="danger" icon="trash">Eliminar</x-veltra::button>');

        $this->assertStringContainsString('vlt-btn-danger', $rendered);
        $this->assertStringContainsString('Eliminar', $rendered);
        $this->assertStringContainsString('data-lucide="trash"', $rendered);
    }
}
