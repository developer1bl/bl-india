<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\UserExistPreviouslyException;
use Illuminate\Validation\Rule;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return response
     */
    public function index()
    {
        $blogCategory = BlogCategory::with('blogs')->get();

        return response()->json([
                                'data' => $blogCategory?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blog_category_name' => ['required', 'string', Rule::unique('blog_categories', 'blog_category_name')->whereNull('deleted_at')],
            'blog_category_slug' => ['required', 'string', Rule::unique('blog_categories', 'blog_category_slug')->whereNull('deleted_at')],
            'blog_category_description' => 'nullable|string',
            'seo_title' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'seo_other_details' => 'nullable|string',
            'blog_category_status' => 'nullable|boolean',
        ]);

         //if the request have some validation errors
         if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (BlogCategory::withTrashed()
                          ->where('blog_category_name', $request->blog_category_name)
                          ->OrWhere('blog_category_slug', $request->blog_category_slug)
                          ->exists()) 
        {    
            throw new UserExistPreviouslyException('Oops! It appears that the chosen Blog Category Name or slug is already in use. Please select a different one and try again');
        }

        $result = BlogCategory::create($request->all());

        if ($result) {
            
            return response()->json([
                                    'success' => true,
                                    'message' => 'Blog Category created successfully'
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
     * 
     * @param string $request
     * @return response
     */
    public function restore(string $request)
    {
        $blogCategory = BlogCategory::withTrashed()
                                      ->whereBlog_category_name($request)
                                      ->first();

        if ($blogCategory) {
            
            $blogCategory->restore();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Blog Category restored successfully'
                                    ], 202);
        } else {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Blog Category not found'
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
        $blogCategory = BlogCategory::find($id);

        if ($blogCategory) {
            
            return response()->json([
                                    'data' => $blogCategory,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {
            
            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Blog Category not found'
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
     * @param Request $resource
     * @return Response
     */
    public function update(Request $request, string $id)
    {
        $blogCategory = BlogCategory::find($id);

        if (!$blogCategory) {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Blog Category not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'blog_category_name' => ['required', 'string', Rule::unique('blog_categories', 'blog_category_name')->ignore($id, 'blog_category_id')],
            'blog_category_slug' => ['required', 'string', Rule::unique('blog_categories', 'blog_category_slug')->ignore($id, 'blog_category_id')],
            'blog_category_description' => 'nullable|string',
            'seo_title' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'seo_other_details' => 'nullable|string',
            'blog_category_status' => 'nullable|boolean',
        ]);

         //if the request have some validation errors
         if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }
       
        $result = $blogCategory->update($request->all());

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Blog Category updated successfully'
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
        $blogCategory = BlogCategory::find($id);

        if (!$blogCategory) {
            
            return response()->json([
                                   'success' => false,
                                   'message' => 'Blog Category not found'
                                    ], 404);
        }

        $result = $blogCategory->delete();

        if ($result) {

            return response()->json([
                                   'success' => true,
                                   'message' => 'Blog Category deleted successfully'
                                    ], 201);
        } else {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }
}
