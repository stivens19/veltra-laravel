<?php

namespace Veltra\VeltraLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Veltra\VeltraLaravel\Commands\Concerns\InteractsWithVeltraCli;

class McpCommand extends Command
{
    use InteractsWithVeltraCli;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'veltra:mcp
                            {--ping : Probar el estado del servidor MCP y listar capacidades sin bloquear stdio}
                            {--test : Ejecutar autoprueba de herramientas MCP}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia el servidor Model Context Protocol (MCP) de VeltraUI sobre JSON-RPC/stdio para IAs';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $stack = $this->determineStack($filesystem);

        if ($this->option('ping') || $this->option('test') || app()->runningUnitTests()) {
            $this->displayVeltraBanner('Servidor Model Context Protocol (MCP)', $stack);

            $tools = $this->getToolsDefinitions();

            $this->displaySuccessCard(
                'Servidor MCP VeltraUI Operativo (JSON-RPC 2.0)',
                [
                    'Protocolo' => 'Model Context Protocol (MCP stdio)',
                    'Stack Activo' => "<fg=yellow;options=bold>[" . strtoupper($stack) . "]</>",
                    'Herramientas Registradas' => count($tools) . ' Tools listas para agentes',
                    'Integración Cursor / Claude' => 'php artisan veltra:mcp',
                ],
                'Para conectar este MCP a Cursor o Claude Desktop, añade el comando "php artisan veltra:mcp" a tu configuración mcpServers.'
            );

            return self::SUCCESS;
        }

        // Bucle principal JSON-RPC sobre STDIN / STDOUT
        $stdin = fopen('php://stdin', 'r');
        $stdout = fopen('php://stdout', 'w');

        if (! $stdin || ! $stdout) {
            $this->error('No se pudo abrir el canal stdio para MCP.');
            return self::FAILURE;
        }

        while (! feof($stdin)) {
            $line = fgets($stdin);
            if ($line === false || trim($line) === '') {
                continue;
            }

            $request = json_decode($line, true);
            if (! is_array($request)) {
                continue;
            }

            $response = $this->handleMcpRequest($request, $filesystem, $stack);
            if ($response !== null) {
                fwrite($stdout, json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
                fflush($stdout);
            }
        }

        fclose($stdin);
        fclose($stdout);

        return self::SUCCESS;
    }

    /**
     * Procesa una petición individual JSON-RPC MCP.
     */
    public function handleMcpRequest(array $request, Filesystem $filesystem, string $stack): ?array
    {
        $id = $request['id'] ?? null;
        $method = $request['method'] ?? '';
        $params = $request['params'] ?? [];

        // Notificaciones sin ID que no requieren respuesta
        if ($id === null && str_starts_with($method, 'notifications/')) {
            return null;
        }

        return match ($method) {
            'initialize' => [
                'jsonrpc' => '2.0',
                'id' => $id,
                'result' => [
                    'protocolVersion' => '2024-11-05',
                    'serverInfo' => [
                        'name' => 'veltra-mcp',
                        'version' => '1.0.0',
                    ],
                    'capabilities' => [
                        'tools' => (object) [],
                        'resources' => (object) [],
                    ],
                ],
            ],

            'tools/list' => [
                'jsonrpc' => '2.0',
                'id' => $id,
                'result' => [
                    'tools' => $this->getToolsDefinitions(),
                ],
            ],

            'tools/call' => [
                'jsonrpc' => '2.0',
                'id' => $id,
                'result' => $this->executeToolCall($params['name'] ?? '', $params['arguments'] ?? [], $filesystem, $stack),
            ],

            'resources/list' => [
                'jsonrpc' => '2.0',
                'id' => $id,
                'result' => [
                    'resources' => [
                        [
                            'uri' => 'veltra://tokens',
                            'name' => 'Design Tokens',
                            'mimeType' => 'application/json',
                        ],
                        [
                            'uri' => 'veltra://reference',
                            'name' => 'AI Reference Guide',
                            'mimeType' => 'text/markdown',
                        ],
                    ],
                ],
            ],

            'resources/read' => [
                'jsonrpc' => '2.0',
                'id' => $id,
                'result' => [
                    'contents' => [
                        [
                            'uri' => $params['uri'] ?? 'veltra://tokens',
                            'mimeType' => 'application/json',
                            'text' => json_encode($this->getDesignTokens($stack), JSON_PRETTY_PRINT),
                        ],
                    ],
                ],
            ],

            default => [
                'jsonrpc' => '2.0',
                'id' => $id,
                'error' => [
                    'code' => -32601,
                    'message' => "Método MCP no encontrado: [{$method}]",
                ],
            ],
        };
    }

