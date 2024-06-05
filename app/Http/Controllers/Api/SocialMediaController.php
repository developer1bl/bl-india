<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\MediaHelper;

class SocialMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $socialMedia = SocialMedia::all();

        return response()->json(['data' => $socialMedia ?? []], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'social_media_name' => ['required', 'string'],
            'social_link_url' => ['required', 'url'],
            'social_media_id' => ['required', 'exists:media,media_id'],
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 403);
        }

        $socialIMageUrl = MediaHelper::getMediaPath($request->social_media_id ?? null);
 
        $socialMedia = SocialMedia::create([
            'social_media_name' => $request->social_media_name,
            'social_link_url' => $request->social_link_url,
            'social_icon_url' => $socialIMageUrl,
            'social_link_status' => true
        ]);

        return response()->json(['success' => true, 'message' => 'Social media entry created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $socialMedia = SocialMedia::find($id);

        if (!$socialMedia) {
            return response()->json(['error' => 'Social media entry not found'], 404);
        }

        return response()->json(['data' => $socialMedia], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $socialMedia = SocialMedia::find($id);

        if (!$socialMedia) {
            return response()->json(['error' => 'Social media entry not found'], 404);
        }

        $validate = Validator::make($request->all(), [
            'social_media_name' => ['required', 'string'],
            'social_link_url' => ['required', 'url'],
            'social_media_id' => ['required', 'exists:media,media_id'],
            'social_link_status' => ['required', 'boolean']
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 403);
        }

        $socialIMageUrl = MediaHelper::getMediaPath($request->social_media_id ?? null);

        $data = [
            'social_media_name' => $request->social_media_name,
            'social_link_url' => $request->social_link_url,
            'social_icon_url' => $socialIMageUrl,
            'social_link_status' =>  $request->social_link_status
        ];

        $socialMedia->update($data);

        return response()->json(['success' => true, 'message' => 'Social media entry updated successfully'], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $socialMedia = SocialMedia::find($id);

        if (!$socialMedia) {
            return response()->json(['error' => 'Social media entry not found'], 404);
        }

        $socialMedia->delete();

        return response()->json(['success' => true, 'message' => 'Social media entry deleted successfully'], 200);
    }
}
