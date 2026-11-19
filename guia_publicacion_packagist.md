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
│ 3. Enviar a Packagist.org       │  Submit Repository URL
└────────────────┬────────────────┘
                 ▼
┌─────────────────────────────────┐
│ 4. Webhook / GitHub App         │  Sincronización automática de releases
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

> **Resultado esperado**: `composer.json is valid`. Si hay advertencias de campos faltantes o formato, corrígelas antes de continuar.

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

# Vincular con tu repositorio remoto de GitHub (reemplaza con tu URL real)
git remote add origin https://github.com/<TU_USUARIO_O_ORGANIZACION>/veltra-laravel.git

# Subir a GitHub
git push -u origin main
```

---

## 📦 Fase 3: Registro y Publicación en Packagist

### 3.1. Iniciar sesión en Packagist
1. Ve a [https://packagist.org/](https://packagist.org/).
2. Haz clic en **Login** (se recomienda usar **Login with GitHub** para vincular tus permisos automáticamente).

### 3.2. Enviar el paquete
1. En el menú superior de Packagist, haz clic en **[Submit](https://packagist.org/packages/submit)**.
2. En el campo **Repository URL (Git/Svn/Hg)**, pega la URL pública de tu repositorio de GitHub:
   ```text
   https://github.com/<TU_USUARIO_O_ORGANIZACION>/veltra-laravel
   ```
3. Haz clic en **Check**.
   - Packagist clonará temporalmente los metadatos y leerá el `name` definido en tu `composer.json` (`veltra/veltra-laravel`).
4. Haz clic en **Submit**.

> [!NOTE]
> Si el prefijo/vendor `veltra` ya fue reclamado por otro desarrollador, Packagist te avisará. En ese caso, puedes cambiar el vendor a tu nombre de usuario de GitHub o corporativo en `composer.json` (por ejemplo, `tunombre/veltra-laravel` o `veltra-ui/veltra-laravel`).

---

## ⚡ Fase 4: Configurar Auto-Update (Sincronización Automática)

Para que Packagist detecte automáticamente cada `git push` o nuevo release sin tener que presionar manualmente el botón "Update" en la web de Packagist:

### Método Recomendado: Packagist GitHub App
1. Entra en Packagist a tu perfil: [https://packagist.org/profile/](https://packagist.org/profile/).
2. Verás un banner o botón para conectar la **Packagist GitHub Service / App**.
3. Haz clic en conectar y autoriza a Packagist acceso a tus repositorios públicos.
4. Con esto, cada vez que hagas un commit o crees un tag, GitHub notificará a Packagist al instante.

### Método Alternativo: Webhook Manual
Si prefieres un Webhook directo en GitHub:
1. En GitHub, ve a tu repositorio `veltra-laravel` -> **Settings** -> **Webhooks** -> **Add webhook**.
2. **Payload URL**: `https://packagist.org/api/github?username=<TU_USUARIO_PACKAGIST>`
3. **Content type**: `application/json`
4. **Secret**: Tu token de API de Packagist (obtenido en tu perfil de Packagist -> *Show API Token*).
5. **Which events**: Selecciona *Just the push event*.
6. Haz clic en **Add webhook**.

---

## 🏷️ Fase 5: Versionado Semántico y Creación de Tags (SemVer)

Por defecto, Packagist solo ofrecerá la rama `dev-main`. Para que los desarrolladores puedan instalar una versión estable sin banderas de estabilidad, **debes publicar un tag semántico**.

### 5.1. Crear el primer Tag de versión (v1.0.0)
Desde tu terminal:

```bash
cd d:\PROYECTOS_LIBRES\veltra_exosistema\veltra-laravel

# Crear el tag v1.0.0
git tag -a v1.0.0 -m "Release v1.0.0: Soporte completo para Laravel 10-13, Livewire 3/4, Vue, React y Svelte"

# Subir el tag a GitHub
git push origin v1.0.0
```

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

Packagist se actualizará inmediatamente mostrando la versión estable **`1.0.0`**.

---

## 🧪 Fase 6: Prueba de Instalación en Limpio (Smoke Test)

Para verificar que el paquete esté funcionando desde Packagist en todo el mundo, crea un proyecto Laravel de prueba o abre uno existente:

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

Agrega estos badges al inicio de tu [README.md](file:///d:/PROYECTOS_LIBRES/veltra_exosistema/veltra-laravel/README.md) para dar una imagen profesional y de confianza a la comunidad:

```markdown
[![Latest Version on Packagist](https://img.shields.io/packagist/v/veltra/veltra-laravel.svg?style=flat-square&color=06b6d4)](https://packagist.org/packages/veltra/veltra-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/veltra/veltra-laravel.svg?style=flat-square&color=10b981)](https://packagist.org/packages/veltra/veltra-laravel)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![PHP Version Require](https://img.shields.io/packagist/dependency-v/veltra/veltra-laravel/php.svg?style=flat-square&color=8b5cf6)](composer.json)
```

---

## 🔄 Flujo de Trabajo para Futuras Actualizaciones

Cuando agregues nuevas características o corrijas errores:

| Tipo de Cambio | Ejemplo de Cambio | Próxima Versión | Comando Git |
| :--- | :--- | :--- | :--- |
| **Patch (Corrección)** | Bugfix en CSS o layout | `v1.0.1` | `git tag -a v1.0.1 -m "fix: ..." && git push origin v1.0.1` |
| **Minor (Novedad)** | Nuevo componente o soporte de stack | `v1.1.0` | `git tag -a v1.1.0 -m "feat: ..." && git push origin v1.1.0` |
| **Major (Breaking)** | Cambio radical en contratos o arquitectura | `v2.0.0` | `git tag -a v2.0.0 -m "feat!: ..." && git push origin v2.0.0` |
