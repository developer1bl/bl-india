<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

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
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
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
                                       'message' => 'Something went wrong',
                                        ],500);
            }
            
        } else {
            
            return response()->json([
                                  'success' => false,
                                  'message' => 'Product not found',
                                    ],404);
        }
    }
}
