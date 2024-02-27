<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->isUser() === true) {
            return $next($request);
        }
        
        return response()->json([
                                'success' => false,
                                'message' => 'You are not authorized to access this resource',
                                ], Response::HTTP_FORBIDDEN);
    }
}
