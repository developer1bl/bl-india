<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceSection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;

class ServiceSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return Response
     */
    public function index()
    {
        $serviceSection = ServiceSection::With('service')
                                          ->OrderByDesc('service_section_id')
                                          ->get();

        return response()->json([
                                'data' => $serviceSection ?? [],
                                'success' => true,
                                ], 200);
                            
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_section_name' => ['required', 'string', Rule::unique('service_sections', 'service_section_name')->whereNull('deleted_at')],
            'service_section_slug' => ['required', 'string', Rule::unique('service_sections', 'service_section_slug')->whereNull('deleted_at')],
            'service_section_content' =>  'required|string',
            'service_section_status' => 'nullable|boolean',
            'service_section_order' => 'nullable|integer',
            'service_id' => 'integer|exists:services,service_id'
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if(ServiceSection::withTrashed(true)
                          ->where('service_section_name', $request->service_section_name)
                          ->orderByhere('service_section_slug', $request->service_section_slug)
                          ->exists())
        {

            throw new UserExistPreviouslyException('Oops! It appears that the chosen Service section Name or slug is already in use. Please select a different one and try again.');
        }

        $result = ServiceSection::create($request->all());

        if ($result) {
            
            return response()->json([
                                    'success' => true,
                                    'message' => 'Service section created successfully'
                                    ], 201);
        } else {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * this functionis used to restored deleted data.
     * 
     * @param string $name
     * @return response
     */
    public function restore(string $name)
    {
        $serviceSection = ServiceSection::withTrashed(true)->where('service_section_name', $name)->first();

        if ($serviceSection) {
            
            $serviceSection->restore();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Service Section restored successfully'
                                    ], 202);
        } else {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later',
                                    ],422);
        }
    }

    /**
     * Display the specified resource.
     * 
     * @param string $id
     * @return Response
     */
    public function show(string $id)
    {
        $serviceSection = ServiceSection::find($id);

        if ($serviceSection) {
            
            return response()->json([
                                    'data' => $serviceSection,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {
            
            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Service Section not found'
                                    ], 404);
        }
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
     * 
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $serviceSection = ServiceSection::findOrFail($id);

        if (!$serviceSection) {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Service Section not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'service_section_name' => ['required', 'string', Rule::unique('service_sections', 'service_section_name')->ignore($id, 'service_section_id')->whereNull('deleted_at')],
            'service_section_slug' => ['required', 'string', Rule::unique('service_sections', 'service_section_slug')->ignore($id, 'service_section_id')->whereNull('deleted_at')],
            'service_section_content' => 'required|string',
            'service_section_status' => 'nullable|boolean',
            'service_section_order' => 'nullable|integer',
            'service_id' => 'integer|exists:services,service_id'
        ]);

        // Check for validation failure
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        try {

            // Update the attributes
            $serviceSection->update($request->all());

            return response()->json([
                                    'success' => true,
                                    'message' => 'Service Section updated successfully'
                                    ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param string $id
     * @return Response
     */
    public function destroy(string $id)
    {
        $serviceSection = ServiceSection::find($id);

        if (!$serviceSection) {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Service Section not found'
                                    ], 404);
        }else if($serviceSection){

            $serviceSection->delete();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Service Section deleted successfully'
                                    ], 200);
        }else{

            return response()->json([
                                    'success' => false,
                                    'message' => 'something went wrong, please try again'
                                    ], 422);
        }
    }
}
