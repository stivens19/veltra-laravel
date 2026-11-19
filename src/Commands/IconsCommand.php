<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

use function Laravel\Prompts\text;

class IconsCommand extends Command
{
    use InteractsWithVeltraCli;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:icons
                            {search? : Término de búsqueda de iconos (ej: user, cart, chart, settings)}
                            {--category= : Filtrar por categoría (nav, action, finance, status, system)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Buscador interactivo de iconos Lucide optimizados para VeltraUI';

    /**
     * Catálogo curado de iconos Lucide esenciales para aplicaciones empresariales.
     *
     * @var array<string, array{category: string, desc: string}>
     */
    protected array $icons = [
        // Navigation & Dashboard
        'gauge' => ['category' => 'nav', 'desc' => 'Dashboard / Panel de control'],
        'layout-dashboard' => ['category' => 'nav', 'desc' => 'Cuadrícula de dashboard'],
        'layers' => ['category' => 'nav', 'desc' => 'Capas / Módulos generales'],
        'menu' => ['category' => 'nav', 'desc' => 'Menú hamburguesa'],
        'home' => ['category' => 'nav', 'desc' => 'Inicio / Portada'],
        'grid' => ['category' => 'nav', 'desc' => 'Vista de cuadrícula'],
        'list' => ['category' => 'nav', 'desc' => 'Vista de lista'],
        'compass' => ['category' => 'nav', 'desc' => 'Exploración / Rutas'],

        // Actions & CRUD
        'plus' => ['category' => 'action', 'desc' => 'Crear / Agregar nuevo registro'],
        'pencil' => ['category' => 'action', 'desc' => 'Editar / Modificar'],
        'trash-2' => ['category' => 'action', 'desc' => 'Eliminar / Borrar'],
        'search' => ['category' => 'action', 'desc' => 'Buscar / Filtrar'],
        'filter' => ['category' => 'action', 'desc' => 'Filtros avanzados'],
        'download' => ['category' => 'action', 'desc' => 'Descargar / Exportar datos'],
        'upload' => ['category' => 'action', 'desc' => 'Cargar / Importar archivos'],
        'refresh-cw' => ['category' => 'action', 'desc' => 'Actualizar / Sincronizar'],
        'copy' => ['category' => 'action', 'desc' => 'Copiar al portapapeles'],
        'external-link' => ['category' => 'action', 'desc' => 'Enlace externo'],
        'save' => ['category' => 'action', 'desc' => 'Guardar cambios'],
        'eye' => ['category' => 'action', 'desc' => 'Ver detalle / Mostrar'],
        'eye-off' => ['category' => 'action', 'desc' => 'Ocultar contraseña'],
        'more-horizontal' => ['category' => 'action', 'desc' => 'Menú de tres puntos (Acciones)'],

        // Data & Analytics
        'activity' => ['category' => 'analytics', 'desc' => 'Actividad / Rendimiento'],
        'bar-chart-3' => ['category' => 'analytics', 'desc' => 'Gráfico de barras'],
        'line-chart' => ['category' => 'analytics', 'desc' => 'Gráfico de líneas / Tendencias'],
        'pie-chart' => ['category' => 'analytics', 'desc' => 'Gráfico circular / Proporciones'],
        'trending-up' => ['category' => 'analytics', 'desc' => 'Tendencia positiva / Crecimiento'],
        'trending-down' => ['category' => 'analytics', 'desc' => 'Tendencia negativa / Descenso'],
        'table' => ['category' => 'analytics', 'desc' => 'Tabla de datos / Registros'],
        'database' => ['category' => 'analytics', 'desc' => 'Base de datos / Almacenamiento'],

        // Commerce & Finance
        'shopping-cart' => ['category' => 'finance', 'desc' => 'Carrito de compras / POS'],
        'shopping-bag' => ['category' => 'finance', 'desc' => 'Tienda / Catálogo / Productos'],
        'package' => ['category' => 'finance', 'desc' => 'Paquetes / Inventario / Envíos'],
        'dollar-sign' => ['category' => 'finance', 'desc' => 'Dinero / Ingresos'],
        'credit-card' => ['category' => 'finance', 'desc' => 'Pagos / Tarjeta de crédito'],
        'receipt' => ['category' => 'finance', 'desc' => 'Facturas / Comprobantes'],
        'tag' => ['category' => 'finance', 'desc' => 'Descuentos / Categorías'],

        // Users & Roles
        'user' => ['category' => 'users', 'desc' => 'Perfil de usuario individual'],
        'users' => ['category' => 'users', 'desc' => 'Equipo / Lista de clientes'],
        'user-check' => ['category' => 'users', 'desc' => 'Usuario verificado'],
        'user-plus' => ['category' => 'users', 'desc' => 'Invitar / Registrar usuario'],
        'shield-check' => ['category' => 'users', 'desc' => 'Seguridad / Roles & Permisos / 2FA'],
        'key' => ['category' => 'users', 'desc' => 'Contraseña / Credenciales / Passkeys'],
        'lock' => ['category' => 'users', 'desc' => 'Bloqueado / Privado'],
        'unlock' => ['category' => 'users', 'desc' => 'Desbloqueado'],

        // System & Interface
        'settings' => ['category' => 'system', 'desc' => 'Ajustes generales del sistema'],
        'bell' => ['category' => 'system', 'desc' => 'Notificaciones y avisos'],
        'check-circle-2' => ['category' => 'status', 'desc' => 'Éxito / Aprobado (Verde)'],
        'alert-triangle' => ['category' => 'status', 'desc' => 'Advertencia / Atención (Ámbar)'],
        'alert-circle' => ['category' => 'status', 'desc' => 'Error / Peligro (Rojo)'],
        'info' => ['category' => 'status', 'desc' => 'Información (Cyan)'],
        'help-circle' => ['category' => 'system', 'desc' => 'Ayuda / Soporte'],
        'sun-medium' => ['category' => 'system', 'desc' => 'Modo claro'],
        'moon' => ['category' => 'system', 'desc' => 'Modo oscuro'],
        'maximize' => ['category' => 'system', 'desc' => 'Pantalla completa'],
        'kanban' => ['category' => 'system', 'desc' => 'Tablero Kanban / Tareas'],
        'calendar' => ['category' => 'system', 'desc' => 'Calendario / Fechas'],
        'file-text' => ['category' => 'system', 'desc' => 'Documentos / Reportes'],
        'mail' => ['category' => 'system', 'desc' => 'Correo electrónico / Mensajería'],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->displayVeltraBanner('Buscador Interactivo de Iconos Lucide');

        $query = $this->argument('search');
        $category = $this->option('category');

        if (! $query && ! $category && $this->input->isInteractive() && ! app()->runningUnitTests()) {
            if (class_exists(\Laravel\Prompts\Prompt::class)) {
                $query = text(
                    label: '¿Qué icono estás buscando?',
                    placeholder: 'ej: user, cart, check, dollar, chart, settings',
                    hint: 'Deja vacío para ver todos los iconos recomendados.',
                );
            }
        }

        $query = strtolower(trim((string) $query));
        $filtered = [];

        foreach ($this->icons as $name => $meta) {
            // Filtro por categoría
            if ($category && $meta['category'] !== strtolower($category)) {
                continue;
            }

            // Filtro por búsqueda
            if ($query !== '') {
                $matchesName = str_contains($name, $query);
                $matchesDesc = str_contains(strtolower($meta['desc']), $query);
                $matchesCategory = str_contains(strtolower($meta['category']), $query);

                if (! $matchesName && ! $matchesDesc && ! $matchesCategory) {
                    continue;
                }
            }

            $filtered[$name] = $meta;
        }

        if (empty($filtered)) {
            $this->components->warn("No se encontraron iconos para el término [{$query}].");
            $this->line('  <fg=gray>Tip: Consulta el catálogo completo en</> <fg=cyan>https://lucide.dev/icons</>');
            $this->newLine();
            return self::SUCCESS;
        }

        $this->line('  <fg=cyan;options=bold>Resultados Encontrados (' . count($filtered) . ' iconos):</>');
        $this->newLine();

        $rows = [];
        foreach ($filtered as $name => $meta) {
            $rows[] = [
                "<fg=cyan;options=bold>{$name}</>",
                "<fg=yellow>{$meta['category']}</>",
                $meta['desc'],
                "<fg=gray><i data-lucide=\"{$name}\"></i></>",
            ];
        }

        $this->table(['Icono (Lucide)', 'Categoría', 'Descripción', 'Snippet Blade'], $rows);

        $this->newLine();
        $this->line('  <fg=gray>Uso en navegación (config/veltra.php):</> <fg=cyan>\'icon\' => \'' . array_key_first($filtered) . '\'</>');
        $this->newLine();

        return self::SUCCESS;
    }
}
