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
    /**
     * home page index
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * home page section
     * 
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function getHomeSectionData(string $slug){

        $page = StaticPage::wherePage_name('home')
                            ->wherePage_status(1)
                            ->first();

        $pageSection = StaticPageSection::where('section_slug', $slug)
                                          ->where('section_status', 1)
                                          ->where('static_page_id', $page->static_page_id)
                                          ->first();

        return response()->json([
                                'data' => $pageSection ?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * home page service section
     *
     * @return \Illuminate\Http\Response
     */
    public function getHomeServiceData(){

        $homeService = Service::LatestService();

        return response()->json([
                                'data' => $homeService ?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * home page blog section
     *
     * @return \Illuminate\Http\Response
     */
    public function getHomeBlogData(){

        $homeBlogs = Blog::LatestBlogs();

        return response()->json([
                                'data' => $homeBlogs ?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * home page work-flow section
     *
     * @return \Illuminate\Http\Response
     */
    public function getHomeWorkFlowData(){

        $workFlow = WorkFlow::orderByDesc('id')->take(5)->get();

        return response()->json([
                                'data' => $workFlow ?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * home page testimonial section
     *
     * @return \Illuminate\Http\Response
     */
    public function getHomeTestimonialsData(){

        $testimonials = Testimonial::orderByDesc('testimonial_id')->take(3)->get();

        return response()->json([
                                'data' => $testimonials ?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * home page associate section
     *
     * @return \Illuminate\Http\Response
     */
    public function getHomeAssociateData(){

        $associate = Associate::orderByDesc('associate_id')->get();

        return response()->json([
                                'data' => $associate ?? [],
                                'success' => true
                                ], 200);
    }
}
