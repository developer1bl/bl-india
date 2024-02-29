<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service = Service::All();

        return response()->json([
                                'success' => true,   
                                'data' => $service ?? []
                                ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'service_name' => ['required', 'string', 'max:150', Rule::unique('services', 'service_name')->whereNull('deleted_at')],
            'service_slug' => ['required' ,'string', 'max:255', Rule::unique('services', 'service_slug')->whereNull('deleted_at')],
            'service_image_id' => 'required|numeric',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $faq = [
            'question' => $request->question,
            'answer' => $request->answer
        ];

        $data = [
            'service_name' => $request->service_name,
            'service_slug' => $request->service_slug,
            'service_image_id' => $request->service_image_id,
            'service_img_alt' => $request->service_img_alt,
            'service_compliance' => $request->service_compliance,
            'faqs' => $faq,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
            'service_featured' => $request->service_featured,
            'service_product_show' => $request->service_product_show,
            'service_order' => $request->service_order,
            'service_status' => $request->service_status,
        ];
    
        $result = Service::create($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Service created successfully'
                                    ], 201);
        } else {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong'
                                    ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(),[
            'service_name' => ['required', 'string', 'max:150', Rule::unique('services', 'service_name')->ignore($id, 'service_id')],
            'service_slug' => ['required' ,'string', 'max:255', Rule::unique('services', 'service_slug')->ignore($id, 'service_id')],
            'service_image_id' => 'required|numeric',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $faq = [
            'question' => $request->question,
            'answer' => $request->answer
        ];

        $data = [
            'service_name' => $request->service_name,
            'service_slug' => $request->service_slug,
            'service_image_id' => $request->service_image_id,
            'service_img_alt' => $request->service_img_alt,
            'service_compliance' => $request->service_compliance,
            'faqs' => $faq,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
            'service_featured' => $request->service_featured,
            'service_product_show' => $request->service_product_show,
            'service_order' => $request->service_order,
            'service_status' => $request->service_status,
        ];
       
        $service = Service::find($id);

        if ($service) {

            $result = $service->update($data);

            if ($result) {
                
                return response()->json([
                                       'success' => true,
                                       'message' => 'Service updated successfully'
                                        ], 202);
            } else {
                
                return response()->json([
                                       'success' => false,
                                       'message' => 'Something went wrong'
                                        ], 500);
            }

        }else{

            return response()->json([
                                   'success' => false,
                                   'message' => 'Service not found'
                                    ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::find($id);

        if ($service) {

            $service->delete();

            return response()->json([
                                  'success' => true,
                                  'message' => 'Service deleted successfully'
                                    ], 202);
        } else {
            
            return response()->json([
                                  'success' => false,
                                  'message' => 'Service not found'
                                    ], 404);
        }
    }
}
