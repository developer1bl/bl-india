<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;
use App\Helpers\MediaHelper;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blog = Blog::with('blogCategory')->get();

        return response()->json([
                                'data' => $blog ?? [],
                                'success' => true
                                ],200);
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
            'blog_title' => ['required', 'string', Rule::unique('blogs', 'blog_title')->whereNull('deleted_at')],
            'blog_slug' => ['required', 'string', Rule::unique('blogs', 'blog_slug')->whereNull('deleted_at')],
            'blog_category_id' => 'integer|exists:blog_categories,blog_category_id',
            'blog_image_id' => 'integer|exists:media,media_id',
            'blog_img_alt' => 'nullable|string',
            'blog_content' => 'nullable|string',
            'seo_title' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'blog_status' => 'nullable|boolean',
            'seo_other_details' => 'nullable|string'
        ]);

         //if the request have some validation errors
         if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (Blog::withTrashed()
                  ->where('blog_title', $request->blog_title)
                  ->orWhere('blog_slug', $request->blog_slug)
                  ->exists())
        {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen Blog title or slug is already in use. Please select a different one and try again.');
        }

        $blogImagePath = MediaHelper::getMediaPath($request->blog_image_id ?? null);

        $data = [
            'blog_title' => $request->blog_title,
            'blog_slug' => $request->blog_slug,
            'blog_category_id' => $request->blog_category_id,
            'blog_img_url' => $blogImagePath,
            'blog_img_alt' => $request->blog_img_alt,
            'blog_content' => $request->blog_content,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
            'seo_other_details' => $request->seo_other_details,
            'blog_tags' => $request->blog_tags,
        ];

        $result = Blog::create($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Blog created successfully',
                                    ],200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later',
                                    ],422);
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
        $blog = Blog::withTrashed()->whereblog_title($request)->first();

        if (!$blog) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Blog not found'
                                    ], 404);
        }

        $result = $blog->restore();

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Blog restored successfully'
                                    ], 201);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
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
        $blog = Blog::find($id);

        if ($blog) {

            return response()->json([
                                    'data' => $blog,
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
     * @param request $request
     * @return Response
     */
    public function update(Request $request, string $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Blog Category not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'blog_title' => ['required', 'string', Rule::unique('blogs', 'blog_title')->ignore($id, 'blog_id')],
            'blog_slug' => ['required', 'string', Rule::unique('blogs', 'blog_slug')->ignore($id, 'blog_id')],
            'blog_category_id' => 'integer|exists:blog_categories,blog_category_id',
            'blog_image_id' => 'integer|exists:media,media_id',
            'blog_img_alt' => 'nullable|string',
            'blog_content' => 'nullable|string',
            'seo_title' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'blog_status' => 'nullable|boolean',
            'seo_other_details' => 'nullable|string'
        ]);

         //if the request have some validation errors
         if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $data = [
            'blog_title' => $request->blog_title,
            'blog_slug' => $request->blog_slug,
            'blog_category_id' => $request->blog_category_id,
            'blog_image_id' => $request->blog_image_id,
            'blog_img_alt' => $request->blog_img_alt,
            'blog_content' => $request->blog_content,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
            'seo_other_details' => $request->seo_other_details,
            'blog_tags' => $request->blog_tags,
        ];

        $result = $blog->update($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Blog updated successfully',
                                    ],200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later',
                                    ],422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return response
     */
    public function destroy(string $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Blog Category not found'
                                    ], 404);
        }

        $result = $blog->delete();

        if ($result) {

            return response()->json([
                                   'success' => true,
                                   'message' => 'Blog deleted successfully',
                                    ],200);
        } else {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Something went wrong, please try again later',
                                    ],422);
        }
    }
}
