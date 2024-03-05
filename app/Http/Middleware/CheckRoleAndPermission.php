<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRoleAndPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role, $permission)
    {
        $user = Auth::user();
    
        if (!$user || !$user->hasRole($role) || !$user->hasPermission($permission)) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        return $next($request);
    }
}
