---
name: veltra-testing
description: >-
  Manual avanzado de pruebas automatizadas y aseguramiento de calidad (QA) para Laravel.
  Instruye a desarrolladores y agentes de IA para escribir tests de alta fidelidad con Pest
  y PHPUnit para controladores CRUD, autorización y roles (RBAC), componentes Livewire,
  páginas Inertia (React/Vue/Svelte), y emuladores (Fakes) de colas, almacenamiento y correos.
---

# 🧪 VeltraTesting: Manual de Pruebas Automatizadas con Pest & PHPUnit para Laravel

VeltraTesting establece la metodología oficial para garantizar que cada controlador, CRUD, componente y regla de autorización generada con **VeltraUI** cuente con una cobertura de pruebas automatizadas sólida, rápida y mantenible.

Este documento instruye a desarrolladores y agentes de IA sobre cómo escribir tests unitarios y funcionales utilizando **Pest PHP** (sintaxis recomendada) y **PHPUnit**.

---

## 🎯 1. Filosofía de Pruebas en VeltraUI

1. **Cada CRUD debe nacer con sus pruebas:** No se da por terminado un módulo generado por `veltra:crud` sin su archivo de test de Feature correspondiente.
2. **Prueba el Comportamiento, no la Implementación:** Verifica que la respuesta HTTP sea la esperada (`assertOk()`, `assertRedirect()`, `assertForbidden()`), que los datos existan en la base de datos (`assertDatabaseHas()`) y que las vistas rendericen los datos correctos.
3. **Prueba siempre el "Camino Infeliz" (*Sad Path*):** No pruebes solo cuando todo sale bien; prueba que los errores de validación fallen con `assertSessionHasErrors()` y que los accesos indebidos sean bloqueados con `assertForbidden()`.
4. **Aislamiento con Fakes:** Nunca envíes correos reales, no toques discos de almacenamiento en la nube ni despaches peticiones HTTP externas durante las pruebas. Usa los Fakes nativos de Laravel.

---

## 🏗️ 2. Estructura Estándar de Pruebas para un CRUD

Cada módulo CRUD generado debe contar con pruebas para los 5 escenarios fundamentales:
1. `index`: El usuario autorizado ve la lista paginada y puede buscar.
2. `create / store`: El formulario valida campos requeridos y guarda el registro con datos válidos.
3. `show / edit`: El usuario ve el registro correcto (prevención de IDOR).
4. `update`: Modifica los datos validados y redirige con mensaje de éxito.
5. `destroy`: Elimina el registro y lo remueve de la base de datos.

### Ejemplo Completo con Pest PHP (`tests/Feature/CustomerCrudTest.php`):

```php
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Crear un usuario con permisos para las pruebas
    $this->user = User::factory()->create();
});

test('un usuario autenticado puede ver el listado de clientes', function () {
    Customer::factory()->count(3)->create();

    $response = $this->actingAs($this->user)->get(route('customers.index'));

    $response->assertOk()
        ->assertViewIs('customers.index')
        ->assertViewHas('customers');
});

test('el listado filtra clientes por término de búsqueda', function () {
    $target = Customer::factory()->create(['name' => 'Acme Corporation']);
    $other = Customer::factory()->create(['name' => 'Stark Industries']);

    $response = $this->actingAs($this->user)
        ->get(route('customers.index', ['search' => 'Acme']));

    $response->assertOk()
        ->assertSee('Acme Corporation')
        ->assertDontSee('Stark Industries');
});

test('no se puede crear un cliente con datos inválidos', function () {
    $response = $this->actingAs($this->user)
        ->post(route('customers.store'), [
            'name' => '',
            'email' => 'correo-invalido',
        ]);

    $response->assertSessionHasErrors(['name', 'email']);
    $this->assertDatabaseEmpty('customers');
});

test('un usuario puede crear un cliente con datos válidos', function () {
    $payload = [
        'name' => 'Global Logistics Inc',
        'email' => 'contact@globallogistics.com',
        'phone' => '+123456789',
        'status' => 'active',
    ];

    $response = $this->actingAs($this->user)
        ->post(route('customers.store'), $payload);

    $response->assertRedirect(route('customers.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('customers', [
        'email' => 'contact@globallogistics.com',
        'status' => 'active',
    ]);
});

test('un usuario puede actualizar un cliente existente', function () {
    $customer = Customer::factory()->create(['name' => 'Nombre Antiguo']);

    $response = $this->actingAs($this->user)
        ->put(route('customers.update', $customer), [
            'name' => 'Nombre Actualizado',
            'email' => $customer->email,
            'phone' => $customer->phone,
            'status' => 'active',
        ]);

    $response->assertRedirect(route('customers.index'));
    expect($customer->fresh()->name)->toBe('Nombre Actualizado');
});

test('un usuario puede eliminar un cliente', function () {
    $customer = Customer::factory()->create();

    $response = $this->actingAs($this->user)
        ->delete(route('customers.destroy', $customer));

    $response->assertRedirect(route('customers.index'));
    $this->assertModelMissing($customer);
});
```

