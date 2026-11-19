# Registro de Cambios (Changelog)

Todos los cambios notables en este proyecto serán documentados en este archivo.
El formato se basa en [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/) y este proyecto se adhiere a [Semantic Versioning](https://semver.org/lang/es/).

---

## [1.0.0] - 2026-11-16

### 🎉 Lanzamiento Inicial Oficial

#### ✨ Características Principales
* **Compatibilidad Multi-Versión:** Soporte completo y probado en Laravel 10, 11 y 12, con compatibilidad adelantada (*forward-compatible*) para Laravel 13 en PHP 8.2, 8.3 y 8.4.
* **Arquitectura Multi-Stack Estricta:** Aislamiento total de código entre:
  * **Livewire 3:** Blade nativo, Volt, Flux y Alpine.js.
  * **React + Inertia:** Componentes tipados con TypeScript (`.tsx`) y `lucide-react`.
  * **Vue 3 + Inertia:** Single File Components con `<script setup lang="ts">` y `lucide-vue-next`.
  * **Svelte + Inertia:** Svelte 4/5 con `lucide-svelte`.
  * **Vanilla Blade:** Módulo completo de autenticación y perfil para proyectos sin Starter Kit.
* **Doble Modo de Entrega:**
  * Modo precompilado *Plug & Play* (`compiled`): cero dependencias de Node.js ni compilación.
  * Modo *Vite Pipeline* (`vite`): integración con Tailwind CSS v4.
* **Zero-FOUC Dark Mode:** Arranque instantáneo en el `<head>` que previene parpadeos blancos y respeta las preferencias del sistema y `localStorage`.
* **Roles y Permisos (RBAC):**
  * Filtrado automático de navegación en `config/veltra.php` mediante `'can'`, `'permission'`, `'role'` y `'roles'`.
  * Compatible con Gates nativos de Laravel, Policies y `spatie/laravel-permission`.
  * Detección dinámica del rol de usuario en el panel del sidebar.

#### 🤖 Integración AI-First (Skills & MCP)
* **Suite de 4 Skills Oficiales:**
  * **`veltra-ui`:** Instrucciones de diseño, componentes atómicos y distinción estricta entre stacks (Livewire, React, Vue, Svelte).
  * **`veltra-security`:** Manual de ciberseguridad industrial y hardening basado en OWASP Top 10.
  * **`veltra-performance`:** Manual de alto rendimiento, optimización de base de datos (N+1, agregaciones SQL, cursor pagination) y caché.
  * **`veltra-testing`:** Metodología y plantillas para generación de pruebas automatizadas con Pest y PHPUnit.
* **Servidor MCP Nativo (`php artisan veltra:mcp`):** Servidor JSON-RPC 2.0 sobre `stdio` con 8 herramientas:
  * `veltra_detect_stack`
  * `veltra_get_component`
  * `veltra_get_design_tokens`
  * `veltra_search_icons`
  * `veltra_list_components`
  * `veltra_get_security_checklist`
  * `veltra_get_performance_guidelines`
  * `veltra_get_testing_guidelines`

#### 🛠️ Suite de Comandos Artisan
* `veltra:install`: Asistente interactivo y silencioso con auto-detección de stack.
* `veltra:page` / `veltra:make-page`: Generador de páginas con plantillas (`blank`, `table`, `analytics`, `kanban`, `pos`, `form`).
* `veltra:crud`: Generador de CRUDs productivos con controladores Eloquent y vistas responsivas.
* `veltra:component`: Generador Atomic Design (Átomos, Moléculas, Organismos).
* `veltra:reference`: Generador de catálogos anti-alucinación (`AI_REFERENCE.md`).
* `veltra:build`: Preparador y blindaje para pase a producción limpio.
* `veltra:theme`: Personalizador sincronizado de marca comercial, tema y layout boxed/fluid.
* `veltra:menu`: Administrador de elementos y secciones del sidebar.
* `veltra:icons`: Buscador interactivo de iconos Lucide empresariales.
* `veltra:doctor`: Auditoría integral de integridad, assets, skills y seguridad.
* `veltra:demo`: Control de activación de vitrina interactiva.
* `veltra:auth`: Scaffolding completo de autenticación y 2FA.
* `veltra:skill`: Instalador de las skills de IA oficiales.
* `veltra:mcp`: Servidor Model Context Protocol.
* `veltra:publish`: Publicador granular de recursos.

#### 🧪 Calidad & Testing
* Suite completa con Orchestra Testbench (33 tests, 167 aserciones, 100% aprobado).
* Workflow de GitHub Actions con matriz de pruebas contra PHP 8.2, 8.3 y 8.4 en Laravel 10, 11 y 12.
