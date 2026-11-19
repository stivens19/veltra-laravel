<?php

namespace Veltra\VeltraLaravel;

class VeltraManager
{
    /**
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * VeltraManager constructor.
     *
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get a configuration value.
     */
    public function config(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Get the brand name.
     */
    public function brandName(): string
    {
        return (string) $this->config('brand.name', 'VeltraUI');
    }

    /**
     * Get the brand logo URL.
     */
    public function logoUrl(): string
    {
        return (string) $this->config('brand.logo', '/vendor/veltra/img/logo.png');
    }

    /**
     * Check if compiled assets mode is active.
     */
    public function isCompiledMode(): bool
    {
        return $this->config('assets.mode', 'compiled') === 'compiled';
    }

    /**
     * Get navigation items.
     *
     * @return array<int, array<string, mixed>>
     */
    public function navigation(): array
    {
        return (array) $this->config('navigation', []);
    }

    /**
     * Get the package version.
     */
    public function version(): string
    {
        return '1.0.0';
    }
}

