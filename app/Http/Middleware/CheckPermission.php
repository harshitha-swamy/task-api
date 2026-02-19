<?php
namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function __construct(private PermissionService $permissionService) {}

    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!$this->permissionService->hasPermission($request->user(), $permission)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}