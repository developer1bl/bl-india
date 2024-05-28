<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KnowledgeBaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $knowledgeBase = KnowledgeBase::all();

        return response()->json(['data' => $knowledgeBase ?? []], 200);
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
            'category_id' => ['required', 'exists:knowledgebase_categories,knowledgebase_category_id', 'numeric'],
            'knowledge_bases_question' => ['required', 'string'],
            'knowledge_bases_answer' => ['required', 'string']
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 403);
        }

        $knowledgeBase = KnowledgeBase::create([
            'category_id' => $request->category_id,
            'knowledge_bases_question' => $request->knowledge_bases_question,
            'knowledge_bases_answer' => $request->knowledge_bases_answer
        ]);

        return response()->json(['success' => true, 'message' => 'knowledge base create successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $knowledgeBase = KnowledgeBase::find($id);

        if (!$knowledgeBase) {
            return response()->json(['error' => 'knowledge base not found'], 404);
        }

        return response()->json(['data' => $knowledgeBase], 200);
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
        $knowledgeBase = KnowledgeBase::find($id);

        if (!$knowledgeBase) {
            return response()->json(['error' => 'knowledge base not found'], 404);
        }

        $validate = Validator::make($request->all(), [
            'category_id' => ['required', 'exists:knowledgebase_categories,knowledgebase_category_id', 'numeric'],
            'knowledge_bases_question' => ['required', 'string'],
            'knowledge_bases_answer' => ['required', 'string']
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 403);
        }

        $data = [
            'category_id' => $request->category_id,
            'knowledge_bases_question' => $request->knowledge_bases_question,
            'knowledge_bases_answer' => $request->knowledge_bases_answer
        ];

        $knowledgeBase->update($data);

        return response()->json(['success' => true, 'message' => 'knowledge base updated successfully'], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $knowledgeBase = KnowledgeBase::find($id);

        if (!$knowledgeBase) {
            return response()->json(['error' => 'knowledge base not found'], 404);
        }

        $knowledgeBase->delete();

        return response()->json(['success' => true,'message' => 'knowledge base deleted successfully'], 200);
    }
}
