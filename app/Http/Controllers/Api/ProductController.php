<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;
use App\Models\Service;
use App\Helpers\MediaHelper;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::with(['productCategories'])
                            ->orderByDesc('product_id')
                            ->get();

        return response()->json([
                                'data' => $product ?? [],
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
        $validator = Validator::make(
            $request->all(),
            [
                'product_name' => ['required', 'string', 'max:150'],
                'product_slug' => ['required', 'string', 'max:150', Rule::unique('products', 'product_slug')->whereNull('deleted_at')],
                'product_category_id' => 'exists:product_categories,product_category_id',
                'product_image_id' => 'integer|exists:media,media_id',
                'product_technical_name' => 'nullable|string',
                'product_img_alt' => 'nullable|string',
            ]
        );

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (Product::withTrashed(true)
                    ->Where('product_slug', $request->product_slug)
                    ->exists()
        ) {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen Product slug is already in use. Please select a different one and try again');
        }

        $productImagePath = MediaHelper::getMediaPath($request->product_image_id ?? null);

        $data = [
            'product_name' => $request->product_name,
            'product_slug' => $request->product_slug,
            'product_technical_name' => $request->product_technical_name,
            'product_img_url' => $productImagePath,
            'product_img_alt' => $request->product_img_alt,
            'product_content' => $request->product_content,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
            'product_status' =>  $request->product_status ?? 1,
            'product_order' => $request->product_order,
        ];

        $product = Product::create($data);

        // attach product category with product
        $category = explode(',', $request->product_category_id);
        $category = array_map('intval', $category);
        $product->productCategories()->sync($category);

        //attach services on product
        $services = [];
        $requestService = json_decode($request->service);

        foreach ($requestService as $serviceData) {

            $serviceId = $serviceData->service_id;
            $serviceType = $serviceData->service_type;
            $complianceData = [];

            foreach ($serviceData->service_compliance as $compliance) {
                $complianceData[$compliance->name] = $compliance->value;
            }

            $services[$serviceId] = [
                'service_type' => $serviceType,
                'service_compliance' => json_encode($complianceData),
            ];
        }

        $product->productService()->sync($services);

        if ($product) {
            return response()->json([
                                    'success' => true,
                                    'message' => 'Product created successfully'
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, try again later'
                                    ], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $request
     * @return response
     */
    public function restore(String $request)
    {
        $product = Product::withTrashed(true)->where('product_slug', $request)->first();

        if ($product) {

            $product->restore();

            return response()->json([
                'success' => true,
                'message' => 'Product restored successfully'
            ], 202);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['productCategories', 'productService'])->find($id);

        if ($product) {

            return response()->json([
                'data' => $product,
                'success' => true,
                'message' => ''
            ], 200);

        } else {

            return response()->json([
                'data' => [],
                'success' => false,
                'message' => 'Product not found',
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
     */
    public function update(Request $request, string $id)
    {
        // Find the product by ID
        $product = Product::find($id);

        if (!$product) {

             return response()->json([
                                    'success' => false,
                                    'message' => 'Product not found'
                                    ], 404);
        }

        // Validate the request data
        $validator = Validator::make(
            $request->all(),
            [
                'product_name' => ['required', 'string', 'max:150'],
                'product_slug' => ['required', 'string', 'max:150', Rule::unique('products', 'product_slug')
                                                                          ->ignore($id, 'product_id')
                                                                          ->whereNull('deleted_at')],
                'product_category_id' => 'exists:product_categories,product_category_id',
                'product_image_id' => 'integer|exists:media,media_id',
                'product_technical_name' => 'nullable|string',
                'product_img_alt' => 'nullable|string',
            ]
        );

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        // Prepare the updated data
        $productImagePath = MediaHelper::getMediaPath($request->product_image_id ?? null);

        $data = [
            'product_name' => $request->product_name,
            'product_slug' => $request->product_slug,
            'product_technical_name' => $request->product_technical_name,
            'product_img_url' => $productImagePath,
            'product_img_alt' => $request->product_img_alt,
            'product_content' => $request->product_content,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
            'product_status' => $request->product_status ?? 1,
            'product_order' => $request->product_order,
        ];

        // Update the product
        $product->update($data);

        // Sync product categories
        $category = explode(',', $request->product_category_id);
        $category = array_map('intval', $category);
        $product->productCategories()->sync($category);

        // Sync product services
        $services = $complains = [];
        $requestService = json_decode($request->service);

        foreach ($requestService as $serviceData) {
            $serviceId = $serviceData->service_id;
            $serviceType = $serviceData->service_type;
            $complianceData = [];

            foreach ($serviceData->service_compliance as $compliance) {
                $complianceData[$compliance->name] = $compliance->value;
            }

            $services[$serviceId] = [
                'service_type' => $serviceType,
                'service_compliance' => json_encode($complianceData),
            ];
        }

        $product->productService()->sync($services);

        return response()->json([
                                'success' => true,
                                'message' => 'Product updated successfully'
                                ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if ($product) {

            $result = $product->delete();

            if ($result) {

                return response()->json([
                    'success' => true,
                    'message' => 'Product deleted successfully'
                ], 202);
            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong, try again later',
                ], 422);
            }
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Product not found',
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
    public function deleteSelectedProduct(Request $request)
    {
        $product_ids = explode(',', $request->input('product_ids'));

        if (!empty($product_ids)) {

            if (Product::whereIn('product_id', $product_ids)->exists()) {

                Product::whereIn('product_id', $product_ids)->delete();

                return response()->json([
                    'success' => true,
                    'message' => "All Selected Product deleted successfully",
                ], 200);
            } else {

                return response()->json([
                    'success' => false,
                    'message' => "Selected Product not found",
                ], 404);
            }
        } else {

            return response()->json([
                'success' => false,
                'message' => "No Product selected",
            ], 404);
        }
    }
}
