<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MediaHelper;
use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gallery = Gallery::all();

        return response()->json(['data' => $gallery], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gallery_image_title' => ['string', 'required'],
            'gallery_image_slug' => ['string', 'required', Rule::unique('galleries', 'gallery_image_slug')->whereNull('deleted_at')],
            'media_id' => 'integer|exists:media,media_id',
            'img_alt' => 'string',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $media_url  = MediaHelper::getMediaPath($request->media_id ?? null);

        $data = [
            'gallery_image_title' => $request->gallery_image_title,
            'gallery_image_slug' => $request->gallery_image_slug,
            'media_Url' => $media_url,
            'img_alt' => $request->img_alt,
        ];

        $gallery = Gallery::create($data);

        return response()->json(['message' => 'Image uploaded in gallery successfully '], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gallery = Gallery::find($id);

        if ($gallery) {

            return response()->json([
                                    'data' => $gallery,
                                   'message' => '',
                                   'success' => true
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                   'message' => 'Gallery not found',
                                   'success' => false
                                    ], 404);
        }
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
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'gallery_image_title' => ['string', 'required'],
            'gallery_image_slug' => ['string', 'required', Rule::unique('galleries', 'gallery_image_slug')
                                                                 ->ignore($id, 'gallery_id')
                                                                 ->whereNull('deleted_at')],
            'media_id' => 'integer|exists:media,media_id',
            'img_alt' => 'string',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $media_url  = MediaHelper::getMediaPath($request->media_id ?? null);

        $data = [
            'gallery_image_title' => $request->gallery_image_title,
            'gallery_image_slug' => $request->gallery_image_slug,
            'media_Url' => $media_url,
            'img_alt' => $request->img_alt,
        ];

        $gallery = $gallery->update($data);

        return response()->json(['message' => 'Gallery updated successfully '], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        $gallery->delete();

        return response()->json(['message' => 'Gallery deleted successfully '], 201);
    }
}
