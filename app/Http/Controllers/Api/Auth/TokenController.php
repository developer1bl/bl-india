<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class TokenController extends Controller
{
    /**
     * if the token in valid then return true.
     *
     * @return void
     */
    public function checkTokenValidity(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized access.'], 401);
        }

        return response()->json(['message' => 'Token is valid.'], 200);
    }
}
