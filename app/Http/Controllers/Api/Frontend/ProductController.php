<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * home page work-flow section
     *
     * @param product $product
     * @return \Illuminate\Http\Response
     */
    public function getProductDetails($product){

        $product = Product::find($product);

        if ($product) {
            return response()->json(['data' => $product, 'status' => true],200);
        }else{
            return response()->json(['data' => [], 'status' => false ], 404);
        }
    }


    /**
     * home page work-flow section
     *
     * @param product $product
     * @return \Illuminate\Http\Response
     */
    public function getProductAllServices($product){
        
        $product = Product::find($product);

        if ($product) {
            return response()->json(['data' => $product->productService()->get(), 'status' => true],200);
        }else{
            return response()->json(['data' => [], 'status' => false ], 404);
        }
    }


}
