<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategories;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;

class ProductCatrgoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product_category = ProductCategories::with('products')
                                               ->orderByDesc('product_category_id')
                                               ->get();

        return response()->json([
                                'data'=> $product_category,
                                'success' => true
                                ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_category_name' => ['required', 'string', 'max:150'],
            'product_category_slug' => ['required', 'string', 'max:255', Rule::unique('product_categories', 'product_category_slug')->whereNull('deleted_at')],
            'product_category_content' => ['nullable'],
            'seo_title' => ['nullable', 'string'],
            'seo_description' => ['nullable', 'string'],
            'seo_keywords' => ['nullable', 'string'],
            'product_category_status' => ['nullable', 'boolean'],
            'product_category_order' => ['nullable', 'numeric'],
        ]);

       //if the request have some validation errors
       if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (ProductCategories::withTrashed()
                                ->Where('product_category_slug', $request->product_category_slug)
                                ->exists())
        {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen Product Category slug is already in use. Please select a different one and try again');
        }

        $product_category = ProductCategories::create($request->all());

        if ($product_category) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Product Category created successfully'
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
     *
     *  @param string $request
     *  @return Response
     */
    public function restore(string $request)
    {
        $service = ProductCategories::withTrashed(true)
                                      ->whereProduct_category_name($request)
                                      ->first();

        if ($service) {

            $service->restore();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Product category restored successfully'
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Product category not found'
                                    ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $productCategory = ProductCategories::find($id);

        if ($productCategory) {

            return response()->json([
                                    'data'=> $productCategory,
                                    'message' => '',
                                    'success' => true
                                    ],200);
        } else {

            return response()->json([
                                    'data'=> [],
                                    'message' => 'No such product category found',
                                    'success' => false
                                    ],404);
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
        $productCategory = ProductCategories::find($id);

        if (!$productCategory) {

            return response()->json([
                                    'message' => 'No such product category found',
                                    'success' => false
                                    ],404);
        }

        $validator = Validator::make($request->all(),[
            'product_category_name' => ['required', 'string', 'max:150'],
            'product_category_slug' => ['required', 'string', 'max:255', Rule::unique('product_categories', 'product_category_slug')->ignore($id, 'product_category_id')],
            'product_category_content' => ['nullable'],
            'seo_title' => ['nullable', 'string'],
            'seo_description' => ['nullable', 'string'],
            'seo_keywords' => ['nullable', 'string'],
            'product_category_status' => ['nullable', 'boolean'],
            'product_category_order' => ['nullable', 'numeric'],
        ]);

         //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if ($productCategory) {

            $productCategory->update($request->all());

            return response()->json([
                                    'success' => true,
                                    'message' => 'Product Category updated successfully'
                                    ], 201);
        }else{

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $productCategory = ProductCategories::find($id);


        if ($productCategory) {

            $productCategory->delete();

            return response()->json([
                                   'success' => true,
                                   'message' => 'Product Category deleted successfully'
                                    ], 202);

        } elseif (!$productCategory) {

            return response()->json([
                                   'message' => 'No such product category found',
                                   'success' => false
                                    ],404);
        } else {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Something went wrong, please try again later'
                                    ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return Response
     *
     **/
    public function deleteSelectedProductCategory(Request $request)
    {
        $category_ids = explode(',', $request->input('categories_ids'));

        if (!empty($category_ids)) {

            if (ProductCategories::whereIn('product_category_id', $category_ids)->exists()) {

                ProductCategories::whereIn('product_category_id', $category_ids)->delete();

                return response()->json([
                                        'success' => true,
                                        'message' => "All Selected Product Category deleted successfully",
                                        ], 200);
            } else {

                return response()->json([
                                        'success' => false,
                                        'message' => "Selected Product Category not found",
                                        ], 404);
            }
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => "No Product Category selected",
                                    ], 404);
        }
    }
}
