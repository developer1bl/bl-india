<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaticPage;
use App\Models\StaticPageSection;
use App\Models\Team;
use App\Models\ClientUser;
use App\Models\FounderVoice;

class AboutPageController extends Controller
{
    /**
     * about page index
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        //check data for the About page
        $page = StaticPage::wherePage_status(1)
                            ->Where('page_slug', 'about')
                            ->first();

        return response()->json([
                                'data' => $page ?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * about page section
     *
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function getAboutSectionData(string $slug){

        $pageSectionData = StaticPageSection::Select('static_page_sections.*')
                                              ->leftJoin('static_pages', function ($q){
                                                $q->on('static_pages.static_page_id','static_page_sections.static_page_id');
                                              })
                                              ->where('static_page_sections.section_slug', $slug)
                                              ->first();

        return response()->json([
                                'data' => $pageSectionData ?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * about page team section
     *
     * @return \Illuminate\Http\Response
     */
    public function getAboutTeamData(){

        $team  = Team::orderByDesc('id')->get();

        return response()->json([
                                'data' => $team ?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * about page Founder Voice section
     *
     * @return \Illuminate\Http\Response
     */
    public function getFounderVoiceData(){

        $foundVoice = FounderVoice::all();

        return response()->json([
                                'data' => $foundVoice ?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * about page client section
     *
     * @return \Illuminate\Http\Response
     */
    public function getAboutClientData(){

        $clientUser = ClientUser::all();

        return response()->json([
                                'data' => $clientUser ?? [],
                                'success' => true
                                ], 200);
    }
}