    /**
     * Definiciones de herramientas para tools/list.
     */
    public function getToolsDefinitions(): array
    {
        return [
            [
                'name' => 'veltra_get_component',
                'description' => 'Obtiene el código fuente, props y directivas de un componente VeltraUI distinguiendo con precisión el stack solicitado (livewire, react, vue, svelte).',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'description' => 'Nombre del componente (Button, Badge, Input, StatCard, FormField, Card, Table)'],
                        'stack' => ['type' => 'string', 'enum' => ['livewire', 'blade', 'react', 'vue', 'svelte'], 'description' => 'Stack frontend objetivo'],
                    ],
                    'required' => ['name'],
                ],
            ],
            [
                'name' => 'veltra_search_icons',
                'description' => 'Busca iconos empresariales Lucide por término clave (user, cart, check, settings, chart, etc.) y devuelve el snippet Blade/JSX.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Término de búsqueda de icono'],
                    ],
                    'required' => ['query'],
                ],
            ],
            [
                'name' => 'veltra_get_design_tokens',
                'description' => 'Devuelve los tokens de diseño oficiales de VeltraUI (Color Cyan primario #06b6d4, Dark mode, Border-radius, Tipografías) para que la IA no alucine estilos.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
            [
                'name' => 'veltra_detect_stack',
                'description' => 'Detecta y confirma el stack activo del proyecto actual (Livewire, React, Vue, Svelte o Blade).',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
            [
                'name' => 'veltra_list_components',
                'description' => 'Lista todos los componentes disponibles en VeltraUI organizados bajo Atomic Design (Átomos, Moléculas, Organismos).',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
            [
                'name' => 'veltra_get_security_checklist',
                'description' => 'Devuelve el checklist de ciberseguridad industrial OWASP Top 10 para Laravel (prevención de IDOR, Mass Assignment, XSS, CSRF, encriptación, y blindaje de controladores).',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
            [
                'name' => 'veltra_get_performance_guidelines',
                'description' => 'Devuelve las reglas de alto rendimiento para Laravel (prevención N+1, agregaciones directas SQL para dashboards, paginación con cursor, y caché con Redis).',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
            [
                'name' => 'veltra_get_testing_guidelines',
                'description' => 'Devuelve las pautas y plantillas para generación de pruebas automatizadas con Pest y PHPUnit (tests de CRUDs, verificación RBAC, Fakes de Storage/Mail/Queue).',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
        ];
    }

    /**
     * Ejecuta una llamada a una herramienta MCP.
     */
    protected function executeToolCall(string $tool, array $args, Filesystem $filesystem, string $detectedStack): array
    {
        return match ($tool) {
            'veltra_detect_stack' => [
                'content' => [
                    [
                        'type' => 'text',
                        'text' => json_encode([
                            'active_stack' => $detectedStack,
                            'recommendation' => match ($detectedStack) {
                                'react' => 'Usa componentes TSX con @/Components/Veltra/ y Lucide React.',
                                'vue' => 'Usa SFC Vue 3 con <script setup lang="ts"> y Lucide Vue.',
                                'svelte' => 'Usa componentes Svelte con Lucide Svelte.',
                                default => 'Usa componentes Blade con <x-veltra::*> y Alpine.js.',
                            },
                        ], JSON_PRETTY_PRINT),
                    ],
                ],
            ],

            'veltra_get_design_tokens' => [
                'content' => [
                    [
                        'type' => 'text',
                        'text' => json_encode($this->getDesignTokens($detectedStack), JSON_PRETTY_PRINT),
                    ],
                ],
            ],

            'veltra_list_components' => [
                'content' => [
                    [
                        'type' => 'text',
                        'text' => json_encode([
                            'atoms' => ['Button', 'Badge', 'Input', 'ThemeToggle', 'Avatar', 'BrandLogo'],
                            'molecules' => ['StatCard', 'FormField', 'SearchInput', 'UserPanel', 'NavItem'],
                            'organisms' => ['Card', 'Table', 'Modal', 'Alert', 'Sidebar', 'Header'],
                        ], JSON_PRETTY_PRINT),
                    ],
                ],
            ],

            'veltra_search_icons' => [
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $this->searchIcons($args['query'] ?? ''),
                    ],
                ],
            ],

            'veltra_get_component' => [
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $this->getComponentSource($args['name'] ?? 'Button', $args['stack'] ?? $detectedStack),
                    ],
                ],
            ],

            'veltra_get_security_checklist' => [
                'content' => [
                    [
                        'type' => 'text',
                        'text' => json_encode($this->getSecurityChecklist(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    ],
                ],
            ],

            'veltra_get_performance_guidelines' => [
                'content' => [
                    [
                        'type' => 'text',
                        'text' => json_encode($this->getPerformanceGuidelines(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    ],
                ],
            ],

            'veltra_get_testing_guidelines' => [
                'content' => [
                    [
                        'type' => 'text',
                        'text' => json_encode($this->getTestingGuidelines(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    ],
                ],
            ],

            default => [
                'isError' => true,
                'content' => [
                    [
                        'type' => 'text',
                        'text' => "Herramienta desconocida: [{$tool}]",
                    ],
                ],
            ],
        };
    }

    /**
     * Retorna tokens de diseño.
     */
    protected function getDesignTokens(string $stack): array
    {
        return [
            'brand' => [
                'name' => config('veltra.brand.name', 'VeltraUI'),
                'primary_accent' => '#06b6d4',
                'tailwind_accent' => 'cyan-500',
                'active_stack' => $stack,
            ],
            'theme' => [
                'default' => config('veltra.theme.default', 'light'),
                'dark_mode_class' => 'dark',
                'dark_bg' => 'bg-slate-900',
                'light_bg' => 'bg-white',
            ],
            'radii' => [
                'cards' => 'rounded-2xl',
                'buttons_inputs' => 'rounded-xl',
                'pills' => 'rounded-full',
            ],
            'typography' => [
                'body' => 'Plus Jakarta Sans',
                'mono' => 'JetBrains Mono',
            ],
        ];
    }

    /**
     * Busca iconos.
     */
    protected function searchIcons(string $query): string
    {
        $icons = [
            'plus' => 'Crear / Agregar',
            'pencil' => 'Editar',
            'trash-2' => 'Eliminar',
            'search' => 'Buscar',
            'filter' => 'Filtros',
            'check' => 'Aprobado / Éxito',
            'alert-triangle' => 'Advertencia',
            'gauge' => 'Dashboard',
            'shopping-cart' => 'Ventas / POS',
            'user' => 'Usuario / Perfil',
            'shield-check' => 'Seguridad / 2FA',
        ];

        $matched = [];
        foreach ($icons as $icon => $desc) {
            if ($query === '' || str_contains($icon, strtolower($query)) || str_contains(strtolower($desc), strtolower($query))) {
                $matched[$icon] = [
                    'desc' => $desc,
                    'blade_snippet' => "<i data-lucide=\"{$icon}\"></i>",
                    'react_snippet' => "<Icon name=\"{$icon}\" />",
                ];
            }
        }

        return json_encode($matched, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Obtiene código de componente según stack.
     */
    protected function getComponentSource(string $name, string $stack): string
    {
        $stk = match (strtolower($stack)) {
            'react' => 'react',
            'vue' => 'vue',
            'svelte' => 'svelte',
            default => 'blade',
        };

        return "Componente [{$name}] para Stack [{$stk}]: Consulta las implementaciones vivas en resources/veltra/components/{$stk}/ o en la guía resources/veltra/AI_REFERENCE.md.";
    }

    /**
     * Retorna el checklist de ciberseguridad industrial OWASP Top 10 para Laravel.
     *
     * @return array<string, mixed>
     */
    protected function getSecurityChecklist(): array
    {
        return [
            'standards' => 'OWASP Top 10 (2021/2025) & Laravel Hardening Protocol',
            'philosophy' => 'Zero-Trust, Principle of Least Privilege (PoLP), Fail-Safe Defaults',
            'threat_matrix' => [
                'A01_broken_access_control' => [
                    'rule' => 'Prevenir IDOR y Mass Assignment en todo controlador',
                    'actions' => [
                        'Usar siempre FormRequest dedicado y $request->validated() (PROHIBIDO $request->all())',
                        'Verificar propiedad/tenant en búsquedas: $user->orders()->findOrFail($id)',
                        'Implementar Policies con $this->authorize() o $this->authorizeResource()',
                    ],
                ],
                'A02_cryptographic_failures' => [
                    'rule' => 'Cifrado en reposo y tránsito de secretos',
                    'actions' => [
                        'Ocultar atributos en Eloquent: $hidden = [\'password\', \'remember_token\', \'two_factor_secret\', \'stripe_id\']',
                        'Casting de atributos sensibles: \'api_token\' => \'encrypted\'',
                        'Hashing nativo seguro: Hash::make() o casting \'password\' => \'hashed\'',
                    ],
                ],
                'A03_injection' => [
                    'rule' => 'Protección contra SQL Injection, XSS y Command Injection',
                    'actions' => [
                        'Prohibido concatenar variables en DB::raw(); usar PDO bindings ?',
                        'En Blade: usar siempre {{ $var }} (escapado); nunca {!! !!} con datos de usuario',
                        'En CSV exports: anteponer \' a celdas que inicien con =, +, -, @',
                    ],
                ],
                'A04_insecure_design' => [
                    'rule' => 'Validación exhaustiva de lógica de negocio',
                    'actions' => [
                        'Validar estados con Rule::in([\'pending\', \'completed\', \'cancelled\'])',
                        'Prevenir transiciones de estado imposibles o hacia atrás',
                    ],
                ],
                'A05_security_misconfiguration' => [
                    'rule' => 'Hardening de entorno y cabeceras HTTP',
                    'actions' => [
                        'Producción: APP_DEBUG=false y VELTRA_ROUTES_ENABLED=false (ejecutar veltra:build)',
                        'Cabeceras de seguridad: X-Frame-Options: SAMEORIGIN, X-Content-Type-Options: nosniff',
                    ],
                ],
                'A07_authentication_failures' => [
                    'rule' => 'Protección contra fuerza bruta y robo de sesiones',
                    'actions' => [
                        'Regenerar sesión tras login: $request->session()->regenerate()',
                        'Rate limiting en endpoints de autenticación: Limit::perMinute(5)',
                        'Reglas de contraseñas: Password::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised()',
                    ],
                ],
                'A08_software_data_integrity' => [
                    'rule' => 'Subida segura de archivos y prevención de RCE',
                    'actions' => [
                        'Validar Magic Bytes / MIME real (mimetypes:image/png,image/jpeg), no solo mimes',
                        'Guardar con nombre hash aleatorio ($file->hashName()) en disco privado',
                        'Prohibir subida de SVG público sin sanitización previa de scripts',
                    ],
                ],
                'A10_ssrf' => [
                    'rule' => 'Server-Side Request Forgery en peticiones salientes',
                    'actions' => [
                        'Bloquear rangos de IP privadas (10.0.0.0/8, 192.168.0.0/16, 127.0.0.1, 169.254.169.254)',
                    ],
                ],
            ],
            'full_manual' => '.agents/skills/veltra-security/SKILL.md',
        ];
    }

    /**
     * Retorna las directivas de optimización de rendimiento y base de datos.
     *
     * @return array<string, mixed>
     */
    protected function getPerformanceGuidelines(): array
    {
        return [
            'standards' => 'High-Throughput Laravel & Clean Query Execution',
            'core_mandates' => [
                'n_plus_one_elimination' => 'Usar siempre with() o loadMissing() para relaciones consumidas en loops. Usar withCount() para métricas.',
                'database_aggregations' => 'PROHIBIDO Model::all()->count() o sum(). Usar siempre agregaciones SQL directas: Model::count(), Model::sum("amount").',
                'pagination_strategy' => 'Usar ->paginate(15) para vistas estándar o ->cursorPaginate(15) para feeds masivos (>100k registros).',
                'chunking_memory' => 'Para procesar miles de registros en comandos o jobs, usar chunkById(500).',
                'caching_layer' => 'Cachear métricas de dashboard con Cache::remember("key", 600, fn() => ...) e invalidar en Observers.',
                'queues_for_slow_tasks' => 'Reportes PDF, exports Excel y correos masivos deben despacharse en Jobs con ShouldQueue.',
            ],
            'full_manual' => '.agents/skills/veltra-performance/SKILL.md',
        ];
    }

    /**
     * Retorna las directivas para generación de pruebas automatizadas con Pest y PHPUnit.
     *
     * @return array<string, mixed>
     */
    protected function getTestingGuidelines(): array
    {
        return [
            'standards' => 'Pest PHP & PHPUnit 10/11 Production Quality Standard',
            'core_mandates' => [
                'crud_coverage' => 'Probar los 5 métodos: index (listado + búsqueda), store (validación 422 + éxito 302), show, update, destroy.',
                'sad_path_validation' => 'Verificar assertSessionHasErrors(["field"]) cuando el payload sea inválido.',
                'authorization_rbac' => 'Asegurar que usuarios no autorizados reciban assertForbidden() y no logren mutar datos.',
                'anti_idor' => 'Verificar que un usuario no pueda ver ni editar registros de otro usuario/tenant.',
                'service_fakes' => 'Usar siempre Storage::fake(), Queue::fake(), Mail::fake(), Http::fake().',
            ],
            'full_manual' => '.agents/skills/veltra-testing/SKILL.md',
        ];
    }
}
