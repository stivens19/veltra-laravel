---
name: veltra-performance
description: >-
  Manual avanzado de optimización de rendimiento y bases de datos para Laravel.
  Instruye a desarrolladores y agentes de IA para eliminar problemas de N+1 consultas,
  optimizar agregaciones en dashboards, implementar capas de caché con Redis, paginación
  escalable (Cursor Pagination) y procesamiento asíncrono con Queues y Jobs.
---

# 🚀 VeltraPerformance: Manual de Optimización & Alto Rendimiento para Laravel

VeltraPerformance establece los estándares de ingeniería para garantizar que las aplicaciones construidas con **Laravel** y la suite **VeltraUI** mantengan tiempos de respuesta inferiores a **100ms**, incluso bajo cargas de millones de registros en bases de datos relacionales (PostgreSQL, MySQL, SQLite).

Este documento es una directiva técnica para desarrolladores y agentes de IA. Toda consulta Eloquent, cálculo de métricas para dashboards o procesamiento de datos debe cumplir con las siguientes reglas de eficiencia.

---

## ⚡ 1. Los 5 Mandamientos del Rendimiento en Laravel

1. **PROHIBIDO EL N+1 EN CONSULTAS:** Toda relación que se consuma en un bucle (`@foreach`, `map`, serializadores JSON) debe precargarse mediante **Eager Loading** (`with()`, `loadMissing()`).
2. **NUNCA CARGUES MODELOS EN MEMORIA PARA CONTAR:** Prohibido `Model::all()->count()` o `Model::get()->sum()`. Usa agregaciones SQL directas en base de datos (`Model::count()`, `Model::sum('amount')`).
3. **PAGINACIÓN OBLIGATORIA EN LISTADOS:** Jamás ejecutes `->get()` o `::all()` sobre tablas que crezcan con el tiempo. Exige `->paginate(15)` o `->cursorPaginate(15)`.
4. **LOS REPORTES PESADOS PERTENECEN A LAS COLAS (QUEUES):** Exportaciones Excel/CSV, generación de PDFs, sincronizaciones con APIs externas y envíos masivos deben ejecutarse en segundo plano con `ShouldQueue`.
5. **CACHEA RESULTADOS COSTOSOS:** Los KPIs y métricas del dashboard principal deben almacenarse en caché con TTL (*Time-To-Live*) e invalidación atómica.

---

## 🔍 2. Detección y Eliminación del Problema N+1

El problema de consultas N+1 es la causa #1 de degradación del rendimiento en Laravel.

### Activación de Prevención Estricta en Desarrollo
En `app/Providers/AppServiceProvider.php` (o `bootstrap/app.php`):
```php
use Illuminate\Database\Eloquent\Model;

public function boot(): void
{
    // Lanza una excepción si se detecta N+1 en desarrollo o pruebas
    Model::preventLazyLoading(! $this->app->isProduction());

    // Alerta silenciosa en logs si ocurre en producción
    Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
        logger()->warning("N+1 detectado en modelo [".get_class($model)."] relación [{$relation}]");
    });
}
```

### ❌ Código Ineficiente (N+1 Query Explosion):
```php
// En el Controlador:
$orders = Order::latest()->paginate(10); // 1 consulta

// En la Vista Blade:
@foreach($orders as $order)
    {{-- Genera 1 consulta adicional por CADA fila para obtener el cliente --}}
    <td>{{ $order->customer->name }}</td>
    {{-- Genera OTRA consulta para contar los ítems --}}
    <td>{{ $order->items->count() }}</td>
@endforeach
{{-- Total: 1 + 10 + 10 = 21 consultas SQL para solo 10 filas --}}
```

### ✅ Código Optimizado (Eager Loading & withCount):
```php
// En el Controlador:
$orders = Order::query()
    ->with(['customer:id,name,email']) // Precarga solo las columnas necesarias
    ->withCount('items')                // Agrega items_count en la MISMA consulta SQL
    ->latest()
    ->paginate(10);

// En la Vista Blade:
@foreach($orders as $order)
    <td>{{ $order->customer->name }}</td>
    <td>{{ $order->items_count }}</td> {{-- Acceso instantáneo en memoria --}}
@endforeach
{{-- Total: Solo 2 consultas SQL fijas, sin importar si son 10 o 1,000 registros --}}
```

---

## 📊 3. Optimización de Dashboards y Tarjetas de Métricas (`StatCard`)

Un error clásico al construir dashboards es cargar miles de modelos Eloquent para calcular simples totales.

### ❌ Consumo Excesivo de Memoria RAM:
```php
// PELIGRO: Carga 50,000 modelos Eloquent hidratados en la memoria de PHP
$totalRevenue = Order::where('status', 'completed')->get()->sum('total');
$customerCount = Customer::all()->count();
```

