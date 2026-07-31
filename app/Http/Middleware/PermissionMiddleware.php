<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $permissions = explode('|', $permission);
        $authorized = false;

        foreach ($permissions as $p) {
            if ($request->user() && $request->user()->hasPermission(trim($p))) {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
