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
                            ->Where('page_slug', 'home')
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

        $pageSection = StaticPage::Select('static_page_sections.*')
                                   ->where('page_slug', 'home')
                                   ->where('page_status', 1)
                                   ->leftJoin('static_page_sections', 'static_page_sections.static_page_id', '=', 'static_pages.static_page_id')
                                   ->where('static_page_sections.section_slug', $slug)
                                   ->where('static_page_sections.section_status', 1)
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

        $homeService = Service::select('service_id','service_name', 'service_slug', 'service_img_url', 'service_img_alt', 'service_description')
                                ->limit(4)->get();

        return response()->json([
                                'data' => $homeService ?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * home page service with id
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function getHomeSingleServiceData(string $id){

        $service = Service::find($id);

        if ($service) {

            return response()->json([
                                    'data' => $service,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Service not found'
                                    ], 404);
        }
    }

     /**
     * home page all service
     *
     * @return \Illuminate\Http\Response
     */
    public function getHomeAllServiceData(){

        $service = Service::orderByDesc('service_id')
                            ->get();

        return response()->json([
                                'success' => true,
                                'data' => $service ?? []
                                ],200);
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

        $workFlow = WorkFlow::orderBy('id')->get();

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

        $testimonials = Testimonial::orderByDesc('testimonial_id')->get();

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
