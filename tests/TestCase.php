<?php

namespace Veltra\VeltraLaravel\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Veltra\VeltraLaravel\VeltraLaravelServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            VeltraLaravelServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('veltra.brand.name', 'Veltra Test');
        $app['config']->set('veltra.routes.enabled', true);
        $app['config']->set('app.key', 'base64:6Cu/4L06D8X+nF3T/3jO9m+Wq5N5Z8Q3b8U9d1K1L0M=');
    }
}

