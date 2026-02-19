<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    private const TTL = 3600;
    private const PREFIX = 'user_permissions_';

    public function getUserPermissions(User $user): array
    {
        return Cache::remember(
            self::PREFIX . $user->id,
            self::TTL,
            fn() => $user->role?->permissions()
                        ->pluck('name')
                        ->toArray() ?? []
        );
    }

    public function hasPermission(User $user, string $permission): bool
    {
        return in_array($permission, $this->getUserPermissions($user));
    }

    public function invalidateCache(int $userId): void
    {
        Cache::forget(self::PREFIX . $userId);
    }

    public function invalidateRoleCache(int $roleId): void
    {
        User::where('role_id', $roleId)
            ->pluck('id')
            ->each(fn($id) => Cache::forget(self::PREFIX . $id));
    }
}