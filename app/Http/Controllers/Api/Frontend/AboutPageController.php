<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaticPage;
use App\Models\StaticPageSection;
use App\Models\Team;

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
                            ->Where('Page_name', 'about')
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
    public function getAboutSection(string $slug){

        $page = StaticPage::wherePage_name('about')
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

        $founderVoice = "Lorem Ipsum is simply dummy text of the printing and typesetting
                         industry. Lorem Ipsum has been the industry's standard dummy text
                         ever since the 1500s, when an unknown printer took a galley of type
                         and scrambled it to make a type specimen book.";

        $founderName = "Rajesh Kumar";

        $data = [
            'founder_voice' => $founderVoice,
            'founder_name' => $founderName,
        ];

        return response()->json([
                                'data' => $data ?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * about page client section
     *
     * @return \Illuminate\Http\Response
     */
    public function getClientData(){


    }
}