---

## 🔐 3. Pruebas de Autorización & Roles (RBAC)

Verificar siempre que los usuarios sin privilegios sean rechazados inmediatamente:

```php
test('los invitados son redirigidos al login al intentar acceder al crud', function () {
    $response = $this->get(route('customers.index'));
    $response->assertRedirect(route('login'));
});

test('un usuario sin permiso no puede eliminar registros', function () {
    // Usuario sin el rol/permiso requerido
    $normalUser = User::factory()->create(['role' => 'viewer']);
    $customer = Customer::factory()->create();

    $response = $this->actingAs($normalUser)
        ->delete(route('customers.destroy', $customer));

    // Debe retornar 403 Forbidden
    $response->assertForbidden();
    $this->assertModelExists($customer);
});

test('un usuario solo puede acceder a registros de su propio inquilino (anti-IDOR)', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $orderOfUserB = Order::factory()->create(['user_id' => $userB->id]);

    // Usuario A intenta acceder a la orden de Usuario B
    $response = $this->actingAs($userA)
        ->get(route('orders.show', $orderOfUserB));

    $response->assertForbidden();
});
```

---

## 🔀 4. Pruebas según el Stack Frontend Activo

### 1. Pruebas en Livewire (Blade / Volt)
```php
use Livewire\Livewire;
use App\Livewire\CustomerTable;

test('el componente livewire de clientes busca y pagina correctamente', function () {
    Customer::factory()->create(['name' => 'Empresa Alfa']);
    Customer::factory()->create(['name' => 'Empresa Beta']);

    Livewire::test(CustomerTable::class)
        ->assertSee('Empresa Alfa')
        ->assertSee('Empresa Beta')
        ->set('search', 'Alfa')
        ->assertSee('Empresa Alfa')
        ->assertDontSee('Empresa Beta');
});
```

### 2. Pruebas en Inertia (React, Vue, Svelte)
```php
use Inertia\Testing\AssertableInertia as Assert;

test('la página de dashboard de inertia renderiza las props correctas', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('metrics.total_revenue')
            ->has('metrics.new_users')
            ->where('auth.user.id', $user->id)
        );
});
```

---

## 🎭 5. Uso Correcto de Emuladores (Fakes)

Para mantener los tests veloces y sin efectos colaterales en servicios externos:

```php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

test('la subida de avatar almacena el archivo en disco privado y despacha job', function () {
    Storage::fake('private');
    Queue::fake();

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg', 400, 400);

    $response = $this->actingAs($user)
        ->post(route('profile.avatar'), ['avatar' => $file]);

    $response->assertRedirect();

    // 1. Verificar almacenamiento en disco
    Storage::disk('private')->assertExists('avatars/' . $file->hashName());

    // 2. Verificar que se encoló el trabajo de redimensionamiento
    Queue::assertPushed(\App\Jobs\ResizeAvatarJob::class);
});
```

---

## 📋 Checklist de Testing para IAs y Desarrolladores

Al generar o modificar código:
- [ ] ¿Se creó o actualizó el archivo de pruebas en `tests/Feature/` o `tests/Unit/`?
- [ ] ¿Se probaron tanto los casos exitosos (`200`/`302`) como los casos de validación fallida (`422`)?
- [ ] ¿Se incluyó al menos un test de autorización (`403 Forbidden`) para usuarios no autorizados?
- [ ] ¿Se utilizaron `Storage::fake()`, `Mail::fake()` o `Queue::fake()` si el código toca archivos o correos?
- [ ] ¿Se ejecuta `vendor/bin/pest` o `vendor/bin/phpunit` sin arrojar advertencias ni fallos?
