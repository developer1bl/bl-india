<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\StaticPage;
use App\Models\StaticPageSection;
use App\Models\WorkFlow;
use App\Models\Testimonial;
use App\Models\Associate;

class HomeController extends Controller
{
    public function index(){

        //check data for the home page
        $page = StaticPage::wherePage_status(1)
                            ->Where('Page_name', 'home')
                            ->first();

        return response()->json([
                                'data' => $page ?? [],
                                'success' => true,
                                ], 200);
    }

    public function getHomeSectionData($id ,string $slug){

        $pageSection = StaticPageSection::where('section_slug', $slug)
                                          ->where('section_status', 1)
                                          ->where('static_page_id', $id)
                                          ->first();

        return response()->json([
                                'data' => $pageSection ?? [],
                                'success' => true,
                                ], 200);
    }

    public function getHomeServiceData(){

        $homeService = Service::LatestService();

        return response()->json([
                                'data' => $homeService ?? [],
                                'success' => true,
                                ], 200);
    }

    public function getHomeBlogData(){

        $homeBlogs = Blog::LatestBlogs();

        return response()->json([
                                'data' => $homeBlogs ?? [],
                                'success' => true,
                                ], 200);
    }

    public function getHomeWorkFlowData(){

        $workFlow = WorkFlow::orderByDesc('id')->take(5)->get();

        return response()->json([
                                'data' => $workFlow ?? [],
                                'success' => true
                                ], 200);
    }

    public function getHomeTestimonialsData(){

        $testimonials = Testimonial::orderByDesc('testimonial_id')->take(3)->get();

        return response()->json([
                                'data' => $testimonials ?? [],
                                'success' => true
                                ], 200);
    }

    public function getHomeAssociateData(){

        $associate = Associate::orderByDesc('associate_id')->get();

        return response()->json([
                                'data' => $associate ?? [],
                                'success' => true
                                ], 200);
    }
}
