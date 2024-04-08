<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaticPage;

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

    public function getHomeSectionData($id){

        // $pageSection =
    }
}
