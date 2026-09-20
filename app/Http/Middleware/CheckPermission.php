<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $module, string $action): Response
    {
        $allowedActions = $request->user()?->effectivePermissions()[$module] ?? [];

        if (!in_array($action, $allowedActions, true)) {
            return response()->json([
                'message' => 'No tienes permiso para realizar esta acción',
            ], 403);
        }

        return $next($request);
    }
}
