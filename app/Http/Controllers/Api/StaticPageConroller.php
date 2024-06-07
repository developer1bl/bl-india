<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaticPage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;
use App\Helpers\MediaHelper;

class StaticPageConroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $staticPage = StaticPage::with('pageSection')
                                  ->orderByDesc('static_page_id')
                                  ->get();

        return response()->json([
                                 'data' => $staticPage ?? [],
                                 'success' => true
                                ], 200);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_name' => ['required','string','max:150'],
            'page_slug' => ['required','string','max:150', Rule::unique('static_pages', 'page_slug')->whereNull('deleted_at')],
            'tagline' => 'string|nullable',
            'page_image_id' => ['nullable','exists:media,media_id'],
            'page_image_alt' => 'nullable|string',
            'seo_title' => 'string|nullable',
            'seo_keywords' => 'string|nullable',
            'seo_description' => 'string|nullable',
            'page_status' => 'boolean|nullable',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (StaticPage::withTrashed()
                        ->where('page_slug', $request->page_slug)
                        ->first()) {

            throw new UserExistPreviouslyException('Oops! It appears that the chosen Page slug is already in use. Please select a different one and try again');
        }

        $PageImagePath = MediaHelper::getMediaPath($request->page_image_id ?? null);

        $data = [
            'page_name' => $request->input('page_name'),
            'page_slug' => $request->input('page_slug'),
            'tagline' => $request->input('tagline'),
            'page_img_url' => $PageImagePath,
            'page_image_alt' => $request->input('page_image_alt'),
            'seo_title' => $request->input('seo_title'),
            'seo_keywords' => $request->input('seo_keywords'),
            'seo_description' => $request->input('seo_description'),
            'page_status' => true,
        ];

        $result = StaticPage::create($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Page created successfully'
                                    ], 201);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function restore(string $request)
    {
        $staticPage = StaticPage::withTrashed()
                                  ->where('page_name', $request)
                                  ->orwhere('page_slug', $request)
                                  ->first();

        if ($staticPage) {

            $staticPage->restore();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Page restored successfully'
                                    ], 202);
        }else{

            return response()->json([
                                    'success' => false,
                                    'message' => 'static page not found'
                                    ], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return Response
     */
    public function show(string $id)
    {
        $staticPage = StaticPage::with('pageSection')->find($id);

        if ($staticPage) {

            return response()->json([
                                    'data' => $staticPage,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'static page not found'
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
     *
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, string $id)
    {
        $staticPage = StaticPage::find($id);

        if (!$staticPage) {

            return response()->json([
                                    'success' => false,
                                    'message' =>'static page not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'page_name' => ['required','string','max:150'],
            'page_slug' => ['required','string','max:150', Rule::unique('static_pages', 'page_slug')->ignore($id,'static_page_id')->whereNull('deleted_at')],
            'tagline' => 'string|nullable',
            'page_image_id' => ['nullable','integer','exists:media,media_id'],
            'page_image_alt' => 'nullable|string',
            'seo_title' => 'string|nullable',
            'seo_keywords' => 'string|nullable',
            'seo_description' => 'string|nullable',
            'page_status' => 'boolean|nullable',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $PageImagePath = MediaHelper::getMediaPath($request->page_image_id ?? null);

        $data = [
            'page_name' => $request->input('page_name'),
            'page_slug' => $request->input('page_slug'),
            'tagline' => $request->input('tagline'),
            'page_img_url' => $PageImagePath,
            'page_image_alt' => $request->input('page_image_alt'),
            'seo_title' => $request->input('seo_title'),
            'seo_keywords' => $request->input('seo_keywords'),
            'seo_description' => $request->input('seo_description'),
            'page_status' => $request->input('page_status'),
        ];

        $result = $staticPage->update($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Page updated successfully'
                                    ], 201);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $staticPage = StaticPage::find($id);

        if (!$staticPage) {

            return response()->json([
                                    'success' => false,
                                    'message' =>'static page not found'
                                    ], 404);
        }

        $result = $staticPage->delete();

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Page deleted successfully'
                                    ], 201);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }
}
