<?php

namespace Veltra\VeltraLaravel\Tests\Feature;

use Veltra\VeltraLaravel\Support\Navigation;
use Veltra\VeltraLaravel\Tests\TestCase;

class NavigationTest extends TestCase
{
    public function test_it_resolves_profile_url_dynamically(): void
    {
        $this->app['router']->get('settings/profile', fn () => 'profile')->name('profile.edit');
        $this->app['router']->getRoutes()->refreshNameLookups();

        $url = Navigation::getProfileUrl();
        $this->assertEquals(route('profile.edit'), $url);

        $resolved = Navigation::resolveUrl([
            'title' => 'Perfil & 2FA',
            'url' => '/profile',
            'route' => 'profile.edit',
        ]);
        $this->assertEquals(route('profile.edit'), $resolved);
    }

    public function test_it_identifies_active_profile_and_settings_paths(): void
    {
        $this->get('/settings/profile');

        $this->assertTrue(Navigation::isActive('/settings/profile'));
        $this->assertTrue(Navigation::isActive('/profile'));
    }

    public function test_it_filters_navigation_by_authorization(): void
    {
        $items = [
            ['title' => 'Dashboard', 'url' => '/dashboard'],
            ['title' => 'Admin Only', 'url' => '/admin', 'role' => 'admin'],
            ['title' => 'Reports', 'url' => '/reports', 'permission' => 'view-reports'],
        ];

        // Guest user: only unrestricted items
        $filteredGuest = Navigation::filterAuthorized($items);
        $this->assertCount(1, $filteredGuest);
        $this->assertEquals('Dashboard', $filteredGuest[0]['title']);

        // Authenticated user with role
        $user = new class extends \Illuminate\Foundation\Auth\User {
            protected $guarded = [];
            public function can($ability, $arguments = []): bool {
                return $ability === 'view-reports';
            }
        };
        $user->name = 'Super Admin';
        $user->role = 'admin';

        $this->actingAs($user);

        $filteredAdmin = Navigation::filterAuthorized($items);
        $this->assertCount(3, $filteredAdmin);
    }

    public function test_it_resolves_user_role_name(): void
    {
        $this->assertEquals('Invitado', Navigation::getUserRoleName());

        $user = new class extends \Illuminate\Foundation\Auth\User {
            protected $guarded = [];
        };
        $user->name = 'Jane Doe';
        $user->role = 'manager';

        $this->actingAs($user);
        $this->assertEquals('Manager', Navigation::getUserRoleName());
    }
}
