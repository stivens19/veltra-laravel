---
name: veltra-security
description: >-
  Manual avanzado de ciberseguridad y hardening para Laravel basado en el estándar OWASP Top 10.
  Instruye a desarrolladores y agentes de IA para blindar aplicaciones, controladores,
  modelos Eloquent, autenticación, APIs, subida de archivos y vistas contra vulnerabilidades
  críticas (IDOR, Injection, XSS, CSRF, Broken Access Control, Mass Assignment, SSRF).
---

# 🛡️ VeltraSecurity: Manual de Ciberseguridad & Hardening OWASP Top 10 para Laravel

VeltraSecurity establece el estándar de seguridad industrial para aplicaciones desarrolladas con **Laravel** (versiones 10, 11, 12 y 13) y la suite **VeltraUI**. 

Este documento es una directiva obligatoria para desarrolladores y agentes de inteligencia artificial (Cursor, Windsurf, Claude Code, Copilot, Antigravity). Cada línea de código generada debe cumplir con el principio de **Zero-Trust (Confianza Cero)** y **Defensa en Profundidad**.

---

## 🧭 Principios Fundamentales Innegociables

1. **Nunca confíes en el input del cliente:** Todo dato que provenga de una petición HTTP (`$request`) es potencialmente hostil hasta ser validado y sanitizado mediante un `FormRequest`.
2. **Denegación por Defecto (Secure by Default):** Todo endpoint, ruta, acción o recurso es privado y requiere autenticación y autorización explícita a menos que se justifique lo contrario.
3. **Principio del Menor Privilegio (PoLP):** Los usuarios, roles y procesos deben tener acceso únicamente a los recursos estrictamente necesarios para su función.
4. **Falla Segura (Fail-Safe Defaults):** Si una verificación de autorización o validación falla o arroja una excepción, la respuesta por defecto debe ser denegar el acceso (`403 Forbidden` o `401 Unauthorized`).

---

## 🎯 OWASP Top 10: Vulnerabilidades, Reglas & Patrones de Código

```
┌────────────────────────────────────────────────────────────────────────┐
│                   MATRIZ DE AMENAZAS OWASP TOP 10                      │
├─────────┬──────────────────────────────────┬───────────────────────────┤
│ ID      │ Categoría OWASP                  │ Mitigación en Laravel     │
├─────────┼──────────────────────────────────┼───────────────────────────┤
│ A01     │ Broken Access Control (IDOR)     │ Policies + Scopes + DTOs  │
│ A02     │ Cryptographic Failures           │ Hash, Crypt & $hidden     │
│ A03     │ Injection (SQL / XSS / Command)  │ PDO Bindings + Blade e()  │
│ A04     │ Insecure Design                  │ FormRequest + State Machine│
│ A05     │ Security Misconfiguration        │ veltra:build + CSP Headers│
│ A06     │ Vulnerable Components            │ composer audit + lockfile │
│ A07     │ Auth & Identification Failures   │ RateLimiter + 2FA + Regen │
│ A08     │ Software & Data Integrity        │ MIME Magic Bytes + Hash   │
│ A09     │ Security Logging & Monitoring    │ Audit Trail + Masking PII │
│ A10     │ SSRF (Server-Side Request Forgery│ IP Filtering + DNS Checks │
└─────────┴──────────────────────────────────┴───────────────────────────┘
```

---

### A01:2021 — Broken Access Control (Control de Acceso Roto & IDOR)

Es la vulnerabilidad #1 en paneles administrativos. Ocurre cuando un usuario modifica identificadores en peticiones (ej: `/orders/105/edit` cambiando a `/orders/104/edit`) accediendo a registros de otros usuarios o inquilinos (*Insecure Direct Object Reference - IDOR*).

#### ❌ Código Vulnerable:
```php
// Control de acceso inexistente o débil
public function show($id)
{
    // Peligro: cualquier usuario autenticado puede ver la orden de cualquier otro
    $order = Order::findOrFail($id);
    return view('orders.show', compact('order'));
}

public function update(Request $request, Order $order)
{
    // Peligro: Mass Assignment sin validación y sin verificar permisos
    $order->update($request->all());
    return redirect()->back();
}
```

