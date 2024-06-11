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
     * @return Response
     */
    public function getAllServiceCategories()
    {

        $serviceCategory = ServiceCategory::with('services')->get();

        return response()->json([
                                'success' => true,
                                'data' => $serviceCategory ?? []
                                ], 200);
    }

    /**
     * Display a specific service category.
     *
     * @param string $service
     * @return Response
     */
    public function getSingleServiceCategory(string $service = null)
    {

        $service = ServiceCategory::with('services')
                                    ->find($service);

        return response()->json([
                                'success' => true,
                                'data' => $service
                                ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getAllServices()
    {
        $services = Service::all();

        return response()->json([
                                'success' => true,
                                'data' => $services?? []
                                ], 200);
    }


    /**
     * Display a specific service.
     *
     * @param string $service
     * @return Response
     **/
    public function getSingleService(string $service = null){

        $service = Service::with('service_category')->find($service);

        if ($service) {

            return response()->json([
                                    'success' => true,
                                    'data' => $service,
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
     * Display a listing of the resource.
     *
     * @param string $service
     * @param string $name
     * @return Response
     */
    public function getServiceSectionDetails(string $service = null, string $name = null)
    {
        // Convert name to lowercase for case-insensitive comparison
        $lowercaseName = strtolower($name);

        // Keywords to check in the name parameter
        $keywords = ['mandatory-product-list', 'product list', 'list'];
        $is_productList = false;

        // Check if the lowercase name parameter contains any of the keywords
        foreach ($keywords as $keyword) {
            if (stripos($lowercaseName, $keyword) !== false) {
                $is_productList = true;
            }
        }

        if($is_productList === true){

            // Call getMandatoryProductList if a keyword is found
            $sectionData = $this->getMandatoryProductList($service);

        }else{
            // Proceed with the original logic if no keyword is found
            $sectionData = ServiceSection::select('service_sections.*')
                                            ->join('services', 'services.service_id', '=', 'service_sections.service_id')
                                            ->where('services.service_id', '=', $service)
                                            ->where('services.service_status', 1)
                                            ->where('service_sections.service_section_slug', $name) // Assuming $slug is the correct variable for the slug
                                            ->first();

        }

        return response()->json([
                                'success' => true,
                                'data' => $sectionData ?? []
                                ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @param string $service
     * @return Response
     **/
    public function getMandatoryProductList($service)
    {
        // Assuming you have some logic here to fetch the mandatory product list
        $service = Service::find($service);
        $mandatoryProductList = $service->products()->get();

        return $mandatoryProductList ?? [];
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     **/
    public function getAllServiceSectionList($service)
    {
        $serviceSection = ServiceSection::select('service_sections.*')
                                        ->leftJoin('services', function ($query) use ($service){
                                            $query->on('services.service_id', '=', 'service_sections.service_section_id')
                                              ->where('services.service_id', $service);
                                        })->get();


        return response()->json([
                                'success' => true,
                                'data' => $serviceSection?? []
                                ], 200);
    }

}
