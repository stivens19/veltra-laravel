# Guía Definitiva de Publicación y CI/CD en Packagist: `veltra/veltra-laravel`

Esta guía detalla el procedimiento de publicación inicial y el ciclo de vida continuo de desarrollo profesional (Ramas, Pull Requests, Pruebas Automatizadas y Versionado Semántico Autónomo) para **`veltra/veltra-laravel`** en **[Packagist.org](https://packagist.org/)**.

---

## 📋 Arquitectura del Ciclo de Desarrollo (Enterprise CI/CD)

```
┌──────────────────────────────────────────────────────────────┐
│ 1. Desarrollar en rama de trabajo                            │
│    git checkout -b feature/nuevo-componente                  │
└──────────────────────────────┬───────────────────────────────┘
                               ▼
┌──────────────────────────────────────────────────────────────┐
│ 2. Commits con estándar Semántico                            │
│    git commit -m "feat: agregar componente de calendario"    │
└──────────────────────────────┬───────────────────────────────┘
                               ▼
┌──────────────────────────────────────────────────────────────┐
│ 3. Abrir Pull Request (PR) hacia 'main'                      │
│    GitHub Actions (run-tests.yml) ejecuta la matriz en:      │
│    • PHP 8.2, 8.3, 8.4                                       │
│    • Laravel 10, 11, 12, 13                                  │
└──────────────────────────────┬───────────────────────────────┘
                               ▼
┌──────────────────────────────────────────────────────────────┐
│ 4. Merge del Pull Request en GitHub                          │
│    • Si los tests pasan en verde: Clic en Merge              │
└──────────────────────────────┬───────────────────────────────┘
                               ▼
┌──────────────────────────────────────────────────────────────┐
│ 5. Auto-Release & Tagging Autónomo (auto-release.yml)        │
│    • Calcula la versión semántica (v1.0.1, v1.1.0, etc.)    │
│    • Genera el GitHub Release con Changelog                  │
│    • Crea y empuja el nuevo Tag a GitHub                     │
└──────────────────────────────┬───────────────────────────────┘
                               ▼
┌──────────────────────────────────────────────────────────────┐
│ 6. Packagist Sincronizado en Segundos                        │
│    • El Webhook notifica a Packagist                         │
│    • La nueva versión queda disponible en composer           │
└──────────────────────────────────────────────────────────────┘
```

---

## 🛠️ Fase 1: Verificación y Preparación Local

Antes de subir el paquete, validamos que los metadatos de Composer y las pruebas unitarias estén al 100%.

### 1.1. Validar `composer.json`
```bash
cd d:\PROYECTOS_LIBRES\veltra_exosistema\veltra-laravel
composer validate --strict
```
> **Resultado esperado**: `./composer.json is valid`.

### 1.2. Verificar el archivo `.gitignore`
Asegúrate de que en `veltra-laravel/.gitignore` estén excluidas las dependencias locales y archivos temporales:
```gitignore
/vendor
composer.lock
.phpunit.result.cache
.env
.idea
.vscode
*.swp
```
*(Los paquetes de librerías para Composer **NO** deben versionar el archivo `composer.lock`).*

### 1.3. Ejecutar la suite de pruebas local
```bash
vendor/bin/phpunit
```

---

## 🐙 Fase 2: Repositorio en GitHub

El repositorio en GitHub (`stivens19/veltra-laravel`) aloja el código fuente y orquesta las GitHub Actions:

```bash
cd d:\PROYECTOS_LIBRES\veltra_exosistema\veltra-laravel

git init
git branch -M main
git add .
git commit -m "feat: initial release of veltra-laravel enterprise dashboard suite"
git remote add origin https://github.com/stivens19/veltra-laravel.git
git push -u origin main
```

---

## 📦 Fase 3: Registro en Packagist

1. Entra a [https://packagist.org/](https://packagist.org/) con tu cuenta de GitHub (`stivens19`).
2. En el menú superior, haz clic en **[Submit](https://packagist.org/packages/submit)**.
3. Ingresa la URL: `https://github.com/stivens19/veltra-laravel` y confirma el registro.

---

## ⚡ Fase 4: Los 3 Workflows de GitHub Actions

El paquete incluye 3 flujos de trabajo profesionales en `.github/workflows/`:

| Archivo Workflow | Disparador | Propósito |
| :--- | :--- | :--- |
| **`run-tests.yml`** | PRs y Push a `main`/`develop` | Ejecuta `composer validate` y los 38 tests en PHP 8.2–8.4 y Laravel 10–13. |
| **`auto-release.yml`** | Push/Merge a `main` | Calcula SemVer (`v1.0.1`, `v1.1.0`), genera el Release en GitHub con Changelog y empuja el Tag. |
| **`update-packagist.yml`** | Push a `main` y nuevos Tags | Notifica vía API segura a Packagist para actualizar la versión de inmediato. |

---

### ⚙️ Configuración Única en GitHub

1. **Permiso de Escritura para Workflows**:
   - En tu repositorio de GitHub ➔ **Settings** ➔ **Actions** ➔ **General**.
   - En la sección **Workflow permissions**, selecciona:
     - 🔘 **Read and write permissions** *(permite a GitHub Actions crear tags y releases)*.
   - Haz clic en **Save**.

2. **Secret de Packagist**:
   - En GitHub ➔ **Settings** ➔ **Secrets and variables** ➔ **Actions** ➔ **New repository secret**.
   - **Name**: `PACKAGIST_TOKEN`
   - **Secret**: Tu *Safe API Token* copiado de tu perfil de Packagist.

3. **Webhook de Sincronización Inmediata**:
   - En GitHub ➔ **Settings** ➔ **Webhooks** ➔ **Add webhook**.
   - **Payload URL**: `https://packagist.org/api/github?username=stivens19`
   - **Content type**: `application/json`
   - **Secret**: Tu *Safe API Token* de Packagist.
   - **Which events**: *Just the `push` event*.

---

## 🚀 Fase 5: Flujo de Trabajo para Actualizaciones Futuras (Día a Día)

Para mantener la estabilidad de producción, **nunca programes directamente en `main`**. Sigue este flujo estándar de la industria:

### Paso 1: Crear una rama de trabajo
```bash
git checkout -b feature/nuevo-componente-metricas
# o si es corrección de un bug:
git checkout -b fix/error-estilos-dark-mode
```

### Paso 2: Realizar cambios y escribir commits semánticos
GitHub Actions lee los prefijos de tus commits para decidir qué número de versión incrementar:

| Prefijo de Commit | Tipo de Cambio | Incremento SemVer | Ejemplo de Salto |
| :--- | :--- | :--- | :--- |
| **`fix:`** o **`bug:`** | Corrección de error menor | **Patch** | `v1.0.0` ➔ `v1.0.1` |
| **`feat:`** | Nueva característica compatible | **Minor** | `v1.0.0` ➔ `v1.1.0` |
| **`feat!:`** o **`BREAKING CHANGE:`** | Cambio incompatible con versiones anteriores | **Major** | `v1.0.0` ➔ `v2.0.0` |
| **`docs:`** o **`chore:`** | Tareas de mantenimiento o docs | **Patch** (por defecto) | `v1.0.0` ➔ `v1.0.1` |

```bash
git add .
git commit -m "feat: agregar componente de visualizacion de graficas sparkline"
git push origin feature/nuevo-componente-metricas
```

### Paso 3: Abrir un Pull Request en GitHub
1. Ve a tu repositorio en GitHub y haz clic en **Compare & pull request**.
2. **GitHub Actions se ejecutará automáticamente**:
   - En la parte inferior del PR verás las pruebas corriendo en PHP 8.2, 8.3, 8.4 y Laravel 10, 11, 12, 13.
   - Verás el check verde: `All checks have passed`.

### Paso 4: Hacer Merge del Pull Request
1. Haz clic en el botón verde **Merge pull request** y confirma el merge.
2. **¡La magia ocurre sola en segundo plano!**:
   - El workflow `auto-release.yml` detecta el commit `feat: ...`.
   - Crea automáticamente el tag `v1.1.0`.
   - Crea un GitHub Release formal con el resumen de cambios.
   - El Webhook notifica a Packagist.
   - En menos de 10 segundos, la versión `1.1.0` está publicada para todo el mundo.

---

## 🧪 Fase 6: Instalación y Actualización por Usuarios

### Para instalar por primera vez:
```bash
composer require veltra/veltra-laravel
php artisan veltra:install
```

### Para actualizar a la última versión disponible en un proyecto existente:
```bash
composer update veltra/veltra-laravel
```

---

## 🛡️ Fase 7: Badges Profesionales para el `README.md`

```markdown
[![Run Tests](https://github.com/stivens19/veltra-laravel/actions/workflows/run-tests.yml/badge.svg)](https://github.com/stivens19/veltra-laravel/actions/workflows/run-tests.yml)
[![Auto Release](https://github.com/stivens19/veltra-laravel/actions/workflows/auto-release.yml/badge.svg)](https://github.com/stivens19/veltra-laravel/actions/workflows/auto-release.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/veltra/veltra-laravel.svg?style=flat-square&color=06b6d4)](https://packagist.org/packages/veltra/veltra-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/veltra/veltra-laravel.svg?style=flat-square&color=10b981)](https://packagist.org/packages/veltra/veltra-laravel)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![PHP Version Require](https://img.shields.io/packagist/dependency-v/veltra/veltra-laravel/php.svg?style=flat-square&color=8b5cf6)](composer.json)
```