#### ✅ Código Blindado (Patrón Veltra):
```php
// 1. Uso de FormRequest dedicado
public function update(UpdateOrderRequest $request, Order $order)
{
    // 2. Verificación explícita de Policy
    $this->authorize('update', $order);

    // 3. Solo campos estrictamente validados
    $order->update($request->validated());

    return redirect()->route('orders.show', $order)
        ->with('success', 'Orden actualizada con éxito.');
}

// 4. En controladores resource, blindar en el constructor:
public function __construct()
{
    $this->authorizeResource(Order::class, 'order');
}

// 5. Búsqueda scoped al propietario/tenant:
$order = $request->user()->orders()->findOrFail($id);
```

#### Reglas de Oro A01:
- **Prohibido** usar `$request->all()` para inserciones o actualizaciones.
- **Obligatorio** definir `$fillable` explícito en los Modelos Eloquent o `$guarded = ['id']`.
- **Obligatorio** crear una `Policy` (`php artisan make:policy ModelPolicy --model=Model`) para cada modelo gestionado en el panel.

---

### A02:2021 — Cryptographic Failures (Fuga de Datos Sensibles)

Falta de protección de datos confidenciales (contraseñas, tokens de API, secretos de pago, datos personales PII).

#### ❌ Código Vulnerable:
```php
// Guardar secretos en texto plano o exponerlos en respuestas JSON
$user->api_token = $request->token; // Texto plano en BD
return response()->json($user); // Expone password, remember_token, stripe_id
```

#### ✅ Código Blindado (Patrón Veltra):
```php
// 1. Ocultar atributos sensibles en el Modelo
class User extends Authenticatable
{
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'stripe_id',
        'pm_last_four',
    ];

    // 2. Encriptación nativa de atributos Eloquent (AES-256-CBC)
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'api_token' => 'encrypted',
            'bank_account' => 'encrypted',
            'password' => 'hashed', // Hashing automático en Laravel 11/12
        ];
    }
}

// 3. Cifrado manual seguro para payloads externos
use Illuminate\Support\Facades\Crypt;
$encryptedPayload = Crypt::encryptString($sensibleData);
$decrypted = Crypt::decryptString($encryptedPayload);
```

---

### A03:2021 — Injection (Inyecciones SQL, XSS & Command Injection)

Los ataques de inyección ocurren cuando datos suministrados por el atacante son interpretados como comandos por el motor SQL, el navegador (XSS) o el sistema operativo.

#### 1. Inyección SQL
- **❌ Vulnerable:** Concatenar variables en queries crudas:
  ```php
  $users = DB::select("SELECT * FROM users WHERE status = '{$status}' AND name LIKE '%{$search}%'");
  ```
- **✅ Blindado:** Usar bindings preparados de PDO:
  ```php
  $users = DB::select(
      'SELECT * FROM users WHERE status = :status AND name LIKE :search',
      ['status' => $status, 'search' => "%{$search}%"]
  );
  // O mejor aún, Fluent Query Builder:
  $users = User::where('status', $status)
      ->where('name', 'like', "%{$search}%")
      ->get();
  ```

#### 2. Cross-Site Scripting (XSS) en Vistas
- **❌ Vulnerable:** Usar la sintaxis sin escapar `{!! !!}` en Blade:
  ```blade
  {{-- Peligro mortal: si $comment->body contiene <script>, se ejecuta en el navegador --}}
  <div class="user-comment">{!! $comment->body !!}</div>
  ```
- **✅ Blindado:**
  ```blade
  {{-- 1. Interpolación escapada por defecto (convierte < en &lt; vía htmlspecialchars) --}}
  <div class="user-comment">{{ $comment->body }}</div>

  {{-- 2. Si se requiere soportar Markdown o HTML enriquecido, sanitizar con HTMLPurifier --}}
  <div class="user-comment prose dark:prose-invert">
      {!! clean($comment->html_body) !!}
  </div>
  ```

