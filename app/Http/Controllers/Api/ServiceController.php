<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;
use App\Helpers\MediaHelper;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service = Service::with(['notices', 'service_section', 'service_category'])
                            ->orderByDesc('service_id')
                            ->get();

        return response()->json([
                                'success' => true,
                                'data' => $service ?? []
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_name' => ['required', 'string', 'max:150', Rule::unique('services', 'service_name')->whereNull('deleted_at')],
            'service_slug' => ['required', 'string', 'max:255', Rule::unique('services', 'service_slug')->whereNull('deleted_at')],
            'service_image_id' => 'required|exists:media,media_id',
            'service_category_id' => 'required|exists:service_categories,id',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (Service::withTrashed(true)
                    ->whereService_name($request->service_name)
                    ->whereService_slug($request->service_slug)
                    ->exists()
        ) {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen Service Name or slug is already in use. Please select a different one and try again.');
        }

        $compliance = explode(',', $request->service_compliance);

        $PageImagePath = MediaHelper::getMediaPath($request->service_image_id ?? null);

        $data = [
            'service_name' => $request->service_name,
            'service_slug' => $request->service_slug,
            'service_category_id' => $request->service_category_id,
            'service_img_url' => $PageImagePath,
            'service_img_alt' => $request->service_img_alt,
            'service_compliance' => $compliance,
            'service_description' => $request->service_description,
            'faqs' => $request->faq,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
            'service_featured' => $request->service_featured,
            'service_order' => $request->service_order,
            'service_status' => $request->service_status,
        ];

        try {
            $result = Service::create($data);

            if ($result) {

                return response()->json([
                                        'success' => true,
                                        'message' => 'Service created successfully'
                                        ], 201);
            } else {

                return response()->json([
                                        'success' => false,
                                        'message' => 'Something went wrong, please try again later'
                                        ], 422);
            }
        } catch (\Throwable $th) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $request
     * @return response
     */
    public function restore(string $request)
    {
        $service = Service::withTrashed(true)
            ->whereService_name($request)
            ->first();

        if ($service) {

            $service->restore();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Service restored successfully'
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Service not found'
                                    ], 404);
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
        // $service = Service::with(['notices', 'service_section'])->find($id);
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $id, Request $request
     * @return Response
     */
    public function update(Request $request,  $id)
    {
        $service  = Service::find($id);

        if (!$service) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Service not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'service_name' => ['required', 'string', 'max:150', Rule::unique('services', 'service_name')->ignore($id, 'service_id')],
            'service_slug' => ['required', 'string', 'max:255', Rule::unique('services', 'service_slug')->ignore($id, 'service_id')],
            'service_image_id' => 'required|exists:media,media_id',
            'service_category_id' => 'required|exists:service_categories,id',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $serviceImagePath = MediaHelper::getMediaPath($request->service_image_id ?? null);

        $data = [
            'service_name' => $request->service_name,
            'service_slug' => $request->service_slug,
            'service_category_id' => $request->service_category_id,
            'service_img_url' => $serviceImagePath,
            'service_img_alt' => $request->service_img_alt,
            'service_compliance' => $request->service_compliance,
            'service_description' => $request->service_description,
            'faqs' => $request->faq,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
            'service_featured' => $request->service_featured,
            'service_order' => $request->service_order,
            'service_status' => $request->service_status,
        ];

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
                                        'message' => 'Something went wrong, please try again later'
                                        ], 422);
            }
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Service not found'
                                    ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return response
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

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return Response
     *
     **/
    public function deleteSelectedService(Request $request)
    {
        $service_ids = explode(',', $request->input('service_ids'));

        if (!empty($service_ids)) {

            if (Service::whereIn('service_id', $service_ids)->exists()) {

                Service::whereIn('service_id', $service_ids)->delete();

                return response()->json([
                                        'success' => true,
                                        'message' => "All Selected Service Category deleted successfully",
                                        ], 200);
            } else {

                return response()->json([
                                        'success' => false,
                                        'message' => "Selected Service Category not found",
                                        ], 404);
            }
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => "No Service Category selected",
                                    ], 404);
        }
    }
}
