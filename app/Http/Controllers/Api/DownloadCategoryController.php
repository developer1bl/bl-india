<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DownloadCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\UserExistPreviouslyException;

class DownloadCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $donwloadCategory = DownloadCategory::get();

        return response()->json([
                                'data' => $donwloadCategory ?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'download_category' => ['required', 'string', Rule::unique('download_categories', 'download_category')->whereNull('deleted_at')],
            'download_category_slug' => ['required', 'string', Rule::unique('download_categories', 'download_category_slug')->whereNull('deleted_at')],
        ]);

         //if the request have some validation errors
         if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (DownloadCategory::withTrashed()->whereDownload_category($request->download_category)->exists()) {
            
            throw new UserExistPreviouslyException('this Download Category was deleted previously, did you want to restore it?');
        }

        $downloadCategory = DownloadCategory::create($request->all());

        if ($downloadCategory) {
            
            return response()->json([
                                    'success' => true,
                                    'message' => 'Download Category created successfully'
                                    ], 202);
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
        $downloadCategory = DownloadCategory::withTrashed()->wheredownload_category($request)->first();

        if ($downloadCategory) {
            
            $downloadCategory->restore();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Download Category restored successfully'
                                    ], 202);
        } else {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Download Category not found'
                                    ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $donwloadCategory = DownloadCategory::find($id);

        if ($donwloadCategory) {
            
            return response()->json([
                                    'data' => $donwloadCategory,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {
            
            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Download Category not found'
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
        $donwloadCategory = DownloadCategory::find($id);

        if (!$donwloadCategory) {
            
            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Download Category not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'download_category' => ['required', 'string', Rule::unique('download_categories', 'download_category')->ignore($id, 'download_category_id')],
            'download_category_slug' => ['required', 'string', Rule::unique('download_categories', 'download_category_slug')->ignore($id, 'download_category_id')],
        ]);

         //if the request have some validation errors
         if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $result = $donwloadCategory->update($request->all());

        if ($result) {
            
            return response()->json([
                                    'success' => true,
                                    'message' => 'Download Category updated successfully'
                                    ], 202);
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
        $donwloadCategory = DownloadCategory::find($id);

        if ($donwloadCategory) {
            
            $donwloadCategory->delete();

            return response()->json([
                                   'success' => true,
                                   'message' => 'Download Category deleted successfully'
                                    ], 202);
        } else {
            
            return response()->json([
                                   'success' => false,
                                   'message' => 'Download Category not found'
                                    ], 404);
        }
    }
}
