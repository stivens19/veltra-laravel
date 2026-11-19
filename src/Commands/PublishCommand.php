<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

class PublishCommand extends Command
{
    use InteractsWithVeltraCli;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:publish
                            {--all : Publicar todos los recursos del paquete}
                            {--assets : Publicar assets compilados (CSS, JS, imágenes)}
                            {--views : Publicar componentes y plantillas Blade}
                            {--errors : Publicar vistas de error HTTP (404, 403, 500, etc.)}
                            {--config : Publicar archivo de configuración config/veltra.php}
                            {--skill : Publicar Skill para IAs (.agents/skills/veltra-ui) y configuración MCP}
                            {--force : Sobrescribir archivos existentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publica selectivamente los recursos, vistas, configuración, assets o Skill AI de VeltraUI';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->displayVeltraBanner('Publicador Selectivo de Recursos');

        $force = (bool) $this->option('force');
        $all = (bool) $this->option('all');
        $assets = (bool) $this->option('assets') || $all;
        $views = (bool) $this->option('views') || $all;
        $errors = (bool) $this->option('errors') || $all;
        $config = (bool) $this->option('config') || $all;
        $skill = (bool) $this->option('skill') || $all;

        // Si no se pasó ninguna opción, preguntar o publicar assets + config
        if (! $assets && ! $views && ! $errors && ! $config && ! $skill) {
            $assets = true;
            $config = true;
            $skill = true;
        }

        $published = [];

        if ($config) {
            $this->call('vendor:publish', [
                '--tag' => 'veltra-config',
                '--force' => $force,
            ]);
            $published['Configuración'] = 'config/veltra.php';
        }

        if ($assets) {
            $this->call('vendor:publish', [
                '--tag' => 'veltra-assets',
                '--force' => $force,
            ]);
            $published['Assets Compilados'] = 'public/vendor/veltra/ (CSS, JS, Logos)';
        }

        if ($views) {
            $this->call('vendor:publish', [
                '--tag' => 'veltra-views',
                '--force' => $force,
            ]);
            $published['Vistas Blade'] = 'resources/views/vendor/veltra/';
        }

        if ($errors) {
            $this->call('vendor:publish', [
                '--tag' => 'veltra-errors',
                '--force' => $force,
            ]);
            $published['Vistas de Error HTTP'] = 'resources/views/errors/ (404, 403, 500, etc.)';
        }

        if ($skill) {
            $this->call('vendor:publish', [
                '--tag' => 'veltra-skill',
                '--force' => $force,
            ]);
            $published['Skill AI & MCP'] = '.agents/skills/veltra-ui/ & .agents/mcp_config.json';
        }

        $this->displaySuccessCard(
            'Recursos Publicados Correctamente',
            $published,
            'Los archivos publicados están listos para ser personalizados en tu proyecto.'
        );

        return self::SUCCESS;
    }
}

