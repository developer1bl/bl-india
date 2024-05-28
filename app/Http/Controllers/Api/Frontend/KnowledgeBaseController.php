<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeBaseCategory;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function viewAllCategories(){

        $categories = KnowledgeBaseCategory::all();

        return response()->json(['data' => $categories], 200);
    }

    public function viewSingleCategory($id){

        $category = KnowledgeBaseCategory::find($id);

        if ($category) {

            return response()->json(['data' => $category], 200);
        } else {

            return response()->json(['data' => [], 'message' => 'no Knowledge base category found'], 404);
        }
    }

    public function viewAllKnowledgeBase(){

        $knowledgeBase = KnowledgeBase::all();

        return response()->json(['data' => $knowledgeBase], 200);
    }

    public function viewSingleKnowledgeBase($id){

        $knowledgeBase = KnowledgeBase::find($id);

        if ($knowledgeBase) {

            return response()->json(['data' => $knowledgeBase], 200);
        }
        else {

            return response()->json(['data' => [], 'message' => 'no Knowledge base found'], 404);
        }
    }
}
