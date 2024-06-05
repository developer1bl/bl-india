<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBaseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KnowledgebaseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $k_Base = KnowledgeBaseCategory::all();
        return response()->json(['data' => $k_Base], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'knowledgebase_category_name' => ['required', 'string'],
            'knowledgebase_category_slug' => ['required', 'string',
                                             Rule::unique('knowledgebase_categories', 'knowledgebase_category_slug')
                                                    ->whereNull('deleted_at')],
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 403);
        }

        $data =[
            'knowledgebase_category_name' => $request->knowledgebase_category_name,
            'knowledgebase_category_slug' => $request->knowledgebase_category_slug,
        ];

        $result = KnowledgeBaseCategory::create($data);

        return response()->json(['success' => true, 'message'=> 'Knowledge base category created successfully'], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $k_b_Category = KnowledgeBaseCategory::find($id);

        if (!$k_b_Category) {
            return response()->json(['error' => 'Knowledge base category not found'], 404);
        }

        return response()->json(['data' => $k_b_Category], 200);
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
        $k_b_Category = KnowledgeBaseCategory::find($id);

        if (!$k_b_Category) {
            return response()->json(['error' => 'Knowledge base category not found'], 404);
        }

        $validate = Validator::make($request->all(), [
            'knowledgebase_category_name' => ['required', 'string'],
            'knowledgebase_category_slug' => ['required', 'string',
                                             Rule::unique('knowledgebase_categories', 'knowledgebase_category_slug')
                                                    ->ignore($id, 'knowledgebase_category_id')
                                                    ->whereNull('deleted_at')],
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 403);
        }

        $data =[
            'knowledgebase_category_name' => $request->knowledgebase_category_name,
            'knowledgebase_category_slug' => $request->knowledgebase_category_slug,
        ];

        $result = $k_b_Category->update($data);

        return response()->json(['success' => true, 'message'=> 'Knowledge base category updated successfully'], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $k_b_Category = KnowledgeBaseCategory::find($id);

        if (!$k_b_Category) {
            return response()->json(['error' => 'Knowledge base category not found'], 404);
        }

        $result = $k_b_Category->delete();

        return response()->json(['success' => true, 'message'=> 'Knowledge base category deleted successfully'], 200);
    }
}
