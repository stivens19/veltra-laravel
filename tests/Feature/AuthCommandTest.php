<?php

namespace Veltra\VeltraLaravel\Tests\Feature;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Veltra\VeltraLaravel\Tests\TestCase;

class AuthCommandTest extends TestCase
{
    #[Test]
    public function it_can_publish_auth_views(): void
    {
        $loginView = resource_path('views/auth/login.blade.php');
        $registerView = resource_path('views/auth/register.blade.php');
        $guestLayout = resource_path('views/layouts/guest.blade.php');

        $this->artisan('veltra:auth', ['--force' => true])
            ->assertSuccessful();

        $this->assertTrue(File::exists($loginView));
        $this->assertTrue(File::exists($registerView));
        $this->assertTrue(File::exists($guestLayout));

        $loginContent = File::get($loginView);
        $this->assertStringContainsString('route(\'login\')', $loginContent);
        $this->assertStringContainsString('x-veltra::layout.guest', $loginContent);

        // Limpieza de archivos de prueba
        File::deleteDirectory(resource_path('views/auth'));
        File::deleteDirectory(resource_path('views/layouts'));
    }
}