### ✅ Agregación Directa en el Motor SQL:
```php
// Excelente: PHP consume 0MB de memoria extra, el motor de BD resuelve en 2ms
$totalRevenue = Order::where('status', 'completed')->sum('total');
$customerCount = Customer::count();

// Agregación múltiple en una única consulta SQL:
$metrics = Order::query()
    ->where('created_at', '>=', now()->subDays(30))
    ->selectRaw('
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = "completed" THEN total ELSE 0 END) as revenue,
        AVG(CASE WHEN status = "completed" THEN total ELSE NULL END) as avg_ticket
    ')
    ->first();
```

---

## 🗄️ 4. Paginación a Escala: `paginate()` vs `cursorPaginate()`

Cuando una tabla tiene más de 100,000 registros, la paginación estándar con `OFFSET` (`LIMIT 15 OFFSET 50000`) se vuelve progresivamente lenta porque el motor de base de datos debe escanear y descartar miles de filas.

```
┌──────────────────┬─────────────────────────────┬─────────────────────────────┐
│ Tipo Paginación  │ Método Eloquent             │ Mejor Caso de Uso           │
├──────────────────┼─────────────────────────────┼─────────────────────────────┤
│ Clásica          │ ->paginate(15)              │ Tablas con salto a páginas  │
│ Simple           │ ->simplePaginate(15)        │ Solo "Anterior" y "Siguiente"│
│ Cursor (Ultra)   │ ->cursorPaginate(15)        │ Feeds infinitos, millones de│
│                  │                             │ filas, exportaciones masivas│
└──────────────────┴─────────────────────────────┴─────────────────────────────┘
```

```php
// Para listados masivos de alta frecuencia (ej: logs, transacciones, auditoría):
$logs = AuditLog::latest('id')->cursorPaginate(25);
```

### Procesamiento de Lotes Grandes sin Agotar Memoria
Nunca uses `->get()` para procesar miles de registros en un comando o Job. Usa `chunkById()`:
```php
// ✅ Procesa 500 registros a la vez liberando memoria tras cada lote
Customer::where('is_active', false)->chunkById(500, function ($customers) {
    foreach ($customers as $customer) {
        $customer->anonymize();
    }
});
```

---

## ⚡ 5. Estrategia de Caché con Redis & Tags

Las consultas del Dashboard que no cambian cada segundo deben cachearse con una clave predecible y TTL:

```php
use Illuminate\Support\Facades\Cache;

public function getDashboardStats(): array
{
    // Retorna desde Redis si existe; si no, calcula y almacena por 10 minutos (600s)
    return Cache::remember('veltra.dashboard.stats', 600, function () {
        return [
            'total_sales' => Order::where('status', 'completed')->sum('total'),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
        ];
    });
}
```

### Invalidación Atómica con Observers o Eventos:
Al crearse o modificarse una orden, invalida inmediatamente la clave de caché:
```php
// app/Observers/OrderObserver.php
namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    public function saved(Order $order): void
    {
        Cache::forget('veltra.dashboard.stats');
    }
}
```

---

## 🧵 6. Procesamiento Asíncrono con Queues (Segundo Plano)

Cualquier operación que tome más de **200ms** debe delegarse a una cola de trabajo (*Queue Worker*):
- Envío de correos de confirmación / bienvenida.
- Notificaciones push o webhooks a terceros.
- Generación de reportes PDF o Excel.
- Procesamiento y redimensionamiento de imágenes cargadas.

### Estructura de un Job Optimizado:
```php
namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMonthlyReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120; // Tiempo máximo en segundos
    public int $tries = 3;     // Reintentos máximos

    public function __construct(public User $user, public string $month)
    {
        // SerializesModels guarda solo el ID del modelo para ahorrar espacio en la cola
    }

    public function handle(): void
    {
        // Lógica pesada de generación
    }
}
```

---

## 🗃️ 7. Índices de Base de Datos Indispensables

En las migraciones generadas, **siempre indexa**:
1. Todas las llaves foráneas (`foreignId('customer_id')->index()`).
2. Columnas frecuentemente filtradas en cláusulas `where` (`status`, `is_active`, `type`).
3. Columnas de ordenamiento temporal (`created_at`, `published_at`).
4. Índices compuestos para consultas combinadas habituales:
   ```php
   $table->index(['tenant_id', 'status', 'created_at']);
   ```

---

## 📋 Checklist de Rendimiento para IAs y Desarrolladores

Antes de finalizar cualquier vista, componente, controlador o endpoint:
- [ ] ¿Se eliminaron todas las consultas N+1 usando `with(['relation'])` o `withCount()`?
- [ ] ¿Las métricas y estadísticas del dashboard usan agregaciones SQL (`sum()`, `count()`) en lugar de colecciones de Eloquent?
- [ ] ¿Todas las listas y tablas de datos implementan `paginate()` o `cursorPaginate()`?
- [ ] ¿Los reportes pesados o exportaciones se delegan a un Job en cola con `ShouldQueue`?
- [ ] ¿Las consultas frecuentes del dashboard están protegidas con `Cache::remember()`?
- [ ] ¿Tienen índices de base de datos las columnas utilizadas en filtros de búsqueda?
