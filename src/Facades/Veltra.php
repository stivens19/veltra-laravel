<?php

namespace Veltra\VeltraLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed config(string $key, mixed $default = null)
 * @method static string brandName()
 * @method static string logoUrl()
 * @method static bool isCompiledMode()
 * @method static array navigation()
 * @method static string version()
 *
 * @see \Veltra\VeltraLaravel\VeltraManager
 */
class Veltra extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'veltra';
    }
}

