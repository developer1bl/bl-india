<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::All();

        return response()->json([
                                'data'=> $product ?? [],
                                'success' => true
                                ],200);
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
            'product_name' => ['required','string','max:150', Rule::unique('products', 'product_name')->whereNull('deleted_at')],
            'product_slug' => ['required','string','max:150', Rule::unique('products', 'product_slug')->whereNull('deleted_at')],
        ]);
        
        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }
        
        $data = [
            'product_name' => $request->product_name,
            'product_slug' => $request->product_slug,
            'product_technical_name' => $request->product_technical_name,
            'product_service_id' => $request->product_service_id,
            'product_category_id' => $request->product_category_id,
            'product_image_id' => $request->media_id,
            'product_img_alt' => $request->img_alt,
            'product_compliance' => $request->product_compliance,
            'product_content' => $request->product_content,
            'product_information' => $request->product_information,
            'product_guidelines' => $request->product_guidelines,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
            'product_status' => 1,
            'product_order' => 0,
        ];

        try {

            $result = Product::create($data);

            if ($result) {
                return response()->json([
                                    'success' => true,
                                    'message' => 'Product created successfully'
                                        ], 200);
            }else{

                return response()->json([
                                'success' => false,
                                'message' => 'Something went wrong, try again later'
                                        ], 422);
            }
            
        } catch (\Exception $th) {

            throw new UserExistPreviouslyException('this product was deleted previously, did you want to restore it?');
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
        $product = Product::withTrashed(true)->where('product_name', $request)->first();   
       
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
        $product = Product::find($id);

        if ($product) {

            return response()->json([
                                    'data' => $product,
                                    'success' => true,
                                    'message' =>''
                                    ],200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Product not found',
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
        $validator = Validator::make($request->all(), [
            'product_name' => ['required','string','max:150', Rule::unique('products', 'product_name')->ignore($id, 'product_id')],
            'product_slug' => ['required','string','max:150', Rule::unique('products', 'product_slug')->ignore($id, 'product_id')],
        ]);
        
        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $product = Product::find($id);

        if ($product) {
            
            $data = [
                'product_name' => $request->product_name,
                'product_slug' => $request->product_slug,
                'product_technical_name' => $request->product_technical_name,
                'product_service_id' => $request->product_service_id,
                'product_category_id' => $request->product_category_id,
                'product_image_id' => $request->media_id,
                'product_img_alt' => $request->img_alt,
                'product_compliance' => $request->product_compliance,
                'product_content' => $request->product_content,
                'product_information' => $request->product_information,
                'product_guidelines' => $request->product_guidelines,
                'seo_title' => $request->seo_title,
                'seo_description' => $request->seo_description,
                'seo_keywords' => $request->seo_keywords,
                'product_status' => $request->product_status,
                'product_order' => $request->product_order,
            ];
            
            $result = $product->update($data);

            if ($result) {
                
                return response()->json([
                                        'sucess' => true, 
                                        'message' => 'product update sucessfully '
                                        ], 201);
            } else {
                
                return response()->json([
                                     'success' => false,
                                     'message' => 'Something went wrong, try again later'
                                        ], 422);
            }
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Product not found',
                                    ],404);   
        }
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
                                        ],202);
            } else {
                
                return response()->json([
                                       'success' => false,
                                       'message' => 'Something went wrong, try again later',
                                        ],422);
            }
            
        } else {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Product not found',
                                    ],404);
        }
    }
}
