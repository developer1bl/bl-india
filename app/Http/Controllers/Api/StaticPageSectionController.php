<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaticPageSection;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\UserExistPreviouslyException;

class StaticPageSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return response
     */
    public function index()
    {
        $staticPageSection = StaticPageSection::with(['staticPage', 'Image'])
                                                ->orderByDesc('static_page_section_id')
                                                ->get();

        return response()->json([
                                'data' => $staticPageSection ?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'static_page_id' => 'required|integer|exists:static_pages,static_page_id',
            'section_media_id' => 'integer|exists:media,media_id',
            'section_img_alt' => 'nullable|string',
            'section_name' => ['required','string','max:150', Rule::unique('static_page_sections', 'section_name')->whereNull('deleted_at')],
            'section_tagline' => 'nullable|string',
            'section_description' => 'nullable|string',
            'section_content' => 'nullable|string',
            'section_status' => 'boolean',
            'section_order' => 'integer',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (StaticPageSection::withTrashed()
                               ->where('section_name', $request->section_name)
                               ->first()) {

            throw new UserExistPreviouslyException('Oops! It appears that the chosen Section name is already in use. Please select a different one and try again');
        }

        $staticPageSection = StaticPageSection::create($request->all());

        if ($staticPageSection) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Static Page Section Created Successfully'
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong'
                                    ], 403);
        }

    }

    /**
     * Store a newly restore resource in storage.
     *
     * @param string $request
     * @return Response
     */
    public function restore(string $request)
    {
        $staticPageSection = StaticPageSection::withTrashed()
                                               ->where('section_name', $request)
                                               ->first();

        if ($staticPageSection) {

            $staticPageSection->restore();
            return response()->json([
                                    'success' => true,
                                    'message' => 'Static Page Section Restored Successfully'
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Static Page Section not found'
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
        $staticPageSection = StaticPageSection::with(['staticPage', 'Image'])->find($id);

        if ($staticPageSection) {

            return response()->json([
                                    'data' => $staticPageSection ?? [],
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Static Page Section not found'
                                    ], 403);
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
     * @param Request $request
     *  @param string $id
     * @return Response
     */
    public function update(Request $request, string $id)
    {
        // Check if the record being updated exists
        $staticPageSection = StaticPageSection::find($id);

        if (!$staticPageSection) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Static Page Section Not Found'
                                    ], 403);
        }

        $validator = Validator::make($request->all(), [
            'static_page_id' => 'required|integer|exists:static_pages,static_page_id',
            'section_media_id' => 'integer|exists:media,media_id',
            'section_img_alt' => 'nullable|string',
            'section_name' => [ 'required','string','max:150',
                                Rule::unique('static_page_sections', 'section_name')
                                    ->whereNull('deleted_at')
                                    ->ignore($id, 'static_page_section_id'),
            ],
            'section_tagline' => 'nullable|string',
            'section_description' => 'nullable|string',
            'section_content' => 'nullable|string',
            'section_status' => 'boolean',
            'section_order' => 'integer',
        ]);

        // If the request has validation errors
        if ($validator->fails()) {
            
            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        // Update the StaticPageSection
        $staticPageSection->update($request->all());

        return response()->json([
                                'success' => true,
                                'message' => 'Static Page Section Updated Successfully'
                                ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return Response
     */
    public function destroy(string $id)
    {
        // Check if the record being deleted exists
        $staticPageSection = StaticPageSection::find($id);

        if (!$staticPageSection) {

            return response()->json([
                                       'success' => false,
                                       'message' => 'Static Page Section Not Found'
                                        ], 403);
        }

        $staticPageSection->delete();

        return response()->json([
                                'success' => true,
                                'message' => 'Static Page Section Deleted Successfully'
                                ], 200);
    }
}