#### 3. CSV / Formula Injection
Cuando un usuario ingresa `=CMD('calc')` o `@SUM(...)` en un campo de texto y luego un administrador exporta la tabla a Excel/CSV, la hoja de cálculo puede ejecutar código.
- **✅ Mitigación:** Al exportar a CSV, si un campo comienza con `=`, `+`, `-`, `@`, `\t`, anteponer un apóstrofe `'` para forzar su interpretación como texto literal.

---

### A04:2021 — Insecure Design (Diseño Inseguro & Lógica de Negocio)

Vulnerabilidades que no son errores de sintaxis sino defectos en la concepción del flujo de negocio.

#### Reglas de Validación con `FormRequest`:
```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Product::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'trim'],
            'sku' => ['required', 'string', 'uppercase', 'alpha_dash', Rule::unique('products', 'sku')],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', Rule::exists('categories', 'id')->where('is_active', true)],
        ];
    }
}
```

---

### A05:2021 — Security Misconfiguration (Mala Configuración)

Ocurre cuando el entorno de producción tiene expuestos datos de depuración o rutas de prueba.

#### Reglas de Hardening en VeltraLaravel:
1. **Desactivar Rutas de Demostración:**
   ```bash
   php artisan veltra:build --clean --force
   ```
   Garantiza que `VELTRA_ROUTES_ENABLED=false` en `.env` y `config/veltra.php`.
2. **Desactivar Debug Mode en Producción:**
   En `.env`:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```
3. **Cabeceras HTTP de Seguridad (Security Headers):**
   Registrar en el pipeline de middlewares globales:
   ```php
   // app/Http/Middleware/SecurityHeadersMiddleware.php
   public function handle(Request $request, Closure $next): Response
   {
       $response = $next($request);

       $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
       $response->headers->set('X-Content-Type-Options', 'nosniff');
       $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
       $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
       $response->headers->set('X-XSS-Protection', '1; mode=block');

       if (app()->environment('production')) {
           $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
       }

       return $response;
   }
   ```

---

### A06:2021 — Vulnerable and Outdated Components

Dependencias de Composer o NPM desactualizadas con vulnerabilidades conocidas (CVEs).

#### Protocolo de Auditoría:
```bash
# Auditar paquetes de PHP
composer audit

# Auditar paquetes de Frontend (Node)
npm audit

