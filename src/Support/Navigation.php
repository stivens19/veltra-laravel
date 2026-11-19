<?php

namespace Veltra\VeltraLaravel\Support;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class Navigation
{
    /**
     * Get the dynamic profile or settings URL according to registered routes.
     */
    public static function getProfileUrl(): string
    {
        if (Route::has('profile.edit')) {
            return route('profile.edit');
        }

        if (Route::has('profile')) {
            return route('profile');
        }

        if (Route::has('profile.show')) {
            return route('profile.show');
        }

        if (Route::has('settings.profile')) {
            return route('settings.profile');
        }

        if (Route::has('settings')) {
            return url('settings');
        }

        return url('/profile');
    }

    /**
     * Resolve the target URL for a navigation item.
     *
     * @param array<string, mixed> $item
     */
    public static function resolveUrl(array $item): string
    {
        if (isset($item['route']) && Route::has($item['route'])) {
            return route($item['route']);
        }

        $url = $item['url'] ?? '#';

        if ($url === '/profile' || $url === 'profile' || ($item['route'] ?? '') === 'profile.edit') {
            return self::getProfileUrl();
        }

        return $url;
    }

    /**
     * Determine if a given URL or route is currently active.
     */
    public static function isActive(string $url): bool
    {
        if (empty($url) || $url === '#') {
            return false;
        }

        $currentPath = '/' . trim(Request::path(), '/');
        $targetPath = '/' . trim(parse_url($url, PHP_URL_PATH) ?? '', '/');

        if ($currentPath === $targetPath) {
            return true;
        }

        // Profile & Settings family matching
        if (in_array($targetPath, ['/profile', '/settings/profile', '/settings/security', '/settings/appearance', '/settings'])) {
            return str_starts_with($currentPath, '/settings') || str_starts_with($currentPath, '/profile');
        }

        if ($targetPath !== '/' && $targetPath !== '/dashboard' && str_starts_with($currentPath, $targetPath . '/')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if any child item in a group is active.
     *
     * @param array<int, array<string, mixed>> $children
     */
    public static function hasActiveChild(array $children): bool
    {
        foreach ($children as $child) {
            $url = self::resolveUrl($child);
            if (self::isActive($url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the current authenticated user is authorized to see the navigation item.
     * Supports native Laravel Gates ('can', 'permission'), and role checks ('role', 'roles')
     * compatible with Spatie Permission or custom User model role attributes.
     *
     * @param array<string, mixed> $item
     */
    public static function isAuthorized(array $item): bool
    {
        $hasConstraint = isset($item['permission']) || isset($item['can']) || isset($item['role']) || isset($item['roles']);
        if (! $hasConstraint) {
            return true;
        }

        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // 1. Check 'can' or 'permission' (Native Laravel Gates / Policies / Spatie)
        $permission = $item['can'] ?? $item['permission'] ?? null;
        if ($permission) {
            if (is_array($permission)) {
                $hasAny = false;
                foreach ($permission as $perm) {
                    if ($user->can($perm)) {
                        $hasAny = true;
                        break;
                    }
                }
                if (! $hasAny) {
                    return false;
                }
            } elseif (! $user->can($permission)) {
                return false;
            }
        }

        // 2. Check 'role' or 'roles' (Spatie hasRole / hasAnyRole, or custom $user->role property)
        $roles = $item['roles'] ?? (isset($item['role']) ? (array) $item['role'] : null);
        if ($roles) {
            if (method_exists($user, 'hasAnyRole')) {
                if (! $user->hasAnyRole($roles)) {
                    return false;
                }
            } elseif (method_exists($user, 'hasRole')) {
                $matched = false;
                foreach ($roles as $r) {
                    if ($user->hasRole($r)) {
                        $matched = true;
                        break;
                    }
                }
                if (! $matched) {
                    return false;
                }
            } elseif (isset($user->role)) {
                if (! in_array($user->role, $roles, true)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Filter a list of navigation items according to authorization rules.
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    public static function filterAuthorized(array $items): array
    {
        $filtered = [];

        foreach ($items as $item) {
            if (! self::isAuthorized($item)) {
                continue;
            }

            if (isset($item['children']) && is_array($item['children'])) {
                $authorizedChildren = self::filterAuthorized($item['children']);
                if (empty($authorizedChildren)) {
                    continue;
                }
                $item['children'] = $authorizedChildren;
            }

            $filtered[] = $item;
        }

        return $filtered;
    }

    /**
     * Get the display name for the current authenticated user's role.
     */
    public static function getUserRoleName(): string
    {
        $user = auth()->user();
        if (! $user) {
            return 'Invitado';
        }

        if (method_exists($user, 'getRoleNames')) {
            $roles = $user->getRoleNames();
            if ($roles && method_exists($roles, 'isNotEmpty') && $roles->isNotEmpty()) {
                return ucfirst((string) $roles->first());
            }
        }

        if (isset($user->role) && is_string($user->role) && ! empty($user->role)) {
            return ucfirst($user->role);
        }

        if (isset($user->is_admin) && $user->is_admin) {
            return 'Administrador';
        }

        return 'Usuario';
    }

    /**
     * Get the formatted and authorized navigation tree for serialization (Inertia/API).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getTree(): array
    {
        $rawItems = config('veltra.navigation', []);
        if (empty($rawItems)) {
            return [];
        }

        $tree = [];
        foreach ($rawItems as $item) {
            if (isset($item['header'])) {
                $tree[] = [
                    'header' => (string) $item['header'],
                ];
                continue;
            }

            if (! self::isAuthorized($item)) {
                continue;
            }

            $formatted = self::formatTreeItem($item);
            if ($formatted !== null) {
                $tree[] = $formatted;
            }
        }

        return $tree;
    }

    /**
     * Format a single navigation item for Inertia/frontend consumers.
     *
     * @param array<string, mixed> $item
     * @return array<string, mixed>|null
     */
    protected static function formatTreeItem(array $item): ?array
    {
        $hasChildren = isset($item['children']) && is_array($item['children']) && ! empty($item['children']);
        $children = [];
        $hasActiveChild = false;

        if ($hasChildren) {
            foreach ($item['children'] as $child) {
                if (! self::isAuthorized($child)) {
                    continue;
                }
                $formattedChild = self::formatTreeItem($child);
                if ($formattedChild !== null) {
                    if (! empty($formattedChild['active'])) {
                        $hasActiveChild = true;
                    }
                    $children[] = $formattedChild;
                }
            }

            // If an item had children but none are authorized, hide the parent
            if (empty($children)) {
                return null;
            }
        }

        $url = self::resolveUrl($item);
        $active = self::isActive($url) || $hasActiveChild;

        return [
            'title' => (string) ($item['title'] ?? ''),
            'icon' => (string) ($item['icon'] ?? 'circle'),
            'url' => $url,
            'active' => $active,
            'badge' => $item['badge'] ?? null,
            'badge_variant' => $item['badge_variant'] ?? 'cyan',
            'children' => $children,
            'has_active_child' => $hasActiveChild,
        ];
    }
}
