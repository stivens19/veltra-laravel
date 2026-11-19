# VeltraLaravel — Enterprise Admin UI Suite para Laravel

<p align="left">
  <img src="https://img.shields.io/badge/Laravel-10_%7C_11_%7C_12_%7C_13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 10, 11, 12 y 13" />
  <img src="https://img.shields.io/badge/PHP-8.2_%7C_8.3_%7C_8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4_Nativo-06b6d4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS v4" />
  <img src="https://img.shields.io/badge/Multi--Stack-Livewire_%7C_React_%7C_Vue_%7C_Svelte-10b981?style=for-the-badge" alt="Multi-Stack" />
  <img src="https://img.shields.io/badge/AI--Native-Skill_&_MCP_Protocol-8b5cf6?style=for-the-badge" alt="AI Native" />
  <img src="https://img.shields.io/badge/Tests-100%25_Passing-brightgreen?style=for-the-badge" alt="Tests 100% Passing" />
  <img src="https://img.shields.io/badge/License-MIT-3b82f6?style=for-the-badge" alt="MIT License" />
</p>

---

## 📑 Tabla de Contenidos

- [🌟 Introducción](#-introducción)
- [✨ Características Principales](#-características-principales)
- [📋 Requisitos del Sistema](#-requisitos-del-sistema)
- [⚡ Instalación y Primeros Pasos](#-instalación-y-primeros-pasos)
  - [1. Instalación vía Composer](#1-instalación-vía-composer)
  - [2. Asistente Inteligente de Instalación](#2-asistente-inteligente-de-instalación)
  - [3. Instalación Silenciosa / No Interactiva](#3-instalación-silenciosa--no-interactiva)
- [🔀 Arquitectura Multi-Stack Estricta](#-arquitectura-multi-stack-estricta)
  - [Livewire 3 (Blade / Volt / Flux)](#1-livewire-3-blade-tradicional-volt-y-flux)
  - [React + Inertia (TSX)](#2-react--inertia-typescript--tsx)
  - [Vue 3 + Inertia (SFC)](#3-vue-3--inertia-composition-api--sfc)
  - [Svelte + Inertia](#4-svelte--inertia-svelte-45)
  - [Proyectos Limpios (Vanilla Blade + Auth Nativo)](#5-proyectos-limpios-clean--vanilla-blade)
- [🧱 Catálogo de Componentes Atomic Design](#-catálogo-de-componentes-atomic-design)
  - [Átomos](#átomos)
  - [Moléculas](#moléculas)
  - [Organismos](#organismos)
- [🛠️ Referencia Completa de Comandos Artisan](#️-referencia-completa-de-comandos-artisan)
  - [veltra:install](#1-veltrainstall--instalación-y-configuración-del-stack)
  - [veltra:page](#2-veltrapage--generador-de-páginas-con-plantillas)
  - [veltra:crud](#3-veltracrud--generador-de-cruds-eloquent-productivos)
  - [veltra:component](#4-veltracomponent--generador-atomic-design)
  - [veltra:reference](#5-veltrareference--catálogo-y-cheatsheet-anti-alucinación)
  - [veltra:build](#6-veltrabuild--preparador-y-blindaje-para-producción)
  - [veltra:theme](#7-veltratheme--personalizador-de-marca-tema-y-layout)
  - [veltra:menu](#8-veltramenu--administrador-del-sidebar)
  - [veltra:icons](#9-veltraicons--buscador-interactivo-de-iconos-lucide)
  - [veltra:doctor](#10-veltradoctor--auditoría-de-salud-del-entorno)
  - [veltra:demo](#11-veltrademo--vitrina-interactiva-de-componentes)
  - [veltra:auth](#12-veltraauth--scaffolding-completo-de-autenticación)
  - [veltra:skill](#13-veltraskill--instalador-de-skills-para-agentes-ia)
  - [veltra:mcp](#14-veltramcp--servidor-nativo-model-context-protocol)
  - [veltra:publish](#15-veltrapublish--publicador-selectivo-de-recursos)
- [🤖 Integración AI-First & Servidor MCP](#-integración-ai-first--servidor-mcp)
  - [¿Qué es el Protocolo MCP en VeltraUI?](#qué-es-el-protocolo-mcp-en-veltraui)
  - [Configuración en Editores (Cursor, Windsurf, Claude Desktop)](#configuración-en-editores)
  - [Herramientas Disponibles en el Servidor MCP](#herramientas-disponibles-en-el-servidor-mcp)
  - [Skill Oficial para Agentes de Código](#skill-oficial-para-agentes-de-código)
- [⚙️ Configuración y Variables de Entorno](#️-configuración-y-variables-de-entorno)
- [🧭 Sistema de Navegación, Menús y Submenús](#-sistema-de-navegación-menús-y-submenús)
  - [1. Estructura Declarativa en `config/veltra.php`](#1-estructura-declarativa-en-configveltraphp)
  - [2. Submenús Multinivel y Grupos Colapsables](#2-submenús-multinivel-y-grupos-colapsables)
  - [3. Detección Inteligente de Estado Activo y Sincronización](#3-detección-inteligente-de-estado-activo-y-sincronización)
  - [4. Resolución de Rutas Dinámicas y Fallbacks](#4-resolución-de-rutas-dinámicas-y-fallbacks)
  - [5. Consumo Multi-Stack (Blade, React, Vue, Svelte)](#5-consumo-multi-stack-blade-react-vue-svelte)
  - [6. Comandos Artisan y Automatización de Menús](#6-comandos-artisan-y-automatización-de-menús)
- [🔐 Manejo de Roles y Permisos (RBAC)](#-manejo-de-roles-y-permisos-rbac)
  - [1. Filtrado Automático en el Menú Lateral](#1-filtrado-automático-en-el-menú-lateral)
  - [2. Visualización Dinámica del Rol de Usuario](#2-visualización-dinámica-del-rol-de-usuario)
  - [3. En Vistas Blade y Livewire](#3-en-vistas-blade-y-livewire)
  - [4. En Inertia (React, Vue, Svelte)](#4-en-inertia-react-vue-svelte)
  - [5. En Rutas y Controladores](#5-en-rutas-y-controladores)
- [🎨 Tokens de Diseño Corporativo](#-tokens-de-diseño-corporativo)
- [🚀 Despliegue a Producción](#-despliegue-a-producción)
- [🧪 Pruebas Automatizadas (Testing Suite)](#-pruebas-automatizadas-testing-suite)
- [📄 Licencia](#-licencia)

---

## 🌟 Introducción

**VeltraLaravel** es la suite de interfaz administrativa empresarial más avanzada para el ecosistema **Laravel** (compatible con Laravel 10, 11, 12 y 13). Construida desde sus cimientos bajo los principios de **Atomic Design**, ofrece un lenguaje visual de alto impacto con acento corporativo en **Cyan (`#06b6d4`)**, modo oscuro nativo con tecnología anti-parpadeo (*Zero-FOUC*) y una arquitectura **AI-First** nativa mediante **Skills** y un servidor **Model Context Protocol (MCP)** embebido.

A diferencia de plantillas administrativas tradicionales que sobrecargan tu repositorio con gigabytes de dependencias innecesarias o mezclan sintaxis arbitrariamente, **VeltraLaravel detecta y se adapta estrictamente al frontend de tu proyecto**:

* Si trabajas con **Livewire 3**, obtienes componentes Blade puros, Volt, Flux y Alpine.js.
* Si trabajas con **React**, obtienes componentes tipados con TypeScript (`.tsx`) e Inertia.js.
* Si trabajas con **Vue 3**, obtienes Single File Components (`.vue`) con `<script setup>`.
* Si trabajas con **Svelte**, obtienes componentes limpios y reactivos con Svelte + Inertia.
* Si inicias un **proyecto limpio** sin Starter Kit, VeltraLaravel proporciona un sistema de autenticación completo (Login, Registro, Recuperación de Contraseña, Perfil y 2FA).

---

## ✨ Características Principales

- 🎯 **Aislamiento Multi-Stack Estricto:** Jamás mezcla archivos ni dependencias. Cada tecnología posee su propia implementación idiomática y libre de contaminaciones.
- ⚡ **Doble Modo de Entrega:**
  - **Modo Plug & Play (`compiled`):** Carga los bundles CSS/JS optimizados directamente desde `public/vendor/veltra/`. Cero dependencias de Node.js, listo para producción desde el segundo cero.
  - **Modo Pipeline (`vite`):** Integración nativa con Tailwind CSS v4 y Vite para proyectos que compilan sus propios assets.
- 🌓 **Zero-FOUC Dark Mode:** Script de arranque en el `<head>` que previene el destello blanco al navegar o recargar, con persistencia en `localStorage` y soporte para tema del sistema.
- 🤖 **Arquitectura AI-Native (Skill + MCP):** Tus asistentes de IA (Cursor, Windsurf, Claude Code, Antigravity) no adivinan ni alucinan: leen la Skill del proyecto o consultan el servidor MCP nativo `php artisan veltra:mcp` para saber qué componentes, props y tokens usar.
- 🛡️ **Blindaje para Producción (`veltra:build`):** Apaga rutas de demostración, limpia archivos temporales de prueba, compila assets y optimiza cachés de configuración, rutas y vistas con un solo comando.
- 📊 **Generadores Productivos Reales:** `veltra:crud` crea Controladores Eloquent con paginación real, validación con `FormRequest`, directivas `@forelse`, badges semánticos y formularios separados `create`/`edit`.

---

## 📋 Requisitos del Sistema

| Requisito | Versión Soportada |
| :--- | :--- |
| **PHP** | `^8.2`, `8.3` o `8.4` |
| **Laravel Framework** | `10.x`, `11.x`, `12.x` o `13.x` |
| **Extensiones PHP** | `json`, `mbstring`, `fileinfo` |
| **Tailwind CSS** | v4.0+ *(solo requerido si se utiliza el modo de entrega `'vite'`)* |
| **Node.js / npm** | Opcional *(solo para modo `'vite'` o compilación de React/Vue/Svelte)* |

---

## ⚡ Instalación y Primeros Pasos

### 1. Instalación vía Composer

Ejecuta el siguiente comando en la raíz de tu proyecto Laravel:

```bash
composer require veltra/veltra-laravel
```

### 2. Asistente Inteligente de Instalación

Inicia el instalador interactivo:

```bash
php artisan veltra:install
```

El instalador realiza las siguientes acciones de forma automática:
1. **Detección de Entorno:** Inspecciona tu `composer.json` y `package.json` para identificar si tu proyecto utiliza Livewire, React, Vue, Svelte o si es un proyecto Laravel limpio.
2. **Publicación de Configuración y Assets:** Copia `config/veltra.php` y los bundles precompilados a `public/vendor/veltra/`.
3. **Instalación de la Skill AI:** Publica `.agents/skills/veltra-ui/SKILL.md` y `.agents/mcp_config.json` para que tus agentes de IA reconozcan el sistema de diseño.
4. **Scaffolding de Layouts y Vistas:** Configura los layouts maestros (`app.blade.php`, sidebar, header y componentes base) adecuados a tu stack.

### 3. Instalación Silenciosa / No Interactiva

Ideal para scripts de despliegue automatizado, Dockerfiles o prompts de agentes de IA:

```bash
# Para proyectos limpios sin Starter Kit (incluye autenticación completa)
php artisan veltra:install --stack=none --no-interaction

# Para proyectos con Livewire (Breeze / Jetstream / Flux)
php artisan veltra:install --stack=livewire --no-interaction

# Para proyectos con Inertia + React (TypeScript)
php artisan veltra:install --stack=react --no-interaction

# Para proyectos con Inertia + Vue 3 (Composition API)
php artisan veltra:install --stack=vue --no-interaction

# Para proyectos con Inertia + Svelte
php artisan veltra:install --stack=svelte --no-interaction
```

> [!TIP]
> Si deseas publicar todas las vistas Blade del paquete en `resources/views/vendor/veltra` para personalizarlas a fondo, añade la opción `--views`.

---

## 🔀 Arquitectura Multi-Stack Estricta

VeltraLaravel mantiene una separación rigurosa entre las diferentes tecnologías de frontend soportadas por Laravel. A continuación se detallan las directrices y estructuras de cada stack:

### 1. Livewire 3 (Blade tradicional, Volt y Flux)

Diseñado para desarrolladores que prefieren el ecosistema Blade puro y reactividad reactiva con Livewire:

- **Ubicación de Vistas:** `resources/views/`
- **Componentes:** Etiquetados mediante `<x-veltra::*>` o `<x-veltra-button>`, `<x-veltra-stat-card>`, etc.
- **Iconografía:** Componentes Blade nativos de Lucide (`<x-lucide-user class="w-5 h-5 text-cyan-500" />`).
- **Estado Reactivo:** `wire:model.live`, eventos Livewire y Alpine.js (`x-data`, `x-show`).
- **Layout Maestro:** `<x-app-layout>` o `<x-veltra::layouts.app>`.

```blade
{{-- Ejemplo: resources/views/dashboard.blade.php --}}
<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Panel Principal</h1>
            <x-veltra::button variant="primary" icon="plus">Nuevo Pedido</x-veltra::button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-veltra::stat-card title="Ingresos Totales" value="$45,280" icon="badge-dollar-sign" trend="+12.5%" />
            <x-veltra::stat-card title="Nuevos Clientes" value="1,240" icon="users" trend="+8.2%" />
            <x-veltra::stat-card title="Tasa de Conversión" value="3.42%" icon="trending-up" trend="-0.4%" isNegative />
        </div>
    </div>
</x-app-layout>
```

---

### 2. React + Inertia (TypeScript / TSX)

Diseñado para aplicaciones modernas basadas en React 18/19 y TypeScript estricto:

- **Ubicación de Vistas:** `resources/js/Pages/`
- **Componentes Base:** `@/Components/Veltra/` (`Button.tsx`, `StatCard.tsx`, `Table.tsx`, `Badge.tsx`)
- **Iconografía:** `lucide-react`
- **Gestión de Formularios:** Hook `useForm` de `@inertiajs/react`
- **Layout:** `<AuthenticatedLayout>` con sidebar reactivo y switch de tema oscuro.

```tsx
// Ejemplo: resources/js/Pages/Dashboard.tsx
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Button, StatCard } from '@/Components/Veltra';
import { Plus, DollarSign, Users, TrendingUp } from 'lucide-react';

export default function Dashboard({ auth }: { auth: any }) {
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Panel Principal" />
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold text-slate-900 dark:text-white">Panel Principal</h1>
                    <Button variant="primary" icon={<Plus className="w-4 h-4" />}>
                        Nuevo Pedido
                    </Button>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <StatCard title="Ingresos Totales" value="$45,280" icon={<DollarSign className="w-5 h-5" />} trend="+12.5%" />
                    <StatCard title="Nuevos Clientes" value="1,240" icon={<Users className="w-5 h-5" />} trend="+8.2%" />
                    <StatCard title="Tasa de Conversión" value="3.42%" icon={<TrendingUp className="w-5 h-5" />} trend="-0.4%" isNegative />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
```

---

### 3. Vue 3 + Inertia (Composition API / SFC)

Diseñado para aplicaciones Vue 3 con `<script setup lang="ts">`:

- **Ubicación de Vistas:** `resources/js/Pages/`
- **Componentes Base:** `@/Components/Veltra/` (`Button.vue`, `StatCard.vue`, `Table.vue`, `Badge.vue`)
- **Iconografía:** `lucide-vue-next`
- **Gestión de Formularios:** `useForm` de `@inertiajs/vue3`
- **Layout:** `<AuthenticatedLayout>`

```vue
<!-- Ejemplo: resources/js/Pages/Dashboard.vue -->
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Button, StatCard } from '@/Components/Veltra';
import { Plus, DollarSign, Users, TrendingUp } from 'lucide-vue-next';

defineProps<{ auth: any }>();
</script>

<template>
    <AuthenticatedLayout :user="auth.user">
        <Head title="Panel Principal" />
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Panel Principal</h1>
                <Button variant="primary" :icon="Plus">Nuevo Pedido</Button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <StatCard title="Ingresos Totales" value="$45,280" :icon="DollarSign" trend="+12.5%" />
                <StatCard title="Nuevos Clientes" value="1,240" :icon="Users" trend="+8.2%" />
                <StatCard title="Tasa de Conversión" value="3.42%" :icon="TrendingUp" trend="-0.4%" :is-negative="true" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
```

---

### 4. Svelte + Inertia (Svelte 4/5)

Diseñado para aplicaciones ultraligeras y reactivas con Svelte:

- **Ubicación de Vistas:** `resources/js/Pages/`
- **Componentes Base:** `$lib/Components/Veltra/` (`Button.svelte`, `StatCard.svelte`, etc.)
- **Iconografía:** `lucide-svelte`
- **Gestión de Formularios:** `useForm` de `@inertiajs/svelte`

```svelte
<!-- Ejemplo: resources/js/Pages/Dashboard.svelte -->
<script>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.svelte';
    import { Button, StatCard } from '$lib/Components/Veltra';
    import { Plus, DollarSign, Users, TrendingUp } from 'lucide-svelte';

    export let auth;
</script>

<svelte:head>
    <title>Panel Principal - VeltraUI</title>
</svelte:head>

<AuthenticatedLayout user={auth.user}>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Panel Principal</h1>
            <Button variant="primary">
                <Plus class="w-4 h-4 mr-2" /> Nuevo Pedido
            </Button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <StatCard title="Ingresos Totales" value="$45,280" trend="+12.5%">
                <DollarSign class="w-5 h-5 text-cyan-500" slot="icon" />
            </StatCard>
            <StatCard title="Nuevos Clientes" value="1,240" trend="+8.2%">
                <Users class="w-5 h-5 text-cyan-500" slot="icon" />
            </StatCard>
        </div>
    </div>
</AuthenticatedLayout>
```

---

### 5. Proyectos Limpios (Clean / Vanilla Blade)

Si creaste tu proyecto con `laravel new mi-app` sin instalar Breeze ni Jetstream, el comando `php artisan veltra:install --stack=none` te entrega una solución completa de inicio:

- Vistas de Autenticación (`resources/views/auth/login.blade.php`, `register.blade.php`, `forgot-password.blade.php`, etc.)
- Vistas de Perfil y Factor de Doble Autenticación (2FA).
- Controladores completos en `app/Http/Controllers/Auth/`.
- Rutas protegidas configuradas en `routes/web.php` y `routes/auth.php`.
- Layouts de invitado (`layouts/guest.blade.php`) y autenticado (`layouts/app.blade.php`).

---

## 🧱 Catálogo de Componentes Atomic Design

VeltraUI organiza sus componentes en tres niveles jerárquicos:

### Átomos
Unidades indivisibles con soporte completo para modo claro y oscuro:
- **`Button`:** Variantes `primary` (Cyan #06b6d4), `secondary`, `outline`, `ghost`, `danger`. Tamaños `sm`, `md`, `lg`. Estados de carga y soporte para iconos.
- **`Input`:** Campo de texto con estados de validación, soporte para iconos a la izquierda/derecha y prefijos numéricos.
- **`Badge`:** Indicadores de estado con tonos suaves (`cyan`, `emerald`, `amber`, `rose`, `slate`).
- **`Switch / Toggle`:** Interruptores accesibles con animaciones fluidas.
- **`Dropdown / Select`:** Menús desplegables estables con microinteracciones en Tailwind.
- **`Avatar`:** Imagen de usuario con indicador de presencia en línea y fallback a iniciales.

### Moléculas
Combinación de átomos para formar unidades funcionales de interfaz:
- **`StatCard`:** Tarjeta de métricas con título, cifra en `font-mono`, porcentaje de tendencia positiva/negativa e icono contextual.
- **`FormField`:** Agrupador de etiqueta (`label`), campo interactivo (`input`/`select`), mensaje de ayuda y visualización reactiva de errores `@error`.
- **`SearchBar`:** Barra de búsqueda con icono de lupa, atajo de teclado (`Ctrl+K` / `Cmd+K`) y limpieza rápida.
- **`Alert`:** Notificaciones en contenedor redondeado (`rounded-2xl`) con variantes de información, éxito, advertencia y error.
- **`ModalDialog`:** Ventana emergente con desenfoque de fondo (*backdrop-blur*), tecla `ESC` para cierre y accesibilidad ARIA.

### Organismos
Estructuras complejas y autónomas que componen pantallas completas:
- **`Sidebar`:** Menú lateral inteligente con soporte para colapso, submenús anidados, badges de conteo y persistencia de estado.
- **`Navbar / Header`:** Barra superior con buscador global, selector de tema oscuro, notificaciones y menú de perfil.
- **`DataTable`:** Tabla de datos con cabecera estilizada en `text-[11px] font-bold uppercase`, ordenamiento por columnas, filtros y paginación integrada.
- **`KanbanBoard`:** Tablero de flujo de trabajo visual con columnas (*To Do*, *In Progress*, *Done*).
- **`PosRegister`:** Terminal de punto de venta con catálogo visual y ticket dinámico de compra.

---

## 🛠️ Referencia Completa de Comandos Artisan

La suite incluye 15 comandos optimizados. Todos soportan la bandera `--no-interaction` para ejecución desatendida.

---

### 1. `veltra:install` — Instalación y Configuración del Stack

Configura el paquete adaptándose a tu proyecto:

```bash
php artisan veltra:install [opciones]
```

| Opción | Descripción |
| :--- | :--- |
| `--stack=` | Fuerza el stack (`none`, `livewire`, `blade`, `react`, `vue`, `svelte`). Si se omite, se detecta automáticamente. |
| `--views` | Publica todas las vistas Blade en `resources/views/vendor/veltra` para personalización profunda. |
| `--no-build` | Omite la compilación automática de assets con `npm run build`. |
| `--force` | Sobrescribe archivos existentes sin solicitar confirmación. |

---

### 2. `veltra:page` — Generador de Páginas con Plantillas

Crea vistas conectadas al layout maestro, registrando opcionalmente la ruta web y el enlace en el menú del sidebar:

```bash
php artisan veltra:page {name} [opciones]
# Alias disponible: php artisan veltra:make-page
```

| Argumento / Opción | Descripción |
| :--- | :--- |
| `{name}` | Nombre de la página en notación de ruta o vista (ej: `Admin/Orders`, `reports.sales`). |
| `--template=` | Plantilla base: `blank`, `table`, `analytics`, `kanban`, `pos`, `form`. |
| `--stack=` | Stack de destino (`livewire`, `react`, `vue`, `svelte`). Se autodetecta si se omite. |
| `--route` | Registra automáticamente la ruta en `routes/web.php`. |
| `--route-uri=` | URI personalizada para la ruta (ej: `/admin/orders`). |
| `--menu` | Añade automáticamente el enlace al sidebar en `config/veltra.php`. |
| `--menu-title=` | Título a mostrar en el menú lateral. |
| `--menu-icon=` | Nombre del icono de Lucide para el enlace (ej: `shopping-cart`). |
| `--force` | Sobrescribe la vista si ya existe. |

**Ejemplo de uso:**
```bash
php artisan veltra:page Admin/Inventory --template=table --route --menu --menu-icon="boxes" --no-interaction
```

---

### 3. `veltra:crud` — Generador de CRUDs Eloquent Productivos

Genera código productivo de alta calidad listo para el mundo real (no prototipos estáticos):

```bash
php artisan veltra:crud {model} [opciones]
```

- Genera el **Controlador Eloquent** con métodos `index` (con paginación y búsqueda), `create`, `store` (con validación), `edit`, `update` y `destroy`.
- Genera la vista `index.blade.php` con tabla de datos, directiva `@forelse` para estados vacíos y badges semánticos.
- Genera las vistas separadas `create.blade.php` y `edit.blade.php` con campos adaptados a los tipos de datos, helpers `old()` y directivas `@error`.
- Registra `Route::resource()` en `routes/web.php` y crea el enlace en el sidebar.

| Opción | Descripción |
| :--- | :--- |
| `{model}` | Nombre del Modelo Eloquent (ej: `Product`, `Customer`, `Invoice`). |
| `--fields=` | Lista de campos y tipos separados por coma (ej: `name:string,price:number,email:email,status:string,active:boolean`). |
| `--stack=` | Stack de frontend (`livewire`, `react`, `vue`, `svelte`). |
| `--route` | Registra la ruta de recurso en `routes/web.php`. |
| `--menu` | Agrega el acceso directo en el sidebar. |
| `--force` | Sobrescribe archivos existentes. |

**Ejemplo de uso:**
```bash
php artisan veltra:crud Customer --fields="name:string,email:email,phone:string,balance:number,status:string" --route --menu --no-interaction
```

---

### 4. `veltra:component` — Generador Atomic Design

Crea componentes reutilizables respetando la jerarquía atómica y el stack activo:

```bash
php artisan veltra:component {name} [opciones]
```

| Opción | Descripción |
| :--- | :--- |
| `{name}` | Nombre del componente (ej: `MetricCard`, `OrderSummary`, `StatusBadge`). |
| `--type=` | Nivel de Atomic Design: `atom`, `molecule` o `organism`. |
| `--stack=` | Stack de destino (`blade`, `react`, `vue`, `svelte`). |
| `--force` | Sobrescribe si ya existe. |

**Ejemplo de uso:**
```bash
php artisan veltra:component SalesChart --type=molecule --no-interaction
```

---

### 5. `veltra:reference` — Catálogo y Cheatsheet Anti-Alucinación

Genera la documentación de referencia oficial en `resources/veltra/` para que los desarrolladores y las IAs de código conozcan los componentes exactos, props y tokens sin inventar clases CSS ni alucinar APIs:

```bash
php artisan veltra:reference [opciones]
```

- **Comportamiento inteligente:** Detecta el stack del proyecto y **únicamente genera los componentes del stack activo**, purgando directorios de tecnologías no utilizadas para no ensuciar tu repositorio.
- Genera `resources/veltra/AI_REFERENCE.md` con reglas estrictas y tokens de diseño.
- Genera `resources/veltra/catalog.json` con metadatos estructurados de todos los componentes.

| Opción | Descripción |
| :--- | :--- |
| `--stack=` | `livewire`, `react`, `vue`, `svelte` o `all` (para exportar la biblioteca completa). |
| `--cursor` | Sincroniza o actualiza automáticamente el archivo `.cursorrules` en la raíz. |
| `--dir=` | Directorio de destino personalizado (por defecto `resources/veltra`). |
| `--force` | Sobrescribe archivos existentes sin confirmación. |

---

### 6. `veltra:build` — Preparador y Blindaje para Producción

Automatiza el checklist crítico de pase a producción en 5 pasos certificados:

```bash
php artisan veltra:build [opciones]
```

1. **Blindaje de Rutas:** Desactiva `/veltra/demo` (`VELTRA_ROUTES_ENABLED=false`) tanto en `config/veltra.php` como en el archivo `.env`.
2. **Limpieza de Archivos:** Elimina vistas y rutas de prueba temporales (`--clean`).
3. **Compilación de Assets:** Ejecuta `npm run build` si el proyecto utiliza Vite.
4. **Optimización de Caché:** Ejecuta `config:cache`, `route:cache` y `view:cache`.
5. **Auditoría de Salud:** Muestra un reporte gráfico en consola confirmando que la aplicación está `PRODUCTION READY`.

| Opción | Descripción |
| :--- | :--- |
| `--clean` | Elimina vistas de prueba y laboratorios de componentes (`resources/views/admin/tests.blade.php`). |
| `--no-cache` | Omite la generación de caché de Laravel. |
| `--no-compile` | Omite la ejecución de `npm run build`. |
| `--force` | Ejecuta todas las acciones sin confirmaciones interactivas. |

---

### 7. `veltra:theme` — Personalizador de Marca, Tema y Layout

Permite configurar la identidad de tu aplicación de forma interactiva o mediante parámetros directos:

```bash
php artisan veltra:theme [opciones]
```

| Opción | Descripción |
| :--- | :--- |
| `--name=` | Nombre comercial de la aplicación (actualiza `config/veltra.php`, `.env` y el sidebar). |
| `--theme=` | Tema por defecto al cargar: `light`, `dark` o `system`. |
| `--boxed` | Activa el diseño de ancho contenido (*Boxed layout*, centrado a 1340px con sombras suaves). |
| `--collapsed` | Inicia la aplicación con el menú lateral en modo icono colapsado. |

**Ejemplo de uso:**
```bash
php artisan veltra:theme --name="LaraCorp" --theme=dark --boxed --no-interaction
```

---

### 8. `veltra:menu` — Administrador del Sidebar

Permite visualizar la estructura del menú lateral o registrar nuevos accesos directos directamente desde la consola:

```bash
# Listar elementos y secciones activas
php artisan veltra:menu --list

# Agregar un nuevo enlace
php artisan veltra:menu --add --title="Facturas" --url="/invoices" --icon="receipt" --section="VENTAS" --no-interaction
```

---

### 9. `veltra:icons` — Buscador Interactivo de Iconos Lucide

Busca entre más de 40 iconos empresariales curados con previsualización del código exacto para copiar y pegar:

```bash
php artisan veltra:icons [termino] [opciones]
```

```bash
# Ejemplo: buscar iconos de dinero y pagos
php artisan veltra:icons payment

# Filtrar por categoría empresarial (nav, action, finance, status, system)
php artisan veltra:icons --category=finance
```

---

### 10. `veltra:doctor` — Auditoría de Salud del Entorno

Ejecuta un diagnóstico completo del entorno de ejecución:
- Comprueba la existencia y permisos de `config/veltra.php`.
- Verifica la presencia de los bundles CSS y JS en `public/vendor/veltra/`.
- Comprueba la validez de los enlaces simbólicos de almacenamiento (`storage:link`).
- Verifica si el modo de entrega de assets (`compiled` o `vite`) es coherente con los archivos del proyecto.

```bash
php artisan veltra:doctor
```

---

### 11. `veltra:demo` — Vitrina Interactiva de Componentes

Controla el acceso a la ruta visual `/veltra/demo`, donde puedes probar interactivamente botones, modales, formularios, tarjetas de métricas y componentes:

```bash
# Habilitar vitrina de demostración (en desarrollo)
php artisan veltra:demo --enable

# Deshabilitar vitrina de demostración (en producción)
php artisan veltra:demo --disable
```

---

### 12. `veltra:auth` — Scaffolding Completo de Autenticación

Instala el módulo de autenticación empresarial de VeltraUI:

```bash
php artisan veltra:auth [opciones]
```

| Opción | Descripción |
| :--- | :--- |
| `--views-only` | Publica únicamente las vistas Blade sin crear controladores ni alterar rutas. |
| `--force` | Sobrescribe archivos existentes sin confirmación. |

---

### 13. `veltra:skill` — Instalador de Skills para Agentes IA

Publica la Skill oficial en `.agents/skills/veltra-ui/SKILL.md` y `.agents/mcp_config.json`:

```bash
php artisan veltra:skill [--force]
```

---

### 14. `veltra:mcp` — Servidor Nativo Model Context Protocol

Inicia el servidor Model Context Protocol sobre `stdio` (JSON-RPC 2.0) para que herramientas de IA como Cursor, Claude Desktop, Antigravity o Windsurf consulten información del proyecto en tiempo real:

```bash
# Probar el servidor y listar herramientas sin bloquear la terminal
php artisan veltra:mcp --ping

# Ejecutar autoprueba funcional de herramientas
php artisan veltra:mcp --test

# Modo servidor continuo sobre stdio (consumido por clientes MCP)
php artisan veltra:mcp
```

---

### 15. `veltra:publish` — Publicador Selectivo de Recursos

Permite publicar selectivamente los recursos del paquete sin sobreescribir partes que no deseas alterar:

```bash
php artisan veltra:publish --config    # Publica únicamente config/veltra.php
php artisan veltra:publish --assets    # Publica CSS, JS e imágenes a public/vendor/veltra
php artisan veltra:publish --views     # Publica vistas y componentes Blade
php artisan veltra:publish --skill     # Publica la Skill para IAs y configuración MCP
php artisan veltra:publish --all       # Publica todos los recursos
```

---

## 🤖 Integración AI-First & Servidor MCP

VeltraLaravel es el primer paquete de interfaz para Laravel diseñado nativamente bajo el paradigma **AI-First**.

### ¿Qué es el Protocolo MCP en VeltraUI?

El **Model Context Protocol (MCP)** es un estándar abierto desarrollado por Anthropic que permite a los modelos de lenguaje (LLMs) conectarse de forma segura con herramientas y fuentes de contexto locales.

VeltraLaravel implementa un servidor MCP nativo directamente en PHP que se comunica mediante `stdio` utilizando el estándar **JSON-RPC 2.0**.

### Configuración en Editores

#### Cursor
Añade lo siguiente en tu configuración (`Settings` > `Features` > `MCP` > `Add New MCP Server`) o en tu archivo `.cursor/mcp.json`:

```json
{
  "mcpServers": {
    "veltra": {
      "command": "php",
      "args": ["artisan", "veltra:mcp"]
    }
  }
}
```

#### Claude Desktop
Edita tu archivo `claude_desktop_config.json`:

```json
{
  "mcpServers": {
    "veltra": {
      "command": "php",
      "args": ["artisan", "veltra:mcp"],
      "cwd": "/ruta/absoluta/a/tu/proyecto-laravel"
    }
  }
}
```

### Herramientas Disponibles en el Servidor MCP

Cuando tu asistente de IA está conectado a VeltraLaravel, dispone de las siguientes funciones automáticas:

| Herramienta | Propósito |
| :--- | :--- |
| `veltra_detect_stack` | Le informa a la IA qué tecnología utiliza el proyecto (`livewire`, `react`, `vue`, `svelte`) para evitar que genere código en el framework incorrecto. |
| `veltra_get_component` | Retorna el código fuente oficial, parámetros, props y slots de cualquier componente de VeltraUI. |
| `veltra_get_design_tokens` | Devuelve los colores hexadecimales exactos (Cyan `#06b6d4`, Slate), clases de bordes (`rounded-2xl`) y reglas tipográficas. |
| `veltra_search_icons` | Busca nombres de iconos de Lucide disponibles para el proyecto. |
| `veltra_list_components` | Lista todos los átomos, moléculas y organismos disponibles. |
| `veltra_get_security_checklist` | Devuelve la matriz de ciberseguridad industrial y reglas de hardening OWASP Top 10 para blindar controladores, modelos y vistas. |
| `veltra_get_performance_guidelines` | Proporciona reglas de alto rendimiento: eliminación de N+1, agregaciones directas SQL en dashboards, paginación escalable y caché. |
| `veltra_get_testing_guidelines` | Devuelve plantillas y pautas de testing automatizado con Pest y PHPUnit para CRUDs, RBAC y emuladores (Fakes). |

### La Suite de 4 Skills Oficiales para Agentes de Código

Al ejecutar `php artisan veltra:install` o `php artisan veltra:skill`, se publica el cuarteto de Skills en `.agents/skills/`:

1. **`veltra-ui` (`.agents/skills/veltra-ui/SKILL.md`):**
   - Enseña el sistema de diseño corporativo VeltraUI, tokens de estilo (Cyan `#06b6d4`, Slate, Dark Mode Zero-FOUC).
   - Impone la separación estricta entre Livewire (Blade/Volt/Flux), React TSX, Vue SFC y Svelte.
   - Instruye el uso no interactivo (`--no-interaction`) de todos los comandos Artisan.

2. **`veltra-security` (`.agents/skills/veltra-security/SKILL.md`):**
   - Manual avanzado de ciberseguridad industrial basado en **OWASP Top 10** adaptado a Laravel.
   - Enseña a la IA a prevenir **IDOR** (`auth()->user()->models()->findOrFail($id)`), **Mass Assignment** (uso obligatorio de `FormRequest` y `$request->validated()`).
   - Bloquea inyecciones SQL en consultas complejas, previene **XSS** en Blade/React/Vue, y blinda la subida de archivos validando Magic Bytes y tipos MIME reales.
   - Reglas de hardening para peticiones HTTP salientes (prevención de **SSRF** bloqueando rangos privados y metadatos de Cloud).

3. **`veltra-performance` (`.agents/skills/veltra-performance/SKILL.md`):**
   - Manual de alto rendimiento y optimización de bases de datos.
   - Eliminación del problema N+1 mediante Eager Loading (`with(['relacion'])`, `withCount()`).
   - Agregaciones directas en SQL para tarjetas de métricas (`StatCard`), evitando saturación de memoria RAM.
   - Paginación escalable con `cursorPaginate()` y procesamiento por lotes con `chunkById()`.
   - Estrategia de caché con Redis e invalidación atómica mediante Observers.

4. **`veltra-testing` (`.agents/skills/veltra-testing/SKILL.md`):**
   - Manual de pruebas automatizadas con **Pest PHP** y **PHPUnit**.
   - Generación de tests completos para cada CRUD (escenarios `index`, `store`, `update`, `destroy` y validación `422`).
   - Pruebas rigurosas de autorización y roles (RBAC): verificar `403 Forbidden` para accesos indebidos y prevención de IDOR.
   - Aislamiento con Fakes de Laravel (`Storage::fake()`, `Queue::fake()`, `Mail::fake()`, `Http::fake()`).

---

## ⚙️ Configuración y Variables de Entorno

El archivo de configuración principal se encuentra en `config/veltra.php`. A continuación se describen las directivas más relevantes y sus variables de entorno asociadas:

```php
return [

    // Identidad de marca visual
    'brand' => [
        'name' => env('VELTRA_BRAND_NAME', 'VeltraUI'),
        'short_name' => 'Veltra',
        'suffix' => 'UI',
        'logo' => env('VELTRA_LOGO_URL', '/vendor/veltra/img/logo.png'),
        'home_url' => env('VELTRA_HOME_URL', '/dashboard'),
    ],

    // Preferencias de tema y modo oscuro
    'theme' => [
        'default' => env('VELTRA_THEME_DEFAULT', 'light'), // 'light', 'dark', 'system'
        'allow_toggle' => true,
        'storage_key' => 'veltra-theme',
    ],

    // Modo de entrega de assets
    'assets' => [
        'mode' => env('VELTRA_ASSETS_MODE', 'compiled'), // 'compiled' o 'vite'
        'cdn_lucide' => true,
        'lucide_version' => 'latest',
    ],

    // Preferencias de layout
    'layout' => [
        'boxed' => env('VELTRA_LAYOUT_BOXED', false), // true para ancho contenido (1340px)
        'sidebar_collapsed' => false,
        'fixed_sidebar' => true,
        'fixed_header' => true,
        'show_breadcrumbs' => true,
        'show_footer' => true,
        'footer_text' => '© ' . date('Y') . ' VeltraUI. Todos los derechos reservados.',
    ],

    // Rutas internas de demostración
    'routes' => [
        'enabled' => env('VELTRA_ROUTES_ENABLED', false), // Desactivar siempre en producción
        'prefix' => 'veltra',
        'middleware' => ['web'],
    ],

];
```

### Variables en tu archivo `.env`

```env
VELTRA_BRAND_NAME="Mi Empresa"
VELTRA_THEME_DEFAULT="light"
VELTRA_LAYOUT_BOXED=false
VELTRA_ROUTES_ENABLED=false
VELTRA_ASSETS_MODE="compiled"
```

---

## 🧭 Sistema de Navegación, Menús y Submenús

VeltraLaravel implementa una arquitectura de navegación declarativa, centralizada y reactiva gestionada a través de `Veltra\VeltraLaravel\Support\Navigation` y el archivo de configuración `config/veltra.php`. Esta arquitectura garantiza que tanto el frontend (Livewire, React, Vue o Svelte) como el backend compartan la misma verdad sin duplicación de código.

### 1. Estructura Declarativa en `config/veltra.php`

El arreglo `'navigation'` en `config/veltra.php` admite tres tipos de elementos principales:

```php
// config/veltra.php
'navigation' => [
    // 1. Encabezados de Sección (Separadores visuales en mayúsculas)
    [
        'header' => 'MAIN NAVIGATION',
    ],

    // 2. Enlace Directo Simple
    [
        'title' => 'Dashboard',
        'icon' => 'gauge',              // Icono de Lucide
        'route' => 'dashboard',          // Resuelto mediante route('dashboard')
        // o bien: 'url' => '/dashboard',
        'badge' => 'Nuevo',             // Insignia opcional
        'badge_variant' => 'cyan',      // Variante visual: cyan, emerald, amber, rose
    ],

    // 3. Submenú Desplegable con Elementos Hijos (Treeview)
    [
        'title' => 'Ventas & Facturación',
        'icon' => 'receipt',
        'children' => [
            [
                'title' => 'Facturas Emitidas',
                'icon' => 'file-text',
                'url' => '/billing/invoices',
            ],
            [
                'title' => 'Notas de Crédito',
                'icon' => 'file-minus',
                'url' => '/billing/credit-notes',
            ],
        ],
    ],
],
```

---

### 2. Submenús Multinivel y Grupos Colapsables

Los elementos con la clave `children` se transforman automáticamente en menús acordeón desplegables:
- **En Blade / Livewire:** Se gestiona mediante el atributo `data-vlt-toggle="treeview"` o estado interactivo de Alpine.js, agregando la clase `is-open` si el grupo contiene una página actualmente activa.
- **En Inertia (React, Vue, Svelte):** Se serializan con la propiedad `has_active_child`. El componente `Sidebar` mantiene reactivamente el estado de apertura (`openGroups` en Vue/Svelte o `useState` en React), expandiendo automáticamente el submenú si la URL actual corresponde a uno de sus elementos hijos.

---

### 3. Detección Inteligente de Estado Activo y Sincronización

La clase `Navigation::isActive($url)` y los componentes de cliente analizan la URL actual en tiempo real para iluminar la navegación:
1. **Coincidencia Exacta:** Si la URL solicitada es idéntica a la ruta (`/dashboard` === `/dashboard`).
2. **Coincidencia de Subruta:** Para recursos anidados tipo CRUD (`/customers/create` o `/customers/12/edit` activan el menú `/customers`).
3. **Mapeo de Perfil y Ajustes:** Soporta la familia de rutas de usuario (`/settings/profile`, `/settings/security`, `/settings/appearance`, `/profile`), manteniendo activa la sección correcta en la barra lateral y en los accesos directos superiores.
4. **Resaltado Visual Corporativo:** Cuando un elemento está activo, recibe automáticamente el token de acento:
   - Fondo: Cyan corporativo (`bg-cyan-500` / `var(--color-cyan-500)`).
   - Tipografía: Blanco puro en negrita (`text-white font-bold`).
   - Icono: Blanco puro (`text-white`).

---

### 4. Resolución de Rutas Dinámicas y Fallbacks

`Navigation::resolveUrl($item)` resuelve las URLs de forma segura para evitar errores de ejecución cuando las rutas aún no se han registrado:
- Si el ítem especifica `'route' => 'nombre'`, comprueba `Route::has('nombre')`. Si no existe, realiza un fallback elegante a `'url'`.
- Resuelve dinámicamente las rutas de perfil (`profile.edit`, `settings.profile`, etc.) según los controladores activos en la aplicación.

---

### 5. Consumo Multi-Stack (Blade, React, Vue, Svelte)

La navegación se adapta con total fidelidad al stack que estés utilizando:

| Stack | Método de Renderizado | Archivo de Componente |
| :--- | :--- | :--- |
| **Blade / Livewire** | `Navigation::filterAuthorized(config('veltra.navigation'))` | `resources/views/layouts/partials/sidebar.blade.php` |
| **React + Inertia** | Props compartidas en `HandleInertiaRequests` (`props.veltra.navigation`) | `resources/js/components/Organisms/Sidebar.tsx` |
| **Vue 3 + Inertia** | Props compartidas en `HandleInertiaRequests` (`props.veltra.navigation`) | `resources/js/components/Organisms/Sidebar.vue` |
| **Svelte 5 + Inertia**| Runes reactivos (`$derived(page.props.veltra?.navigation)`) | `resources/js/components/Organisms/Sidebar.svelte` |

#### Árbol Serializado para Inertia (`Navigation::getTree()`):
```json
[
  { "header": "MAIN NAVIGATION" },
  {
    "title": "Dashboard",
    "icon": "gauge",
    "url": "http://127.0.0.1:8000/dashboard",
    "active": true,
    "badge": null,
    "children": [],
    "has_active_child": false
  }
]
```

---

### 6. Comandos Artisan y Automatización de Menús

No necesitas editar `config/veltra.php` a mano para cada nuevo CRUD o vista:

```bash
# 1. Visualizar la estructura actual del sidebar
php artisan veltra:menu --list

# 2. Agregar un nuevo acceso con icono y sección
php artisan veltra:menu --add --title="Facturas" --url="/invoices" --icon="receipt" --section="VENTAS" --no-interaction

# 3. Al generar una nueva página o CRUD, agrega la bandera --menu
php artisan veltra:page Admin/Inventory --template=table --route --menu --menu-icon="boxes" --no-interaction
php artisan veltra:crud Customer --fields="name:string,email:email,phone:string" --route --menu --no-interaction
```

---

## 🔐 Manejo de Roles y Permisos (RBAC)

VeltraLaravel está preparado para interactuar de forma transparente con el sistema de autorización nativo de Laravel (**Gates & Policies**) y con paquetes de roles como **`spatie/laravel-permission`**.

### 1. Filtrado Automático en el Menú Lateral

En `config/veltra.php`, puedes asignar permisos y roles a cualquier elemento o submenú de navegación mediante las claves `'can'`, `'permission'`, `'role'` o `'roles'`. Si el usuario autenticado no posee la autorización requerida, el elemento se omite automáticamente del renderizado en el sidebar sin requerir código adicional:

```php
// config/veltra.php
'navigation' => [
    [
        'header' => 'ADMINISTRACIÓN',
    ],
    // Requiere un permiso específico (Laravel Gate o Spatie Permission)
    [
        'title' => 'Gestión de Usuarios',
        'icon' => 'users',
        'url' => '/admin/users',
        'permission' => 'manage-users', // o 'can' => 'manage-users'
    ],
    // Requiere un rol específico (Spatie hasRole o $user->role === 'admin')
    [
        'title' => 'Configuración Global',
        'icon' => 'settings',
        'url' => '/admin/settings',
        'role' => 'admin',
    ],
    // Submenú con múltiples roles permitidos
    [
        'title' => 'Finanzas & Facturación',
        'icon' => 'receipt',
        'roles' => ['admin', 'accountant'],
        'children' => [
            ['title' => 'Facturas Emitidas', 'url' => '/billing/invoices'],
            ['title' => 'Reportes Fiscales', 'url' => '/billing/tax', 'can' => 'view-tax-reports'],
        ],
    ],
],
```

> [!NOTE]
> Para grupos con `children`, si un usuario no tiene acceso a ninguno de los elementos hijos, el grupo padre completo se oculta automáticamente para mantener el menú limpio.

---

### 2. Visualización Dinámica del Rol de Usuario

El componente `Sidebar` detecta automáticamente el rol del usuario autenticado en el panel lateral:
1. Si usas **Spatie Permission**, extrae el nombre con `$user->getRoleNames()->first()`.
2. Si usas una columna propia en base de datos (`$user->role`), la muestra capitalizada.
3. Si el usuario no está autenticado, muestra `Invitado`.

---

### 3. En Vistas Blade y Livewire

En componentes Blade o Livewire, puedes condicionar la interfaz usando directivas estándar de Laravel y los componentes de VeltraUI:

```blade
{{-- Proteger acciones críticas con @can --}}
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">Catálogo de Productos</h1>
    @can('create-products')
        <x-veltra::button variant="primary" icon="plus">Nuevo Producto</x-veltra::button>
    @endcan
</div>

{{-- Restricción por Rol con Spatie o Directivas de Laravel --}}
@role('admin')
    <div class="p-4 bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 rounded-2xl">
        <p class="text-xs text-cyan-800 dark:text-cyan-300 font-semibold">Modo Superadministrador Activo</p>
    </div>
@endrole
```

---

### 4. En Inertia (React, Vue, Svelte)

Para stacks basados en Inertia.js, comparte los permisos y roles en el middleware `HandleInertiaRequests.php`:

```php
// app/Http/Middleware/HandleInertiaRequests.php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'auth' => [
            'user' => $request->user(),
            'roles' => $request->user()?->getRoleNames() ?? ($request->user()?->role ? [$request->user()->role] : []),
            'permissions' => $request->user() ? $request->user()->getAllPermissions()->pluck('name') : [],
        ],
    ];
}
```

#### En React (TSX):
```tsx
import { usePage } from '@inertiajs/react';
import { Button } from '@/Components/Veltra';

export default function OrderList() {
    const { auth } = usePage<any>().props;
    const canCreate = auth.permissions.includes('create-orders') || auth.roles.includes('admin');

    return (
        <div>
            {canCreate && (
                <Button variant="primary">Crear Pedido</Button>
            )}
        </div>
    );
}
```

#### En Vue 3 (SFC):
```vue
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Button } from '@/Components/Veltra';

const page = usePage();
const canCreate = page.props.auth.permissions.includes('create-orders');
</script>

<template>
    <Button v-if="canCreate" variant="primary">Crear Pedido</Button>
</template>
```

---

### 5. En Rutas y Controladores

Protege las rutas generadas por `veltra:crud` o `veltra:page` usando los middlewares de autorización:

```php
// routes/web.php
use App\Http\Controllers\CustomerController;

// Usando Gates nativos de Laravel
Route::middleware(['auth', 'can:manage-customers'])->group(function () {
    Route::resource('customers', CustomerController::class);
});

// Usando Roles de Spatie
Route::middleware(['auth', 'role:admin|manager'])->group(function () {
    Route::resource('orders', OrderController::class);
});
```

En tus Controladores Eloquent generados con `veltra:crud`:
```php
public function __construct()
{
    $this->authorizeResource(Customer::class, 'customer');
}
```

---

## 🎨 Tokens de Diseño Corporativo

Toda vista o componente generado por VeltraLaravel utiliza una paleta cromática y reglas de espaciado unificadas:

| Token | Propósito | Clase Tailwind Oficial |
| :--- | :--- | :--- |
| **Primary Color** | Botones de acción, enlaces activos, badges destacados | `bg-cyan-500 hover:bg-cyan-600 text-white` |
| **Accent Text** | Resaltados, estados activos | `text-cyan-600 dark:text-cyan-400` |
| **Surface Card** | Contenedores, tarjetas y paneles | `bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl` |
| **Surface Input** | Campos de texto, selectores y controles | `bg-white dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl` |
| **Table Header** | Encabezados de listas | `bg-slate-50/75 dark:bg-slate-800/50 text-[11px] font-bold uppercase text-slate-500 dark:text-slate-400` |
| **Font Body** | Tipografía de lectura general | `font-sans` (*Plus Jakarta Sans*) |
| **Font Numeric**| Tipografía de cifras, IDs y métricas | `font-mono` (*JetBrains Mono*) |
| **Border Radius**| Tarjetas y contenedores principales | `rounded-2xl` |
| **Button Radius**| Botones, inputs y controles pequeños | `rounded-xl` |

---

## 🚀 Despliegue a Producción

Antes de desplegar tu aplicación a un entorno productivo, ejecuta el comando de blindaje:

```bash
php artisan veltra:build --clean --force
```

Este comando garantiza que:
1. Las rutas de demostración `/veltra/demo` queden deshabilitadas (`VELTRA_ROUTES_ENABLED=false`).
2. Se eliminen archivos de laboratorio o pruebas que pudieran exponer datos.
3. Se compilen los assets de producción con Vite (`npm run build`).
4. Se generen las cachés de configuración (`config:cache`), rutas (`route:cache`) y vistas (`view:cache`).

---

## 🧪 Pruebas Automatizadas (Testing Suite)

VeltraLaravel mantiene una cobertura rigurosa respaldada por **Orchestra Testbench**:

```bash
# Ejecutar la suite de pruebas unitarias y funcionales
cd veltra-laravel
vendor/bin/phpunit
```

- **Cobertura:** 31 tests unitarios y funcionales, más de 160 aserciones, **100% de éxito**.
- **Matriz de Compatibilidad en CI:**
  - PHP: `8.2`, `8.3`, `8.4`
  - Laravel: `10.x`, `11.x`, `12.x`
  - Sistemas Operativos: Linux (Ubuntu), macOS, Windows

---

## 📄 Licencia

VeltraLaravel es un paquete de código abierto publicado bajo los términos de la licencia [MIT](LICENSE). Eres libre de utilizarlo en proyectos comerciales, personales o empresariales.
