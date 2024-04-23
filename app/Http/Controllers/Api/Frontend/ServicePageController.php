<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\ServiceSection;
use Illuminate\Http\Request;

class ServicePageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param string $service
     * @return Response
     */
    public function getService(string $service = null){

       $service = ServiceCategory::with('services')
                                   ->find($service);
        return response()->json([
                                'success' => true,
                                'data' => $service
                                ],200);
    }

    /**
     * Display a listing of the resource.
     *
     * @param string $service
     * @return Response
     */
    public function getServiceIntroData(string $service = null, string $name = 'Introduction'){

        $sectionData = ServiceSection::select('service_sections.*')
                                        ->join('services', function($query) use ($service){
                                            $query->on('services.service_id', '=', 'service_sections.service_section_id')
                                                  ->where('services.service_id', '=', $service)
                                                  ->where('services.service_status', true);
                                        })
                                       ->where('service_sections.service_section_name', $name)
                                       ->where('service_sections.service_section_status', true)
                                       ->first();

        return response()->json([
                                'success' => true,
                                'data' => $sectionData ?? []
                                ],200);
    }

    /**
     * Display a listing of the resource.
     *
     * @param string $service
     * @return Response
     */
    public function viewMandatoryProductList(){
        
    }
}