# Diagnóstico integral de Veltra
php artisan veltra:doctor
```

---

### A07:2021 — Identification and Authentication Failures (Fuerza Bruta & Sesiones)

Defectos en el proceso de autenticación que permiten robo de sesiones o adivinación de credenciales.

#### 1. Protección contra Session Fixation
Siempre regenerar el ID de sesión tras un inicio de sesión exitoso:
```php
if (Auth::attempt($credentials, $request->boolean('remember'))) {
    $request->session()->regenerate();
    return redirect()->intended('/dashboard');
}
```

#### 2. Cierre de Sesión Seguro
```php
public function logout(Request $request)
{
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
```

#### 3. Rate Limiting en Inicios de Sesión
```php
// En bootstrap/app.php o AppServiceProvider
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('login', function (Request $request) {
    $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    return Limit::perMinute(5)->by($throttleKey);
});
```

#### 4. Reglas de Contraseña Robustas
```php
use Illuminate\Validation\Rules\Password;

'password' => [
    'required',
    'confirmed',
    Password::min(12)
        ->letters()
        ->mixedCase()
        ->numbers()
        ->symbols()
        ->uncompromised(), // Consulta la base de datos de HaveIBeenPwned
],
```

---

### A08:2021 — Software and Data Integrity Failures (Subida Segura de Archivos)

Subir un archivo malicioso (como un script `.php` o un `.svg` con código JavaScript) que el servidor ejecuta o sirve directamente al navegador.

#### ❌ Código Vulnerable:
```php
// Peligro: guarda con el nombre original y confía en la extensión del cliente
$file = $request->file('avatar');
$file->move(public_path('uploads'), $file->getClientOriginalName());
```

#### ✅ Código Blindado (Patrón Veltra):
```php
// 1. Validación estricta en FormRequest
public function rules(): array
{
    return [
        'avatar' => [
            'required',
            'file',
            'max:2048', // Máximo 2MB
            // Validar Magic Bytes / MIME real, no solo extensión:
            'mimetypes:image/jpeg,image/png,image/webp',
            'mimes:jpg,jpeg,png,webp',
        ],
    ];
}

// 2. Almacenamiento seguro en disco privado con nombre hash único
public function updateAvatar(UpdateAvatarRequest $request)
{
    $path = $request->file('avatar')->store('avatars', 'private');

    $request->user()->update([
        'avatar_path' => $path,
    ]);
}
```

> [!WARNING]
> **Peligro con archivos SVG:** Un archivo SVG es código XML y puede contener etiquetas `<script>` o eventos `onload="alert(1)"`. Nunca permitas la carga de archivos SVG públicos a menos que se procesen previamente con un sanitizador SVG dedicado (ej: `enshrined/svg-sanitize`).

---

### A09:2021 — Security Logging and Monitoring Failures (Auditoría & Trazabilidad)

Falta de registro de eventos de seguridad que impide detectar brechas a tiempo.

#### Patrón de Registro de Auditoría:
```php
use Illuminate\Support\Facades\Log;

// Registrar eventos de seguridad críticos
Log::warning('Intento de acceso no autorizado detectado', [
    'event' => 'unauthorized_access_attempt',
    'user_id' => auth()->id() ?? 'guest',
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'target_route' => $request->path(),
    'timestamp' => now()->toIso8601String(),
]);

// ⚠️ NUNCA registrar contraseñas ni datos sensibles en los logs:
// ❌ Log::info('Login attempt', $request->all()); // EXPONE PASSWORDS EN LOGS
// ✅ Log::info('Login attempt', ['email' => $request->input('email')]);
```

---

### A10:2021 — Server-Side Request Forgery (SSRF)

Ocurre cuando la aplicación realiza peticiones HTTP salientes a URLs suministradas por el usuario (ej: descargar una imagen remota o configurar un Webhook) y el atacante ingresa IPs privadas (`http://169.254.169.254` para robar credenciales de AWS/GCP, o `http://127.0.0.1:3306`).

#### ✅ Código Blindado para Peticiones Salientes:
```php
use Illuminate\Support\Facades\Http;

public function dispatchWebhook(string $url, array $payload)
{
    // 1. Validar formato de URL
    if (! filter_var($url, FILTER_VALIDATE_URL)) {
        throw new \InvalidArgumentException('URL inválida.');
    }

    $parsed = parse_url($url);
    $host = $parsed['host'] ?? '';

    // 2. Resolver dirección IP
    $ip = gethostbyname($host);

    // 3. Bloquear rangos de IP privados, locales y de loopback
    if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        throw new \SecurityException("Petición rechazada: La IP de destino [{$ip}] es privada o restringida.");
    }

    // 4. Ejecutar con Timeout estricto y sin redirecciones arbitrarias
    return Http::timeout(5)
        ->withoutRedirecting()
        ->post($url, $payload);
}
```

---

## 🔒 Checklist Rápido de Pre-Commit & Despliegue para IAs

Antes de entregar cualquier CRUD, página, endpoint o controlador, la IA debe verificar:

- [ ] **CSRF:** ¿Todos los formularios Blade incluyen `@csrf` y las peticiones `fetch()` envían la cabecera `X-CSRF-TOKEN`?
- [ ] **Autorización:** ¿Se utiliza `$this->authorize()` o `authorizeResource()` en el controlador?
- [ ] **IDOR:** ¿Las consultas a modelos verifican que pertenezcan al usuario o inquilino actual?
- [ ] **Mass Assignment:** ¿Se usa exclusivamente `$request->validated()` y nunca `$request->all()`?
- [ ] **XSS:** ¿Se evitan interpolaciones `{!! !!}` con datos de usuario?
- [ ] **Archivos:** ¿Los archivos se validan por `mimetypes` y se guardan con nombres hash aleatorios?
- [ ] **Passphrase/Secrets:** ¿Están los campos sensibles en `$hidden` y se encriptan con `encrypted`?
- [ ] **Producción:** ¿Se corrió `php artisan veltra:build` para apagar demos y cachear configuración?
