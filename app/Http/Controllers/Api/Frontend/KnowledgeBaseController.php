<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeBaseCategory;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function viewAllCategories(){

        $categories = KnowledgeBaseCategory::with('knowledgeBases')->get();

        return response()->json(['data' => $categories], 200);
    }

    public function viewSingleCategory($id){

        $category = KnowledgeBaseCategory::with('knowledgeBases')->find($id);

        if ($category) {

            return response()->json(['data' => $category], 200);
        } else {

            return response()->json(['data' => [], 'message' => 'no Knowledge base category found'], 404);
        }
    }

    public function viewAllKnowledgeBase(){

        $knowledgeBase = KnowledgeBase::with('KnowledgeBaseCategory')->get();

        return response()->json(['data' => $knowledgeBase], 200);
    }

    public function viewSingleKnowledgeBase($id){

        $knowledgeBase = KnowledgeBase::with('KnowledgeBaseCategory')->find($id);

        if ($knowledgeBase) {

            return response()->json(['data' => $knowledgeBase], 200);
        }
        else {

            return response()->json(['data' => [], 'message' => 'no Knowledge base found'], 404);
        }
    }
}
