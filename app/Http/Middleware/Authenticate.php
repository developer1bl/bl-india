<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request)
    {   //set request headers
        $request->headers->set('Accept', 'application/json');

        if ($request->expectsJson()) {
            return response()->json(['message' => 'unauthenticated'],401);
        }
        return null;
    }
}