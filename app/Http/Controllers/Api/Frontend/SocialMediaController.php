<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SocialMedia;

class SocialMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getSocialMedia()
    {
        $socialMedia = SocialMedia::all();

        return response()->json(['data' => $socialMedia ?? []], 200);
    }

}
