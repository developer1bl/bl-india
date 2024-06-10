<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FounderVoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FounderVoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $founderVoices = FounderVoice::all();

        return response()->json([
                                'data' => $founderVoices,
                                'success' => true
                                ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'founder_voices_name' => 'required|string|max:255',
            'founder_voices' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $data = [
            'founder_voices_name' => $request->founder_voices_name,
            'founder_voices' => $request->founder_voices,
        ];

        $founderVoice = FounderVoice::create($data);

        if ($founderVoice) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Founder voice created successfully',
                                    ], 201);
        } else {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, try again later'
                                    ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $founderVoice = FounderVoice::find($id);

        if (!$founderVoice) {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Founder voice not found'
                                    ], 404);
        }

        return response()->json([
                                'data' => $founderVoice,
                                'success' => true
                                ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $founderVoice = FounderVoice::find($id);

        if (!$founderVoice) {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Founder voice not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'founder_voices_name' => 'required|string|max:255',
            'founder_voices' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $data = [
            'founder_voices_name' => $request->founder_voices_name,
            'founder_voices' => $request->founder_voices,
        ];

        $result = $founderVoice->update($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Founder voice updated successfully',
                                    ], 200);
        } else {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, try again later'
                                    ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $founderVoice = FounderVoice::find($id);

        if (!$founderVoice) {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Founder voice not found'
                                    ], 404);
        }

        if ($founderVoice->delete()) {
            return response()->json([
                                    'success' => true,
                                    'message' => 'Founder voice deleted successfully'
                                    ], 200);
        } else {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, try again later'
                                    ], 422);
        }
    }
}
