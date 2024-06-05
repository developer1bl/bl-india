<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;

class BlogPageController extends Controller
{
    /**
     * Display a blogs of the resource.
     *
     * @return response
     */
    public function getBlogs(){

        $blog = Blog::all();

        return response()->json([
                                'data' => $blog ?? [],
                                'success' => true
                                ],200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return response
     */
    public function getBlogsCategory()
    {
        $blogCategory = BlogCategory::with('blogs')->get();

        return response()->json([
                                'data' => $blogCategory?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * home page single blog section
     *
     * @param string $blog
     * @return \Illuminate\Http\Response
     */
    public function getSingleBlogData(string $blog){

        $blog = Blog::find($blog);

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
                                    'message' => 'Blog not found'
                                    ], 404);
            }
    }

    /**
     * home page blog section
     *
     * @return \Illuminate\Http\Response
     */
    public function getLatestBlogData(){

        $homeBlogs = Blog::LatestBlogs();

        return response()->json([
                                'data' => $homeBlogs ?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * home page blog section
     *
     * @param string $slugs
     * @return \Illuminate\Http\Response
     */
    public function getCategoryWiseBlogs(string $slug){

        $blogs = Blog::Select('blogs.*','b.blog_category_slug')
                       ->join('blog_categories as b',
                                function($query){
                                    $query->on('b.blog_category_id', '=', 'blogs.blog_id');
                        })
                       ->where('b.blog_category_slug', $slug)
                       ->orderByDesc('blogs.blog_id')
                       ->get();

        return response()->json([
                                'data' => $blogs ?? [],
                                'success' => true,
                                ], 200);
    }
}
