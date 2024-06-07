<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\UserExistPreviouslyException;
use App\Helpers\MediaHelper;
use Illuminate\Validation\Rule;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serviceCategory = ServiceCategory::with('services')->OrderByDesc('id')->get();

        return response()->json([
                                'data' => $serviceCategory ?? [],
                                'success' => true
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
            'service_category_name' => 'required|string|max:255',
            'service_category_slug' => 'required|string|max:255|unique:service_categories',
            'category_img_id' => 'nullable|integer|exists:media,media_id',
            'category_img_alt' => 'nullable|string|max:255',
            'service_category_type' => 'boolean'
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (ServiceCategory::withTrashed(true)
            ->where('service_category_slug', $request->service_category_slug)
            ->first()
        ) {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen Service Category slug is already in use. Please select a different one and try again');
        }

        $categoryImagPath = MediaHelper::getMediaPath($request->category_img_id ?? null);

        $data = [
            'service_category_name' => $request->service_category_name,
            'service_category_slug' => $request->service_category_slug,
            'category_img_url' => $categoryImagPath,
            'category_img_alt' => $request->category_img_alt,
            'service_category_type' => $request->service_category_type,
            'category_status' =>  true,
        ];

        $result = ServiceCategory::create($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Service Category created successfully'
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $slug
     */
    public function restore(string $slug)
    {
        $serviceCategory = ServiceCategory::withTrashed()
                                            ->where('service_category_slug', $slug)
                                            ->restore();

        if ($serviceCategory) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Service Category restored successfully'
                                    ], 201);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string id
     * @return response
     */
    public function show(string $id)
    {
        $serviceCategory = ServiceCategory::find($id);

        if ($serviceCategory) {

            return response()->json([
                                    'data' => $serviceCategory,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Service Category not found'
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
    public function update(Request $request, string $id)
    {
        $serviceCategory = ServiceCategory::find($id);

        if (!$serviceCategory) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Service Category not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'service_category_name' => 'required|string|max:255',
            'service_category_slug' => ['required', 'string', 'max:255',  Rule::unique('service_categories', 'service_category_slug')
                ->ignore($id)
                ->whereNull('deleted_at')],
            'category_img_id' => 'nullable|integer|exists:media,media_id',
            'category_img_alt' => 'nullable|string|max:255',
            'service_category_type' => 'boolean'
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 403);
        }

        $categoryImagPath = MediaHelper::getMediaPath($request->category_img_id ?? null);

        $data = [
            'service_category_name' => $request->service_category_name,
            'service_category_slug' => $request->service_category_slug,
            'category_img_url' => $categoryImagPath,
            'category_img_alt' => $request->category_img_alt,
            'category_status' => $request->category_status,
            'service_category_type' => $request->service_category_type
        ];

        $result = $serviceCategory->update($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Service Category updated successfully'
                                    ], 201);
        } else {

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
        $serviceCategory = ServiceCategory::find($id);

        if ($serviceCategory) {

            $serviceCategory->delete();
            return response()->json([
                                    'success' => true,
                                    'message' => 'Service Category deleted successfully'
                                    ], 201);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Service Category not found'
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
    public function deleteSelectedServiceCategory(Request $request)
    {
        $category_ids = explode(',', $request->input('category_ids'));

        if (!empty($category_ids)) {

            if (ServiceCategory::whereIn('id', $category_ids)->exists()) {

                ServiceCategory::whereIn('id', $category_ids)->delete();

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
