---
name: veltra-ui
description: >-
  Guía de ingeniería, arquitectura de diseño y estándares de desarrollo para VeltraUI en Laravel.
  Enseña a las IAs a generar vistas, CRUDs, componentes y páginas respetando los tokens de diseño
  corporativo (Cyan #06b6d4, Slate, Dark Mode, rounded-2xl) con distinción ESTRICTA entre los 4
  stacks soportados: Livewire (Blade/Volt/Flux), React + Inertia (TSX), Vue 3 + Inertia (SFC)
  y Svelte + Inertia.
---

# 🛡️ VeltraUI: Manual de Ingeniería & Arquitectura AI-Native

VeltraUI es una suite administrativa empresarial para Laravel inspirada en los estándares de interfaz más limpios y modernos del ecosistema web.

Esta Skill define las directivas estrictas que toda IA o desarrollador debe seguir para diseñar interfaces consistentes, accesibles y de altísima fidelidad visual.

---

## ⛔ 1. Reglas Críticas Anti-Alucinación (Zero-Hallucination Policy)

1. **PROHIBIDO INVENTAR NOMBRES DE COMPONENTES:** Usa únicamente los componentes de VeltraUI estructurados en Atomic Design (`atoms`, `molecules`, `organisms`).
2. **COLOR PRIMARIO INMUTABLE (CYAN):**
   * El color primario de acento es **CYAN (`#06b6d4`)**.
   * Clases Tailwind oficiales: `bg-cyan-500 hover:bg-cyan-600 text-white`, `text-cyan-600 dark:text-cyan-400`, `ring-cyan-500/20`.
   * **NUNCA** uses morado (`indigo` / `purple`), azul genérico ni verde como acento de marca.
3. **SOPORTE NATIVO PARA DARK MODE:** Todo contenedor y elemento debe poseer sus variantes oscuras correspondientes:
   * Fondos: `bg-white dark:bg-slate-900`
   * Paneles/Superficies alternas: `bg-slate-50/50 dark:bg-slate-800/40`
   * Bordes: `border-slate-200/80 dark:border-slate-800`
   * Textos: Principal `text-slate-900 dark:text-white`, Secundario `text-slate-500 dark:text-slate-400`
4. **BORDES Y RADIOS (BORDER-RADIUS):**
   * Cards, contenedores y paneles: `rounded-2xl`
   * Botones, inputs, badges y modales: `rounded-xl`
   * Badges de estado / pills: `rounded-full`
5. **TIPOGRAFÍA:**
   * Textos y etiquetas: `font-sans` (Plus Jakarta Sans).
   * Cifras, códigos, métricas y badges numéricos: `font-mono` (JetBrains Mono).
6. **ICONOGRAFÍA OFICIAL:**
   * Usa exclusivamente iconos de **Lucide Icons**.

---

## 🔀 2. Distinción Estricta entre los 4 Stacks Frontend

Cada stack tiene su propio paradigma de estado, componentes y sintaxis. **NUNCA mezcles sintaxis de stacks diferentes.**

```text
┌────────────────────────────────────────────────────────────────────────┐
│                   MATRIZ DE IMPLEMENTACIÓN POR STACK                   │
├─────────────────┬──────────────────────┬───────────────────────────────┤
│ Stack           │ Ubicación            │ Patrón de Componentes         │
├─────────────────┼──────────────────────┼───────────────────────────────┤
│ Livewire / Blade│ resources/views/     │ <x-veltra::*> + wire:model    │
│ React + Inertia │ resources/js/        │ TSX + Lucide React + useForm  │
│ Vue 3 + Inertia │ resources/js/        │ SFC <script setup> + useForm  │
│ Svelte + Inertia│ resources/js/        │ Svelte 4/5 + Lucide Svelte    │
└─────────────────┴──────────────────────┴───────────────────────────────┘
```

---

### A. Stack: Livewire & Blade Tradicional

* **Ubicación:** `resources/views/`
* **Layout Maestro:** `<x-app-layout>` (Autenticado) y `<x-guest-layout>` (Público/Auth).
* **Prefijo de Componentes:** `<x-veltra::atoms.*>`, `<x-veltra::molecules.*>`, `<x-veltra::organisms.*>`
* **Estado e Interactividad:** Livewire 3 (`wire:model`, `wire:click`) y Alpine.js (`x-data`, `x-show`).

#### Ejemplo Botón & Input:
```blade
<x-app-layout>
    <x-slot name="header">
        <section class="vlt-content-header">
            <h1 class="vlt-page-title">{{ __('Panel') }}</h1>
        </section>
    </x-slot>

    <div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs">
        <input 
            type="text" 
            wire:model.live="search"
            placeholder="Buscar registros..."
            class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 text-slate-800 dark:text-slate-100"
        />

        <button type="button" wire:click="save" class="mt-4 px-4 py-2 text-xs font-semibold text-white bg-cyan-500 hover:bg-cyan-600 rounded-xl transition-colors cursor-pointer inline-flex items-center gap-2">
            <i data-lucide="check" class="w-4 h-4"></i>
            <span>{{ __('Guardar') }}</span>
        </button>
    </div>
</x-app-layout>
```

---

### B. Stack: React + Inertia (TypeScript)

* **Ubicación:** `resources/js/Pages/` y `resources/js/Components/Veltra/`
* **Layout Maestro:** `import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';`
* **Iconos:** `import { Plus, Search, TrendingUp } from 'lucide-react';`
* **Formularios:** `import { useForm, Head } from '@inertiajs/react';`

#### Ejemplo Formulario & Card:
```tsx
import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { Save } from 'lucide-react';

interface Props {
    auth: any;
}

export default function CustomerCreate({ auth }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('customers.store'));
    };

    return (
        <AuthenticatedLayout header={<h1 className="vlt-page-title">Nuevo Cliente</h1>}>
            <Head title="Nuevo Cliente" />

            <div className="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs w-full">
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nombre</label>
                        <input
                            type="text"
                            value={data.name}
                            onChange={e => setData('name', e.target.value)}
                            className="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-cyan-500 text-slate-800 dark:text-slate-100"
                        />
                        {errors.name && <p className="text-[11px] text-rose-500 mt-1">{errors.name}</p>}
                    </div>

                    <button
                        type="submit"
                        disabled={processing}
                        className="px-4 py-2 text-xs font-semibold text-white bg-cyan-500 hover:bg-cyan-600 rounded-xl transition-colors cursor-pointer inline-flex items-center gap-2"
                    >
                        <Save className="w-4 h-4" />
                        <span>Guardar</span>
                    </button>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
```

---

### C. Stack: Vue 3 + Inertia (Composition API)

* **Ubicación:** `resources/js/Pages/` y `resources/js/Components/Veltra/`
* **Layout Maestro:** `import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';`
* **Iconos:** `import { Plus, Save } from 'lucide-vue-next';`
* **Formularios:** `import { useForm, Head } from '@inertiajs/vue3';`

#### Ejemplo Formulario & Card:
```vue
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Save } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
});

const submit = () => {
    form.post(route('customers.store'));
};
</script>

<template>
  <Head title="Nuevo Cliente" />

  <AuthenticatedLayout>
    <template #header>
      <h1 class="vlt-page-title">Nuevo Cliente</h1>
    </template>

    <div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs w-full">
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nombre</label>
          <input
            v-model="form.name"
            type="text"
            class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-cyan-500 text-slate-800 dark:text-slate-100"
          />
          <p v-if="form.errors.name" class="text-[11px] text-rose-500 mt-1">{{ form.errors.name }}</p>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="px-4 py-2 text-xs font-semibold text-white bg-cyan-500 hover:bg-cyan-600 rounded-xl transition-colors cursor-pointer inline-flex items-center gap-2"
        >
          <Save class="w-4 h-4" />
          <span>Guardar</span>
        </button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
```

---

### D. Stack: Svelte + Inertia

* **Ubicación:** `resources/js/Pages/` y `resources/js/Components/Veltra/`
* **Layout Maestro:** `import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.svelte';`
* **Iconos:** `import { Plus, Save } from '@lucide/svelte';`
* **Formularios:** `import { useForm, inertia } from '@inertiajs/svelte';`

#### Ejemplo Formulario & Card:
```svelte
<script lang="ts">
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.svelte';
  import { useForm } from '@inertiajs/svelte';
  import { Save } from '@lucide/svelte';

  let form = useForm({
    name: '',
    email: '',
  });

  function handleSubmit() {
    $form.post('/customers');
  }
</script>

<svelte:head>
  <title>Nuevo Cliente — VeltraUI</title>
</svelte:head>

<AuthenticatedLayout>
  <svelte:fragment slot="header">
    <h1 class="vlt-page-title">Nuevo Cliente</h1>
  </svelte:fragment>

  <div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs w-full">
    <form on:submit|preventDefault={handleSubmit} class="space-y-4">
      <div>
        <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nombre</label>
        <input
          id="name"
          type="text"
          bind:value={$form.name}
          class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-cyan-500 text-slate-800 dark:text-slate-100"
        />
        {#if $form.errors.name}
          <p class="text-[11px] text-rose-500 mt-1">{$form.errors.name}</p>
        {/if}
      </div>

      <button
        type="submit"
        disabled={$form.processing}
        class="px-4 py-2 text-xs font-semibold text-white bg-cyan-500 hover:bg-cyan-600 rounded-xl transition-colors cursor-pointer inline-flex items-center gap-2"
      >
        <svelte:component this={Save} class="w-4 h-4" />
        <span>Guardar</span>
      </button>
    </form>
  </div>
</AuthenticatedLayout>
```

---

## 🤖 3. Guía de Ejecución de Comandos para Agentes de IA

Como modelo de IA, **NO escribas vistas ni CRUDs a mano desde cero si existe un comando oficial de VeltraUI**. Los comandos garantizan el cumplimiento de los estándares de accesibilidad, dark mode y tokens de color.

> [!IMPORTANT]
> **REGLA DE EJECUCIÓN NO INTERACTIVA:** Cuando invoques comandos Artisan en terminales automatizadas, añade **SIEMPRE** `--no-interaction` (o `-n`) y los parámetros con flags (`--template=...`, `--fields=...`, `--force`) para evitar que el comando se quede bloqueado esperando interacción humana.

---

### Mapeo de Intenciones de Usuario a Comandos

#### 1. Creación de Páginas o Vistas
* **Intención del usuario:** *"Crea una página de métricas/pedidos/kanban"*, *"añade una vista llamada Ventas"*.
* **Comando a invocar:**
  ```bash
  # Plantilla analítica (gráficos, KPIs)
  php artisan veltra:page Admin/Analytics --template=analytics --route --menu --no-interaction

  # Plantilla de tabla de datos
  php artisan veltra:page Admin/Orders --template=table --route --menu --no-interaction

  # Plantilla en blanco (Lienzo para construir)
  php artisan veltra:page Admin/Workspace --template=blank --route --no-interaction

  # Plantillas disponibles: blank, table, analytics, kanban, pos, form
  ```

#### 2. Generación de Módulos CRUD
* **Intención del usuario:** *"Crea un CRUD para el modelo Producto con precio, stock y estado"*.
* **Comando a invocar:**
  ```bash
  php artisan veltra:crud Product --fields="name:string,price:number,stock:integer,status:string" --menu --route --no-interaction
  ```
  *(Genera Controller Eloquent paginado con búsqueda, vistas index con `@forelse` y formularios separados create/edit con `old()` y `@error`).*

#### 3. Creación de Componentes Atómicos
* **Intención del usuario:** *"Crea un componente de tarjeta de usuario / botón / estadística"*.
* **Comando a invocar:**
  ```bash
  # Átomo (ej: botón, badge, avatar)
  php artisan veltra:component StatusPill --type=atom --no-interaction

  # Molécula (ej: stat-card, form-field, search)
  php artisan veltra:component UserSummary --type=molecule --no-interaction

  # Organismo (ej: navbar, modal, pricing-table)
  php artisan veltra:component PricingTable --type=organism --no-interaction
  ```

#### 4. Consulta de Iconos Lucide
* **Intención del usuario:** *"¿Qué icono uso para un carrito de compras / candado / factura?"*.
* **Comando a invocar:**
  ```bash
  php artisan veltra:icons cart
  php artisan veltra:icons user
  php artisan veltra:icons settings
  ```

#### 5. Configuración de Marca, Tema y Layout
* **Intención del usuario:** *"Cambia el nombre de la app a Acme y pon modo claro por defecto"*.
* **Comando a invocar:**
  ```bash
  php artisan veltra:theme --name="Acme Corp" --theme=light --layout=fluid --no-interaction
  ```

#### 6. Gestión del Menú Lateral
* **Intención del usuario:** *"Muéstrame qué opciones hay en el menú"*, *"añade una sección al sidebar"*.
* **Comando a invocar:**
  ```bash
  # Ver árbol actual
  php artisan veltra:menu --list

  # Agregar ítem
  php artisan veltra:menu --add --title="Reportes" --url="/reports" --icon="bar-chart-3" --no-interaction
  ```

#### 7. Pase a Producción & Blindaje
* **Intención del usuario:** *"Prepara el proyecto para producción"*, *"optimiza la app"*, *"apaga las demos"*.
* **Comando a invocar:**
  ```bash
  php artisan veltra:build --prod --clean --force --no-interaction
  ```
  *(Desactiva `/veltra/demo`, compila Vite, limpia pruebas, genera cachés de config/routes/views y audita la salud).*

#### 8. Diagnóstico y Salud del Entorno
* **Intención del usuario:** *"Verifica que todo esté bien instalado"*, *"revisa los estilos"*.
* **Comando a invocar:**
  ```bash
  php artisan veltra:doctor
  ```

#### 9. Sincronización de Referencia Anti-Alucinación
* **Intención del usuario:** *"Regenera la referencia de componentes"*, *"actualiza el catálogo"*.
* **Comando a invocar:**
  ```bash
  # Para el stack activo
  php artisan veltra:reference --force --no-interaction

  # Para todos los stacks
  php artisan veltra:reference --stack=all --force --no-interaction
  ```

#### 10. Conexión del Servidor MCP
* Para conectar la IA directamente a herramientas en tiempo real:
  ```bash
  # Diagnóstico del servidor MCP
  php artisan veltra:mcp --ping

  # Servidor en stdio para Cursor/Claude Desktop:
  php artisan veltra:mcp
  ```

---

## 🔐 5. Roles y Permisos (RBAC) en VeltraUI

Cuando el usuario requiera controlar accesos:
1. **En el Menú (`config/veltra.php`):**
   * Agrega `'permission' => 'perm-name'` o `'can' => 'perm-name'` para filtrar por Gates/Policies de Laravel o permisos de Spatie.
   * Agrega `'role' => 'admin'` o `'roles' => ['admin', 'manager']` para filtrar por rol.
   * `Navigation::filterAuthorized()` oculta automáticamente los ítems que el usuario no puede ver. Si un grupo con `children` no tiene ningún hijo autorizado, el grupo padre completo se oculta.
2. **En Vistas Blade / Livewire:** Usa `@can('permission')` o `@role('admin')` alrededor de botones de acción (`<x-veltra::button>`) o tablas.
3. **En Inertia (React / Vue / Svelte):** Consume `auth.permissions` y `auth.roles` provistos en `HandleInertiaRequests`.
4. **En Controladores:** Añade `$this->authorizeResource(Model::class)` o middlewares `can:...` / `role:...`.

---

## 🧭 6. Estándar de Menús y Submenús (`config/veltra.php`)

Todo menú, submenú o acordeón desplegable en el Sidebar se define centralizadamente en `config/veltra.php`:

```php
'navigation' => [
    // Encabezado
    ['header' => 'ADMINISTRACIÓN'],

    // Enlace directo
    [
        'title' => 'Dashboard',
        'icon' => 'gauge',          // Lucide icon
        'url' => '/dashboard',      // o 'route' => 'dashboard'
        'badge' => 'Nuevo',
        'badge_variant' => 'cyan',
    ],

    // Submenú / Acordeón colapsable
    [
        'title' => 'Gestión Comercial',
        'icon' => 'shopping-cart',
        'roles' => ['admin', 'sales'],
        'children' => [
            ['title' => 'Clientes', 'icon' => 'users', 'url' => '/customers'],
            ['title' => 'Cotizaciones', 'icon' => 'file-text', 'url' => '/quotes'],
        ],
    ],
];
```

* **Estado Activo (`bg-cyan-500 !text-white font-bold`):** Se calcula automáticamente con `Navigation::isActive($url)`. Si un hijo está activo, el padre se abre automáticamente (`has_active_child`).
* **Multi-Stack:** Se comparte automáticamente a React, Vue y Svelte vía `Navigation::getTree()` dentro de `HandleInertiaRequests.php` (`props.veltra.navigation`).
