# Guía Definitiva de Publicación en Packagist: `veltra/veltra-laravel`

Esta guía detalla el procedimiento paso a paso para publicar y mantener el paquete oficial **`veltra/veltra-laravel`** en **[Packagist.org](https://packagist.org/)**, permitiendo que cualquier desarrollador en el mundo lo instale mediante:

```bash
composer require veltra/veltra-laravel
```

---

## 📋 Resumen del Flujo de Trabajo

```
┌─────────────────────────────────┐
│ 1. Validación Local             │  composer validate & phpunit
└────────────────┬────────────────┘
                 ▼
┌─────────────────────────────────┐
│ 2. Repositorio en GitHub        │  git init, commit & push (público)
└────────────────┬────────────────┘
                 ▼
┌─────────────────────────────────┐
│ 3. Enviar a Packagist.org       │  Submit Repository URL (¡Ya registrado!)
└────────────────┬────────────────┘
                 ▼
┌─────────────────────────────────┐
│ 4. GitHub Actions (CI & Sync)   │  Pruebas multi-versión y Auto-Update
└────────────────┬────────────────┘
                 ▼
┌─────────────────────────────────┐
│ 5. Tag Semántico (v1.0.0)       │  git tag v1.0.0 & git push origin v1.0.0
└────────────────┬────────────────┘
                 ▼
┌─────────────────────────────────┐
│ 6. ¡Listo para Instalación!     │  composer require veltra/veltra-laravel
└─────────────────────────────────┘
```

---

## 🛠️ Fase 1: Verificación y Preparación Local

Antes de subir el paquete, debemos validar que los metadatos de Composer y las pruebas unitarias estén al 100%.

### 1.1. Validar `composer.json`
Abre una terminal en la raíz del paquete `veltra-laravel`:

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

> [!IMPORTANT]
> Los paquetes de librerías para Composer **NO** deben versionar el archivo `composer.lock`. Solo las aplicaciones finales lo versionan. Asegúrate de que `composer.lock` esté ignorado.

### 1.3. Ejecutar la suite de pruebas
Asegúrate de que todos los tests pasen exitosamente con Orchestra Testbench:

```bash
vendor/bin/phpunit
```

---

## 🐙 Fase 2: Crear el Repositorio en GitHub

Packagist no almacena el código fuente directamente; lee y sincroniza los metadatos desde un repositorio Git público (GitHub, GitLab o Bitbucket).

### 2.1. Crear el repositorio en GitHub
1. Entra a [github.com/new](https://github.com/new).
2. **Nombre del repositorio**: `veltra-laravel` (o bajo tu organización `veltra-ui/veltra-laravel`).
3. **Visibilidad**: Debe ser **Público** (Public) para que Packagist pueda leerlo gratuitamente.
4. **No inicializar**: No agregues README, .gitignore ni licencia desde la interfaz web (ya existen localmente en tu carpeta).
5. Haz clic en **Create repository**.

### 2.2. Inicializar Git y subir el código
Desde la carpeta `veltra-laravel`:

```bash
cd d:\PROYECTOS_LIBRES\veltra_exosistema\veltra-laravel

# Inicializar Git si aún no existe
git init

# Crear rama principal main
git branch -M main

# Agregar todos los archivos
git add .

# Crear el primer commit
git commit -m "feat: initial release of veltra-laravel enterprise dashboard suite"

# Vincular con tu repositorio remoto de GitHub (reemplaza con tu usuario real)
git remote add origin https://github.com/<TU_USUARIO_O_ORGANIZACION>/veltra-laravel.git

# Subir a GitHub
git push -u origin main
```

---

## 📦 Fase 3: Registro y Publicación en Packagist

### 3.1. Iniciar sesión en Packagist
1. Ve a [https://packagist.org/](https://packagist.org/).
2. Inicia sesión con tu cuenta de GitHub (usuario: **`stivens19`**).

### 3.2. Enviar el paquete
*(Si ya lo enviaste, este paso está completo)*:
1. En el menú superior de Packagist, haz clic en **[Submit](https://packagist.org/packages/submit)**.
2. En el campo **Repository URL (Git/Svn/Hg)**, pega la URL pública de tu repositorio de GitHub:
   ```text
   https://github.com/<TU_USUARIO_O_ORGANIZACION>/veltra-laravel
   ```
3. Haz clic en **Check** y luego en **Submit**.

---

## ⚡ Fase 4: Automatización con GitHub Actions (CI & Auto-Update)

El paquete incluye flujos de trabajo profesionales de **GitHub Actions** en `.github/workflows/`:

### 1. `run-tests.yml` (Integración Continua - CI)
Ejecuta la validación estricta de Composer y la suite de pruebas PHPUnit en una matriz de entornos:
* **PHP**: `8.2`, `8.3`, `8.4`
* **Laravel**: `10.*`, `11.*`, `12.*`, `13.*`
* **Orchestra Testbench**: `8.*`, `9.*`, `10.*`, `11.*`
* Se ejecuta automáticamente en cada `push` a ramas principales y en cada `tag` de versión.

### 2. `update-packagist.yml` (Sincronización Automática con Packagist)
Envía un webhook automático a Packagist en cada `push` a `main` y en cada nuevo `tag` (`v*`).

---

### ⚙️ Configuración del Secret en GitHub

Para que el workflow de sincronización tenga acceso seguro a Packagist:

1. **Obtener tu Token en Packagist**:
   - Entra a [https://packagist.org/profile/](https://packagist.org/profile/).
   - En la sección **Your API Tokens**, haz clic en **`Show Safe API Token`** (o `Show Main API Token`).
   - Copia el código alfanumérico del token.

2. **Crear el Secret en GitHub**:
   - Ve a tu repositorio en GitHub: `https://github.com/<TU_USUARIO>/veltra-laravel`.
   - Haz clic en **Settings** (pestaña superior derecha).
   - En el menú lateral izquierdo, haz clic en **Secrets and variables** ➔ **Actions**.
   - Haz clic en el botón verde **New repository secret**.
   - Llena los datos:
     - **Name**: `PACKAGIST_TOKEN`
     - **Secret**: Pega el API Token que copiaste de Packagist.
   - Haz clic en **Add secret**.

*(Opcional: puedes agregar otro secret llamado `PACKAGIST_USERNAME` con valor `stivens19`, aunque por defecto el workflow ya toma `stivens19`).*

---

### 📌 Método Alternativo: Webhook directo en GitHub
Si además deseas configurar el Webhook nativo de GitHub:
1. En GitHub -> Repositorio `veltra-laravel` -> **Settings** -> **Webhooks** -> **Add webhook**.
2. **Payload URL**: `https://packagist.org/api/github?username=stivens19`
3. **Content type**: `application/json` *(⚠️ Importante: NO urlencoded)*
4. **Secret**: Tu *Safe API Token* de Packagist.
5. **Which events**: *Just the `push` event*.
6. Haz clic en **Add webhook**.

---

## 🏷️ Fase 5: Versionado Semántico y Creación de Tags (SemVer)

Para que los desarrolladores puedan instalar una versión estable sin requerir banderas `dev-main`, **debes publicar un tag semántico**.

### 5.1. Crear el primer Tag de versión (v1.0.0)
Desde tu terminal:

```bash
cd d:\PROYECTOS_LIBRES\veltra_exosistema\veltra-laravel

# Crear el tag v1.0.0
git tag -a v1.0.0 -m "Release v1.0.0: Soporte completo para Laravel 10-13, Livewire 3/4, Vue, React y Svelte"

# Subir el tag a GitHub
git push origin v1.0.0
```

> **Al hacer este push**: GitHub Actions ejecutará automáticamente la suite de pruebas y llamará a la API de Packagist para registrar la versión estable `1.0.0`.

### 5.2. Crear el Release en GitHub (Recomendado para DX)
1. Ve a tu repositorio en GitHub -> pestaña **Releases** (a la derecha).
2. Haz clic en **Draft a new release**.
3. Selecciona el tag `v1.0.0`.
4. **Release title**: `v1.0.0 - Enterprise Admin Suite`
5. Describe las novedades:
   - Soporte para Tailwind CSS v4.
   - Comandos Artisan `veltra:install` con selector interactivo (Blade/Livewire, React, Vue, Svelte).
   - Generadores `veltra:make:crud` y servidor MCP para agentes de IA.
6. Haz clic en **Publish release**.

---

## 🧪 Fase 6: Prueba de Instalación en Limpio (Smoke Test)

Prueba la instalación del paquete desde cualquier proyecto Laravel:

```bash
# 1. Instalar el paquete desde Packagist
composer require veltra/veltra-laravel

# 2. Ejecutar el instalador interactivo
php artisan veltra:install

# 3. Probar el comando de generación de componentes
php artisan veltra:make:component MetricCard
```

---

## 🛡️ Fase 7: Badges de Estado para tu `README.md`

Agrega estos badges al inicio de tu [README.md](file:///d:/PROYECTOS_LIBRES/veltra_exosistema/veltra-laravel/README.md) para reflejar la calidad del paquete:

```markdown
[![Run Tests](https://github.com/stivens19/veltra-laravel/actions/workflows/run-tests.yml/badge.svg)](https://github.com/stivens19/veltra-laravel/actions/workflows/run-tests.yml)
[![Notify Packagist](https://github.com/stivens19/veltra-laravel/actions/workflows/update-packagist.yml/badge.svg)](https://github.com/stivens19/veltra-laravel/actions/workflows/update-packagist.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/veltra/veltra-laravel.svg?style=flat-square&color=06b6d4)](https://packagist.org/packages/veltra/veltra-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/veltra/veltra-laravel.svg?style=flat-square&color=10b981)](https://packagist.org/packages/veltra/veltra-laravel)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![PHP Version Require](https://img.shields.io/packagist/dependency-v/veltra/veltra-laravel/php.svg?style=flat-square&color=8b5cf6)](composer.json)
```

---

## 🔄 Flujo de Trabajo para Futuras Actualizaciones

| Tipo de Cambio | Ejemplo de Cambio | Próxima Versión | Comando Git |
| :--- | :--- | :--- | :--- |
| **Patch (Corrección)** | Bugfix en CSS o layout | `v1.0.1` | `git tag -a v1.0.1 -m "fix: ..." && git push origin v1.0.1` |
| **Minor (Novedad)** | Nuevo componente o soporte de stack | `v1.1.0` | `git tag -a v1.1.0 -m "feat: ..." && git push origin v1.1.0` |
| **Major (Breaking)** | Cambio radical en contratos o arquitectura | `v2.0.0` | `git tag -a v2.0.0 -m "feat!: ..." && git push origin v2.0.0` |
