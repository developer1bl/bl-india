<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuickLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuickLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getQuickLinks()
    {
        $quickLinks = QuickLink::all();

        return response()->json([
                                'data' => $quickLinks ?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $quickLink = QuickLink::find($id);

        if ($quickLink) {
            return response()->json([
                                    'data' => $quickLink,
                                    'success' => true
                                    ], 200);
        } else {
            return response()->json([
                                    'data' => null,
                                    'success' => false,
                                    'message' => 'Quick link not found'
                                    ], 404);
        }
    }

    /**
     * Create a new quick link.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createQuickLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quick_link_name' => 'required|string|max:255',
            'quick_link_path' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $data = [
            'quick_link_name' => $request->quick_link_name,
            'quick_link_path' => $request->quick_link_path,
        ];

        $result = QuickLink::create($data);

        if ($result) {
            return response()->json([
                                    'success' => true,
                                    'message' => 'Quick link created successfully'
                                    ], 201);
        } else {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, try again later'
                                    ], 422);
        }
    }

    /**
     * Update an existing quick link.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function updateQuickLink(Request $request, string $id)
    {
        $quickLink = QuickLink::find($id);

        if (!$quickLink) {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Quick link not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'quick_link_name' => 'required|string|max:255',
            'quick_link_path' => 'required|string|max:255',
            'quick_link_status' => 'required|boolean',
        ]);

        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $data = [
            'quick_link_name' => $request->quick_link_name,
            'quick_link_path' => $request->quick_link_path,
            'quick_link_status' => $request->quick_link_status,
        ];

        $result = $quickLink->update($data);

        if ($result) {
            return response()->json([
                                    'success' => true,
                                    'message' => 'Quick link updated successfully'
                                    ], 201);
        } else {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, try again later'
                                    ], 422);
        }
    }

    /**
     * Delete an existing quick link.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function deleteQuickLink(string $id)
    {
        $quickLink = QuickLink::find($id);

        if (!$quickLink) {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Quick link not found'
                                    ], 404);
        }

        $result = $quickLink->delete();

        if ($result) {
            return response()->json([
                                    'success' => true,
                                    'message' => 'Quick link deleted successfully'
                                    ], 200);
        } else {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, try again later'
                                    ], 422);
        }
    }
}
